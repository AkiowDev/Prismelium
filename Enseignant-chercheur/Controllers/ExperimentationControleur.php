<?php
declare(strict_types=1);

namespace Prismelium\Controllers;

use DateTimeImmutable;
use PDOException;
use Prismelium\Controleur;
use Prismelium\GardeEnseignantChercheur;
use Prismelium\Models\ExperimentationModele;
use RuntimeException;

final class ExperimentationControleur extends Controleur
{
    private const FIXED_RESTRICTIONS = [
        'Age minimum', 'Francais', 'Anglais', 'Femme', 'Homme',
        'Sans lunettes', 'Avec lunettes',
    ];

    public function index(): void
    {
        $researcher = GardeEnseignantChercheur::requireResearcher();
        $model = new ExperimentationModele();
        $archiveView = is_string($_REQUEST['archive_view'] ?? null)
            && in_array($_REQUEST['archive_view'], ['hide', 'show'], true)
                ? $_REQUEST['archive_view']
                : 'hide';
        $search = trim(is_string($_REQUEST['q'] ?? null) ? $_REQUEST['q'] : '');
        $error = '';
        $selectedIds = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? null;
            if (!\csrf_is_valid($_POST['csrf_token'] ?? null)) {
                http_response_code(400);
                $error = 'La requête a expiré. Rechargez la page et réessayez.';
            } elseif (!is_string($action) || !in_array($action, ['archive', 'delete'], true)) {
                http_response_code(400);
                $error = 'L’action demandée est invalide.';
            } elseif (!is_array($_POST['selected_experiments'] ?? null)
                || $_POST['selected_experiments'] === []
                || count($_POST['selected_experiments']) > 100) {
                $error = 'Sélectionnez entre 1 et 100 expérimentations.';
            } else {
                foreach ($_POST['selected_experiments'] as $rawId) {
                    $id = (is_string($rawId) || is_int($rawId))
                        ? filter_var($rawId, FILTER_VALIDATE_INT)
                        : false;
                    if ($id === false || $id < 1) {
                        $selectedIds = [];
                        break;
                    }
                    $selectedIds[] = (int) $id;
                }
                $selectedIds = array_values(array_unique($selectedIds));
                if ($selectedIds === []) {
                    $error = 'La sélection des expérimentations est invalide.';
                } else {
                    try {
                        $cleanupFailed = $model->archiveOrDelete(
                            (int) $researcher['Id_Equipe'],
                            (int) $researcher['Id_User'],
                            $selectedIds,
                            $action
                        );
                        $params = ['archive_view' => $archiveView, 'notice' => $action === 'archive' ? 'archived' : 'deleted'];
                        if ($search !== '') {
                            $params['q'] = $search;
                        }
                        if ($cleanupFailed) {
                            $params['document_cleanup_failed'] = '1';
                        }
                        $this->redirect('index.php?page=experimentations&' . http_build_query($params));
                    } catch (PDOException $exception) {
                        error_log('Experiment management action failed: ' . $exception->getMessage());
                        $error = 'L’action n’a pas pu être effectuée. Réessayez.';
                    } catch (RuntimeException $exception) {
                        $error = $exception->getMessage();
                    }
                }
            }
        }

        $this->render('experimentations/liste', [
            'archiveView' => $archiveView,
            'searchTerm' => $search,
            'actionError' => $error,
            'selectedIds' => $selectedIds,
            'experiments' => $model->listForTeam((int) $researcher['Id_Equipe'], $search, $archiveView),
            'csrfToken' => \csrf_token(),
        ]);
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(): void
    {
        $rawId = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['experiment_id'] ?? null) : ($_GET['id'] ?? null);
        $id = (is_string($rawId) || is_int($rawId)) ? filter_var($rawId, FILTER_VALIDATE_INT) : false;
        if ($id === false || $id < 1) {
            http_response_code(404);
            exit('Expérimentation introuvable.');
        }
        $this->form((int) $id);
    }

    private function form(?int $experimentId): void
    {
        $researcher = GardeEnseignantChercheur::requireResearcher();
        $model = new ExperimentationModele();
        $editingExperiment = null;
        $existingLimits = [];
        $existingDocuments = [];
        $formError = '';
        $formData = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];
        $documentsToDelete = [];

        if ($experimentId !== null) {
            $editingExperiment = $model->findForTeam($experimentId, (int) $researcher['Id_Equipe']);
            if ($editingExperiment === null) {
                http_response_code(404);
                exit('Expérimentation introuvable pour votre équipe.');
            }
            if ($editingExperiment['Statut_Exp'] === 'Archivée') {
                http_response_code(403);
                exit('Une expérimentation archivée ne peut pas être modifiée.');
            }
            $existingLimits = $model->getLimits($experimentId);
            $existingDocuments = $model->getDocuments($experimentId);

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $reward = (string) ($editingExperiment['Type_Retribution'] ?? '');
                $formData = [
                    'titre' => $editingExperiment['Nom_Exp'],
                    'description' => $editingExperiment['Desc_Exp'] ?? '',
                    'date_debut' => substr((string) $editingExperiment['Date_Debut_Exp'], 0, 10),
                    'date_fin' => substr((string) $editingExperiment['Date_Fin_Exp'], 0, 10),
                    'duree' => $editingExperiment['Duree_Exp_Min'] ?? '',
                    'nombre_sessions' => $editingExperiment['Nb_Sessions_Prevues'] ?? '',
                    'retribution' => $reward === '' ? 'non' : 'oui',
                    'type_retribution' => $reward === 'Financière'
                        ? 'financiere'
                        : ($reward === 'Autre' ? 'autre' : ($reward !== '' ? 'valeur_existante' : '')),
                    'age_minimum' => $existingLimits['Age minimum']['minimum'] ?? '',
                    'genre' => isset($existingLimits['Homme'])
                        ? 'homme'
                        : (isset($existingLimits['Femme']) ? 'femme' : 'indifferent'),
                    'langue' => isset($existingLimits['Francais'])
                        ? 'francais'
                        : (isset($existingLimits['Anglais']) ? 'anglais' : ''),
                    'lunettes' => isset($existingLimits['Avec lunettes'])
                        ? 'oui'
                        : (isset($existingLimits['Sans lunettes']) ? 'non' : ''),
                    'restriction_personnalisee' => array_values(array_diff(
                        array_keys($existingLimits),
                        self::FIXED_RESTRICTIONS
                    )),
                ];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\csrf_is_valid($_POST['csrf_token'] ?? null)) {
                http_response_code(400);
                $formError = 'La requête a expiré. Rechargez la page et réessayez.';
            } else {
                $formError = $this->validateAndSave(
                    $model,
                    $researcher,
                    $editingExperiment,
                    $experimentId,
                    $existingLimits,
                    $existingDocuments,
                    $formData,
                    $documentsToDelete
                );
            }
        }

        $selectedExistingCount = count($documentsToDelete);
        $this->render('experimentations/formulaire', [
            'isEditing' => $experimentId !== null,
            'experimentId' => $experimentId,
            'editingExperiment' => $editingExperiment,
            'existingDocuments' => $existingDocuments,
            'documentsToDelete' => $documentsToDelete,
            'remainingExistingDocumentCount' => max(0, count($existingDocuments) - $selectedExistingCount),
            'formData' => $formData,
            'formError' => $formError,
            'csrfToken' => \csrf_token(),
        ]);
    }

    private function validateAndSave(
        ExperimentationModele $model,
        array $researcher,
        ?array $editingExperiment,
        ?int $experimentId,
        array $existingLimits,
        array $existingDocuments,
        array $formData,
        array &$documentsToDelete
    ): string {
        $text = static fn (string $key): string => is_string($formData[$key] ?? null) ? $formData[$key] : '';
        $title = trim($text('titre'));
        $description = trim($text('description'));
        $startDate = $text('date_debut');
        $endDate = $text('date_fin');
        $duration = filter_var($formData['duree'] ?? null, FILTER_VALIDATE_INT);
        $sessionCount = filter_var($formData['nombre_sessions'] ?? null, FILTER_VALIDATE_INT);
        $reward = $text('retribution');
        $rewardType = $text('type_retribution');
        $error = '';

        if (array_key_exists('description', $formData) && !is_string($formData['description'])) {
            $error = 'La description est invalide.';
        } elseif ($title === '' || strlen($title) > 100) {
            $error = 'Le titre est obligatoire et ne peut pas dépasser 100 caractères.';
        } elseif (strlen($description) > 1000) {
            $error = 'La description ne peut pas dépasser 1000 caractères.';
        } elseif (!$this->validDate($startDate) || !$this->validDate($endDate) || $endDate < $startDate) {
            $error = 'Les dates de début et de fin sont invalides.';
        } elseif ($duration === false || $duration < 1 || $duration > 1439) {
            $error = 'La durée doit être comprise entre 1 et 1439 minutes.';
        } elseif ($sessionCount === false || $sessionCount < 1 || $sessionCount > 1000) {
            $error = 'Le nombre de séances prévues doit être compris entre 1 et 1000.';
        } elseif (!in_array($reward, ['oui', 'non'], true)) {
            $error = 'Indiquez si l’expérimentation propose une rétribution.';
        } elseif ($reward === 'oui'
            && !in_array($rewardType, ['financiere', 'autre'], true)
            && !($experimentId !== null && $rewardType === 'valeur_existante' && !empty($editingExperiment['Type_Retribution']))) {
            $error = 'Choisissez le type de rétribution.';
        } elseif (!is_array($formData['restriction_personnalisee'] ?? [])) {
            $error = 'Les restrictions personnalisées sont invalides.';
        }

        $documentMap = [];
        foreach ($existingDocuments as $document) {
            $documentMap[(int) $document['Id_Doc']] = $document;
        }
        if ($error === '' && isset($formData['documents_a_supprimer'])) {
            if (!is_array($formData['documents_a_supprimer'])) {
                $error = 'La sélection des documents à supprimer est invalide.';
            } else {
                foreach ($formData['documents_a_supprimer'] as $rawId) {
                    $id = (is_string($rawId) || is_int($rawId)) ? filter_var($rawId, FILTER_VALIDATE_INT) : false;
                    if ($id === false || $id < 1 || !isset($documentMap[$id])) {
                        $error = 'Un document sélectionné n’est plus associé à cette expérimentation.';
                        break;
                    }
                    $documentsToDelete[] = (int) $id;
                }
            }
        }
        $documentsToDelete = array_values(array_unique($documentsToDelete));

        $uploads = [];
        if ($error === '') {
            $resourceFiles = $_FILES['ressources_numeriques'] ?? null;
            if (is_array($resourceFiles) && is_array($resourceFiles['name'] ?? null)) {
                foreach ($resourceFiles['name'] as $index => $name) {
                    $file = [
                        'error' => $resourceFiles['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                        'name' => $name,
                        'tmp_name' => $resourceFiles['tmp_name'][$index] ?? '',
                    ];
                    $upload = $this->validateUpload($file, (string) $name, $error);
                    if ($upload !== null) {
                        $uploads[] = $upload;
                    }
                    if ($error !== '') {
                        break;
                    }
                }
            }
            if ($error === '' && isset($_FILES['fiche_consentement'])) {
                $upload = $this->validateUpload($_FILES['fiche_consentement'], 'fiche de consentement', $error);
                if ($upload !== null) {
                    $uploads[] = $upload;
                }
            }
        }

        $limits = [];
        if ($error === '' && $text('age_minimum') !== '') {
            $age = filter_var($formData['age_minimum'], FILTER_VALIDATE_INT);
            if ($age === false || $age < 0 || $age > 120) {
                $error = 'L’âge minimum doit être compris entre 0 et 120 ans.';
            } else {
                $limits[] = [
                    'name' => 'Age minimum',
                    'minimum' => $age,
                    'maximum' => $existingLimits['Age minimum']['maximum'] ?? null,
                ];
            }
        }
        if ($error === '') {
            foreach ([
                'genre' => ['homme' => 'Homme', 'femme' => 'Femme'],
                'langue' => ['francais' => 'Francais', 'anglais' => 'Anglais'],
                'lunettes' => ['non' => 'Sans lunettes', 'oui' => 'Avec lunettes'],
            ] as $field => $choices) {
                $value = $text($field);
                if ($value === '' || ($field === 'genre' && $value === 'indifferent')) {
                    continue;
                }
                if (!isset($choices[$value])) {
                    $error = 'Une restriction sélectionnée est invalide.';
                    break;
                }
                $name = $choices[$value];
                $limits[] = [
                    'name' => $name,
                    'minimum' => $existingLimits[$name]['minimum'] ?? null,
                    'maximum' => $existingLimits[$name]['maximum'] ?? null,
                ];
            }
        }
        if ($error === '') {
            foreach ($formData['restriction_personnalisee'] ?? [] as $restriction) {
                if (!is_string($restriction)) {
                    $error = 'Une restriction personnalisée est invalide.';
                    break;
                }
                $restriction = trim($restriction);
                if ($restriction === '') {
                    continue;
                }
                if (strlen($restriction) > 50) {
                    $error = 'Chaque restriction personnalisée ne peut pas dépasser 50 caractères.';
                    break;
                }
                $limits[] = [
                    'name' => $restriction,
                    'minimum' => $existingLimits[$restriction]['minimum'] ?? null,
                    'maximum' => $existingLimits[$restriction]['maximum'] ?? null,
                ];
            }
        }

        if ($error !== '') {
            return $error;
        }
        $uniqueLimits = [];
        foreach ($limits as $limit) {
            if (!isset($uniqueLimits[$limit['name']]) || $limit['minimum'] !== null) {
                $uniqueLimits[$limit['name']] = $limit;
            }
        }
        $databaseReward = $reward === 'non'
            ? null
            : ($rewardType === 'financiere'
                ? 'Financière'
                : ($rewardType === 'autre'
                    ? 'Autre'
                    : (string) $editingExperiment['Type_Retribution']));

        try {
            $cleanupFailed = $model->save(
                (int) $researcher['Id_Equipe'],
                (int) $researcher['Id_User'],
                [
                    'title' => $title,
                    'description' => $description,
                    'date_start' => $startDate,
                    'date_end' => $endDate,
                    'duration' => (int) $duration,
                    'session_count' => (int) $sessionCount,
                    'reward_type' => $databaseReward,
                    'limits' => array_values($uniqueLimits),
                    'uploads' => $uploads,
                    'documents_to_delete' => $documentsToDelete,
                ],
                $experimentId
            );
            $target = 'index.php?page=experimentations&'
                . ($experimentId === null ? 'created=1' : 'updated=1');
            if ($cleanupFailed) {
                $target .= '&document_cleanup_failed=1';
            }
            $this->redirect($target);
        } catch (PDOException $exception) {
            error_log(($experimentId === null ? 'Experiment creation' : 'Experiment update')
                . ' failed: ' . $exception->getMessage());
            return 'L’expérimentation n’a pas pu être enregistrée. Vérifiez les informations et réessayez.';
        } catch (RuntimeException $exception) {
            return $exception->getMessage();
        }

        return '';
    }

    private function validDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();

        return $date !== false
            && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
            && $date->format('Y-m-d') === $value;
    }

    private function validateUpload(array $file, string $label, string &$error): ?array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? null) !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
            $error = "Le fichier « {$label} » n’a pas pu être reçu.";
            return null;
        }
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $allowedTypes = [
            'csv' => ['text/plain', 'text/csv', 'application/csv'],
            'doc' => ['application/msword', 'application/x-ole-storage'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
            'jpeg' => ['image/jpeg'], 'jpg' => ['image/jpeg'], 'mp4' => ['video/mp4'],
            'pdf' => ['application/pdf'], 'png' => ['image/png'],
            'ppt' => ['application/vnd.ms-powerpoint', 'application/x-ole-storage'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
            'txt' => ['text/plain'], 'xls' => ['application/vnd.ms-excel', 'application/x-ole-storage'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        ];
        if (!isset($allowedTypes[$extension])) {
            $error = "Le type du fichier « {$label} » n’est pas pris en charge.";
            return null;
        }
        $size = filesize((string) $file['tmp_name']);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        if ($size === false || $size > 20 * 1024 * 1024) {
            $error = "Le fichier « {$label} » dépasse la taille maximale de 20 Mo.";
            return null;
        }
        if (!in_array($mime, $allowedTypes[$extension], true)) {
            $error = "Le contenu du fichier « {$label} » ne correspond pas à son extension.";
            return null;
        }

        return [
            'tmp_path' => (string) $file['tmp_name'],
            'name' => basename((string) $file['name']),
            'extension' => $extension,
            'size' => $size,
        ];
    }
}
