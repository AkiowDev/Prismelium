<?php
declare(strict_types=1);

if (!isset($formMode) || !in_array($formMode, ['create', 'edit'], true)) {
    http_response_code(404);
    exit;
}

$isEditing = $formMode === 'edit';

require_once __DIR__ . '/../BDD_control/current_researcher.php';

$formData = $_POST;
$formError = '';
$editingExperiment = null;
$existingLimitsByName = [];
$existingDocuments = [];
$existingDocumentsById = [];
$documentsToDelete = [];
$invalidDocumentDeletionInput = false;
$experimentId = null;

if ($isEditing) {
    $rawExperimentId = $_SERVER['REQUEST_METHOD'] === 'POST'
        ? ($_POST['experiment_id'] ?? null)
        : ($_GET['id'] ?? null);
    $experimentId = is_string($rawExperimentId) || is_int($rawExperimentId)
        ? filter_var($rawExperimentId, FILTER_VALIDATE_INT)
        : false;

    if ($experimentId === false || $experimentId < 1) {
        http_response_code(404);
        exit('Expérimentation introuvable.');
    }

    $experimentQuery = $pdo->prepare(
        'SELECT Id_Exp, Nom_Exp, Desc_Exp, Date_Debut_Exp, Date_Fin_Exp,
                Duree_Exp_Min, Nb_Sessions_Prevues, Type_Retribution, Statut_Exp
         FROM EXPERIMENTATION
         WHERE Id_Exp = :experiment_id AND Id_Equipe = :team_id'
    );
    $experimentQuery->execute([
        'experiment_id' => $experimentId,
        'team_id' => $currentTeamId,
    ]);
    $editingExperiment = $experimentQuery->fetch();

    if (!$editingExperiment) {
        http_response_code(404);
        exit('Expérimentation introuvable pour votre équipe.');
    }
    if ($editingExperiment['Statut_Exp'] === 'Archivée') {
        http_response_code(403);
        exit('Une expérimentation archivée ne peut pas être modifiée.');
    }

    $limitsQuery = $pdo->prepare(
        'SELECT c.Nom_Contrainte, l.Val_Min, l.Val_Max
         FROM LIMITES l
         INNER JOIN CONTRAINTE c ON c.Id_Contrainte = l.Id_Contrainte
         WHERE l.Id_Exp = :experiment_id'
    );
    $limitsQuery->execute(['experiment_id' => $experimentId]);
    foreach ($limitsQuery->fetchAll() as $limit) {
        $existingLimitsByName[$limit['Nom_Contrainte']] = [
            'minimum' => $limit['Val_Min'],
            'maximum' => $limit['Val_Max'],
        ];
    }

    $documentsQuery = $pdo->prepare(
        'SELECT Id_Doc, Nom_Doc, Url_Doc
         FROM DOCUMENT
         WHERE Id_Exp = :experiment_id
         ORDER BY Id_Doc'
    );
    $documentsQuery->execute(['experiment_id' => $experimentId]);
    $existingDocuments = $documentsQuery->fetchAll();
    foreach ($existingDocuments as $document) {
        $existingDocumentsById[(int) $document['Id_Doc']] = $document;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && array_key_exists('documents_a_supprimer', $_POST)) {
        if (!is_array($_POST['documents_a_supprimer'])) {
            $invalidDocumentDeletionInput = true;
        } else {
            foreach ($_POST['documents_a_supprimer'] as $documentId) {
                if (!is_string($documentId) && !is_int($documentId)) {
                    $invalidDocumentDeletionInput = true;
                    continue;
                }
                $validatedDocumentId = filter_var($documentId, FILTER_VALIDATE_INT);
                if ($validatedDocumentId === false || $validatedDocumentId < 1) {
                    $invalidDocumentDeletionInput = true;
                    continue;
                }
                $documentsToDelete[] = $validatedDocumentId;
            }
        }
        $documentsToDelete = array_values(array_unique($documentsToDelete));
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $existingRewardType = (string) ($editingExperiment['Type_Retribution'] ?? '');
        $formData = [
            'titre' => $editingExperiment['Nom_Exp'],
            'description' => $editingExperiment['Desc_Exp'] ?? '',
            'date_debut' => substr($editingExperiment['Date_Debut_Exp'], 0, 10),
            'date_fin' => substr($editingExperiment['Date_Fin_Exp'], 0, 10),
            'duree' => $editingExperiment['Duree_Exp_Min'] ?? '',
            'nombre_sessions' => $editingExperiment['Nb_Sessions_Prevues'] ?? '',
            'retribution' => $existingRewardType === '' ? 'non' : 'oui',
            'type_retribution' => $existingRewardType === 'Financière'
                ? 'financiere'
                : ($existingRewardType === 'Autre'
                    ? 'autre'
                    : ($existingRewardType !== '' ? 'valeur_existante' : '')),
            'age_minimum' => $existingLimitsByName['Age minimum']['minimum'] ?? '',
            'genre' => isset($existingLimitsByName['Homme'])
                ? 'homme'
                : (isset($existingLimitsByName['Femme']) ? 'femme' : 'indifferent'),
            'langue' => isset($existingLimitsByName['Francais'])
                ? 'francais'
                : (isset($existingLimitsByName['Anglais']) ? 'anglais' : ''),
            'lunettes' => isset($existingLimitsByName['Avec lunettes'])
                ? 'oui'
                : (isset($existingLimitsByName['Sans lunettes']) ? 'non' : ''),
            'restriction_personnalisee' => array_values(array_diff(
                array_keys($existingLimitsByName),
                ['Age minimum', 'Francais', 'Anglais', 'Femme', 'Homme', 'Sans lunettes', 'Avec lunettes']
            )),
        ];
    }
}

$escape = static fn ($value): string => htmlspecialchars(
    is_scalar($value) ? (string) $value : '',
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$readFormText = static fn (string $key): string => is_string($formData[$key] ?? null)
    ? $formData[$key]
    : '';
$customRestrictionValues = is_array($formData['restriction_personnalisee'] ?? null)
    ? $formData['restriction_personnalisee']
    : [];

$validDate = static function (string $value): bool {
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = DateTimeImmutable::getLastErrors();

    return $date !== false
        && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
        && $date->format('Y-m-d') === $value;
};

$validateUpload = static function (array $file, string $label) use (&$formError): ?array {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        $formError = "Le fichier « {$label} » n’a pas pu être reçu.";
        return null;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = [
        'csv' => ['text/plain', 'text/csv', 'application/csv'],
        'doc' => ['application/msword', 'application/x-ole-storage'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'jpeg' => ['image/jpeg'],
        'jpg' => ['image/jpeg'],
        'mp4' => ['video/mp4'],
        'pdf' => ['application/pdf'],
        'png' => ['image/png'],
        'ppt' => ['application/vnd.ms-powerpoint', 'application/x-ole-storage'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        'txt' => ['text/plain'],
        'xls' => ['application/vnd.ms-excel', 'application/x-ole-storage'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
    ];

    if (!isset($allowedTypes[$extension])) {
        $formError = "Le type du fichier « {$label} » n’est pas pris en charge.";
        return null;
    }

    $fileSize = filesize($file['tmp_name']);
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if ($fileSize === false || $fileSize > 20 * 1024 * 1024) {
        $formError = "Le fichier « {$label} » dépasse la taille maximale de 20 Mo.";
        return null;
    }

    if (!in_array($mimeType, $allowedTypes[$extension], true)) {
        $formError = "Le contenu du fichier « {$label} » ne correspond pas à son extension.";
        return null;
    }

    return [
        'tmp_path' => $file['tmp_name'],
        'name' => basename($file['name']),
        'extension' => $extension,
        'size' => $fileSize,
    ];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($readFormText('titre'));
    $description = trim($readFormText('description'));
    $dateStart = $readFormText('date_debut');
    $dateEnd = $readFormText('date_fin');
    $durationMinutes = filter_var($formData['duree'] ?? null, FILTER_VALIDATE_INT);
    $plannedSessionCount = filter_var($formData['nombre_sessions'] ?? null, FILTER_VALIDATE_INT);
    $rewardChoice = $readFormText('retribution');
    $rewardType = $readFormText('type_retribution');
    $customRestrictions = $formData['restriction_personnalisee'] ?? [];

    if ($title === '' || strlen($title) > 100) {
        $formError = 'Le titre est obligatoire et ne peut pas dépasser 100 caractères.';
    } elseif (isset($formData['description']) && !is_string($formData['description'])) {
        $formError = 'La description est invalide.';
    } elseif (strlen($description) > 1000) {
        $formError = 'La description ne peut pas dépasser 1000 caractères.';
    } elseif (!$validDate($dateStart) || !$validDate($dateEnd) || $dateEnd < $dateStart) {
        $formError = 'Les dates de début et de fin sont invalides.';
    } elseif ($durationMinutes === false || $durationMinutes < 1 || $durationMinutes > 1439) {
        $formError = 'La durée doit être comprise entre 1 et 1439 minutes.';
    } elseif ($plannedSessionCount === false || $plannedSessionCount < 1 || $plannedSessionCount > 1000) {
        $formError = 'Le nombre de séances prévues doit être compris entre 1 et 1000.';
    } elseif (!in_array($rewardChoice, ['oui', 'non'], true)) {
        $formError = 'Indiquez si l’expérimentation propose une rétribution.';
    } elseif (
        $rewardChoice === 'oui'
        && !in_array($rewardType, ['financiere', 'autre'], true)
        && !($isEditing && $rewardType === 'valeur_existante' && !empty($editingExperiment['Type_Retribution']))
    ) {
        $formError = 'Choisissez le type de rétribution.';
    } elseif (!is_array($customRestrictions)) {
        $formError = 'Les restrictions personnalisées sont invalides.';
    }

    if ($formError === '' && $isEditing) {
        if ($invalidDocumentDeletionInput) {
            $formError = 'La sélection des documents à supprimer est invalide.';
        } else {
            foreach ($documentsToDelete as $documentId) {
                if (!isset($existingDocumentsById[$documentId])) {
                    $formError = 'Un document sélectionné n’est plus associé à cette expérimentation.';
                    break;
                }
            }
        }
    }

    $uploads = [];
    if ($formError === '' && isset($_FILES['ressources_numeriques'])) {
        $resourceFiles = $_FILES['ressources_numeriques'];
        foreach ($resourceFiles['name'] as $index => $name) {
            $upload = $validateUpload([
                'error' => $resourceFiles['error'][$index],
                'name' => $name,
                'tmp_name' => $resourceFiles['tmp_name'][$index],
            ], $name);
            if ($upload !== null) {
                $uploads[] = $upload;
            }
            if ($formError !== '') {
                break;
            }
        }
    }

    if ($formError === '' && isset($_FILES['fiche_consentement'])) {
        $consentUpload = $validateUpload($_FILES['fiche_consentement'], 'fiche de consentement');
        if ($consentUpload !== null) {
            $uploads[] = $consentUpload;
        }
    }

    $limits = [];
    if ($formError === '' && isset($formData['age_minimum']) && $formData['age_minimum'] !== '') {
        $minimumAge = filter_var($formData['age_minimum'], FILTER_VALIDATE_INT);
        if ($minimumAge === false || $minimumAge < 0 || $minimumAge > 120) {
            $formError = 'L’âge minimum doit être compris entre 0 et 120 ans.';
        } else {
            $limits[] = [
                'name' => 'Age minimum',
                'minimum' => $minimumAge,
                'maximum' => $existingLimitsByName['Age minimum']['maximum'] ?? null,
            ];
        }
    }

    if ($formError === '') {
        $restrictionMap = [
            'genre' => ['homme' => 'Homme', 'femme' => 'Femme'],
            'langue' => ['francais' => 'Francais', 'anglais' => 'Anglais'],
            'lunettes' => ['non' => 'Sans lunettes', 'oui' => 'Avec lunettes'],
        ];

        foreach ($restrictionMap as $field => $values) {
            $value = (string) ($formData[$field] ?? '');
            if ($value === '' || ($field === 'genre' && $value === 'indifferent')) {
                continue;
            }
            if (!isset($values[$value])) {
                $formError = 'Une restriction sélectionnée est invalide.';
                break;
            }
            $name = $values[$value];
            $limits[] = [
                'name' => $name,
                'minimum' => $existingLimitsByName[$name]['minimum'] ?? null,
                'maximum' => $existingLimitsByName[$name]['maximum'] ?? null,
            ];
        }
    }

    if ($formError === '') {
        foreach ($customRestrictions as $restriction) {
            if (!is_string($restriction)) {
                $formError = 'Une restriction personnalisée est invalide.';
                break;
            }
            $restriction = trim($restriction);
            if ($restriction === '') {
                continue;
            }
            if (strlen($restriction) > 50) {
                $formError = 'Chaque restriction personnalisée ne peut pas dépasser 50 caractères.';
                break;
            }
            $limits[] = [
                'name' => $restriction,
                'minimum' => $existingLimitsByName[$restriction]['minimum'] ?? null,
                'maximum' => $existingLimitsByName[$restriction]['maximum'] ?? null,
            ];
        }
    }

    if ($formError === '') {
        $uniqueLimits = [];
        foreach ($limits as $limit) {
            if (!isset($uniqueLimits[$limit['name']]) || $limit['minimum'] !== null) {
                $uniqueLimits[$limit['name']] = $limit;
            }
        }
        $limits = array_values($uniqueLimits);
    }

    if ($formError === '') {
        $storageDirectory = __DIR__ . '/../storage/documents';
        $movedFiles = [];
        $removedDocumentUrls = [];
        $cleanupUploads = static function () use (&$movedFiles): void {
            foreach ($movedFiles as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
        };

        try {
            if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0750, true) && !is_dir($storageDirectory)) {
                throw new RuntimeException('Le dossier de stockage des documents ne peut pas être créé.');
            }

            $pdo->beginTransaction();
            $databaseRewardType = $rewardChoice === 'non'
                ? null
                : ($rewardType === 'financiere'
                    ? 'Financière'
                    : ($rewardType === 'autre'
                        ? 'Autre'
                        : (string) $editingExperiment['Type_Retribution']));

            if ($isEditing) {
                $sessionQuery = $pdo->prepare(
                    'SELECT COUNT(*) AS session_count, MIN(Date_Ses) AS first_session, MAX(Date_Ses) AS last_session
                     FROM `SESSION`
                     WHERE Id_Exp = :experiment_id'
                );
                $sessionQuery->execute(['experiment_id' => $experimentId]);
                $sessionSummary = $sessionQuery->fetch();

                if ((int) $plannedSessionCount < (int) $sessionSummary['session_count']) {
                    throw new RuntimeException('Le nombre de séances prévues ne peut pas être inférieur aux séances déjà créées.');
                }
                if (
                    ($sessionSummary['first_session'] !== null && $dateStart > $sessionSummary['first_session'])
                    || ($sessionSummary['last_session'] !== null && $dateEnd < $sessionSummary['last_session'])
                ) {
                    throw new RuntimeException('Les dates doivent inclure les séances déjà planifiées.');
                }

                $updateExperiment = $pdo->prepare(
                    'UPDATE EXPERIMENTATION
                     SET Nom_Exp = :name, Desc_Exp = :description,
                         Date_Debut_Exp = :date_start, Date_Fin_Exp = :date_end,
                         Duree_Exp_Min = :duration_minutes, Nb_Sessions_Prevues = :session_count,
                         Type_Retribution = :reward_type
                     WHERE Id_Exp = :experiment_id AND Id_Equipe = :team_id'
                );
                $updateExperiment->execute([
                    'name' => $title,
                    'description' => $description !== '' ? $description : null,
                    'date_start' => $dateStart . ' 00:00:00',
                    'date_end' => $dateEnd . ' 23:59:59',
                    'duration_minutes' => $durationMinutes,
                    'session_count' => $plannedSessionCount,
                    'reward_type' => $databaseRewardType,
                    'experiment_id' => $experimentId,
                    'team_id' => $currentTeamId,
                ]);
                $deleteLimits = $pdo->prepare('DELETE FROM LIMITES WHERE Id_Exp = :experiment_id');
                $deleteLimits->execute(['experiment_id' => $experimentId]);

                $deleteDocument = $pdo->prepare(
                    'DELETE FROM DOCUMENT
                     WHERE Id_Doc = :document_id AND Id_Exp = :experiment_id'
                );
                foreach ($documentsToDelete as $documentId) {
                    $deleteDocument->execute([
                        'document_id' => $documentId,
                        'experiment_id' => $experimentId,
                    ]);
                    if ($deleteDocument->rowCount() !== 1) {
                        throw new RuntimeException('Un document sélectionné n’est plus associé à cette expérimentation.');
                    }
                    $removedDocumentUrls[$documentId] = (string) $existingDocumentsById[$documentId]['Url_Doc'];
                }
            } else {
                $insertExperiment = $pdo->prepare(
                    'INSERT INTO EXPERIMENTATION
                        (Nom_Exp, Desc_Exp, Date_Debut_Exp, Date_Fin_Exp, Duree_Exp_Min,
                         Nb_Sessions_Prevues, Type_Retribution, Id_Equipe)
                     VALUES
                        (:name, :description, :date_start, :date_end, :duration_minutes,
                         :session_count, :reward_type, :team_id)'
                );
                $insertExperiment->execute([
                    'name' => $title,
                    'description' => $description !== '' ? $description : null,
                    'date_start' => $dateStart . ' 00:00:00',
                    'date_end' => $dateEnd . ' 23:59:59',
                    'duration_minutes' => $durationMinutes,
                    'session_count' => $plannedSessionCount,
                    'reward_type' => $databaseRewardType,
                    'team_id' => $currentTeamId,
                ]);
                $experimentId = (int) $pdo->lastInsertId();
            }

            $getConstraintId = static function (string $name, bool $custom = false) use ($pdo): int {
                $query = $pdo->prepare('SELECT Id_Contrainte FROM CONTRAINTE WHERE Nom_Contrainte = :name');
                $query->execute(['name' => $name]);
                $constraintId = $query->fetchColumn();

                if ($constraintId !== false) {
                    return (int) $constraintId;
                }
                if (!$custom) {
                    throw new RuntimeException('Une restriction choisie n’existe plus dans la base de données.');
                }

                $insert = $pdo->prepare(
                    'INSERT INTO CONTRAINTE (Nom_Contrainte, Desc_Contrainte)
                     VALUES (:name, :description)'
                );
                $insert->execute([
                    'name' => $name,
                    'description' => 'Restriction personnalisée créée depuis une expérimentation.',
                ]);
                return (int) $pdo->lastInsertId();
            };

            $insertLimit = $pdo->prepare(
                'INSERT INTO LIMITES (Id_Exp, Id_Contrainte, Val_Min, Val_Max)
                 VALUES (:experiment_id, :constraint_id, :minimum, :maximum)'
            );
            foreach ($limits as $limit) {
                $constraintId = $getConstraintId(
                    $limit['name'],
                    !in_array($limit['name'], [
                        'Age minimum', 'Francais', 'Anglais', 'Femme', 'Homme', 'Sans lunettes',
                    ], true)
                );
                $insertLimit->execute([
                    'experiment_id' => $experimentId,
                    'constraint_id' => $constraintId,
                    'minimum' => $limit['minimum'],
                    'maximum' => $limit['maximum'],
                ]);
            }

            $insertDocument = $pdo->prepare(
                'INSERT INTO DOCUMENT (Nom_Doc, Type_Doc, Poids_Doc, Url_Doc, Id_Exp)
                 VALUES (:name, :type, :size, :url, :experiment_id)'
            );
            foreach ($uploads as $upload) {
                $storedName = bin2hex(random_bytes(16)) . '.' . $upload['extension'];
                $storedPath = $storageDirectory . '/' . $storedName;
                if (!move_uploaded_file($upload['tmp_path'], $storedPath)) {
                    throw new RuntimeException('Un document n’a pas pu être enregistré.');
                }
                $movedFiles[] = $storedPath;
                $insertDocument->execute([
                    'name' => $upload['name'],
                    'type' => $upload['extension'],
                    'size' => $upload['size'],
                    'url' => 'storage/documents/' . $storedName,
                    'experiment_id' => $experimentId,
                ]);
            }

            $insertLog = $pdo->prepare(
                'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                 VALUES (:type, :ip, :user_id)'
            );
            $insertLog->execute([
                'type' => $isEditing ? 'Modification expérimentation' : 'Création expérimentation',
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                'user_id' => (int) $currentUser['Id_User'],
            ]);

            $pdo->commit();

            $documentCleanupFailed = false;
            $storageRoot = realpath($storageDirectory);
            if ($isEditing && $storageRoot !== false) {
                foreach ($removedDocumentUrls as $documentId => $documentUrl) {
                    $storagePrefix = 'storage/documents/';
                    if (strncmp($documentUrl, $storagePrefix, strlen($storagePrefix)) !== 0) {
                        continue;
                    }

                    $storedName = substr($documentUrl, strlen($storagePrefix));
                    if ($storedName === '' || basename($storedName) !== $storedName) {
                        $documentCleanupFailed = true;
                        error_log('Could not safely remove stored document ' . $documentId . '.');
                        continue;
                    }

                    $storedPath = realpath($storageRoot . DIRECTORY_SEPARATOR . $storedName);
                    if ($storedPath === false) {
                        continue;
                    }

                    $storedDirectory = dirname($storedPath);
                    $sameDirectory = DIRECTORY_SEPARATOR === '\\'
                        ? strcasecmp($storedDirectory, $storageRoot) === 0
                        : $storedDirectory === $storageRoot;
                    if (!$sameDirectory) {
                        $documentCleanupFailed = true;
                        error_log('Refused to remove document outside storage for document ' . $documentId . '.');
                        continue;
                    }

                    if (is_file($storedPath) && !unlink($storedPath)) {
                        $documentCleanupFailed = true;
                        error_log('Could not remove stored document ' . $documentId . '.');
                    }
                }
            }

            $redirectQuery = $isEditing ? 'updated=1' : 'created=1';
            if ($documentCleanupFailed) {
                $redirectQuery .= '&document_cleanup_failed=1';
            }
            header('Location: ./GestionExperimentationBureau.php?' . $redirectQuery);
            exit;
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $cleanupUploads();
            error_log(($isEditing ? 'Experiment update' : 'Experiment creation') . ' failed: ' . $exception->getMessage());
            $formError = 'L’expérimentation n’a pas pu être enregistrée. Vérifiez les informations et réessayez.';
        } catch (RuntimeException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $cleanupUploads();
            $formError = $exception->getMessage();
        }
    }
}
$selectedExistingDocumentCount = count(array_intersect(
    array_keys($existingDocumentsById),
    $documentsToDelete
));
$remainingExistingDocumentCount = max(0, count($existingDocuments) - $selectedExistingDocumentCount);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEditing ? 'Modifier' : 'Créer' ?> une expérimentation — Bureau</title>
    <link rel="stylesheet" href="../css/stylesGEDesktop.css?v=3">
    <link rel="stylesheet" href="../css/stylesCreerExperimentationDesktop.css?v=6">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="./MenuEnseignantChercheurBureau.php" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="../assets/brain-logo.png" alt="">
                </a>

                <nav class="barre-laterale__navigation">
                    <a href="#">Rétributions</a>
                    <a class="actuel" href="./GestionExperimentationBureau.php" aria-current="page">Expérimentations</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>

                <img class="barre-laterale__logo-universite" src="../assets/university-logo.png" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal page-creation">
                <header class="page-creation__entete">
                    <h1><?= $isEditing ? 'Modifier' : 'Créer' ?> une expérimentation</h1>
                </header>

                <?php if ($formError !== ''): ?>
                    <p class="formulaire-erreur" role="alert"><?= $escape($formError) ?></p>
                <?php endif; ?>

                <form class="formulaire-creation" method="post" enctype="multipart/form-data">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="experiment_id" value="<?= (int) $experimentId ?>">
                    <?php endif; ?>
                    <section class="informations-experimentation" aria-label="Informations de l’expérimentation">
                        <div class="champ champ--titre">
                            <label for="titre-experimentation">Titre de l’expérimentation</label>
                            <input id="titre-experimentation" name="titre" type="text" maxlength="100" required value="<?= $escape($formData['titre'] ?? '') ?>">
                        </div>

                        <div class="champ champ--description">
                            <label for="description-experimentation">Description :</label>
                            <textarea id="description-experimentation" name="description" rows="3" maxlength="1000"><?= $escape($formData['description'] ?? '') ?></textarea>
                        </div>

                        <div class="details-experimentation">
                            <div class="champ-inline champ-inline--date-debut">
                                <label for="date-debut">Date de début:</label>
                                <input id="date-debut" name="date_debut" type="date" required value="<?= $escape($formData['date_debut'] ?? '') ?>">
                            </div>
                            <div class="champ-inline champ-inline--duree">
                                <label for="duree">Durée (min) :</label>
                                <input id="duree" name="duree" type="number" min="1" max="1439" required value="<?= $escape($formData['duree'] ?? '') ?>">
                            </div>
                            <div class="champ-inline champ-inline--date-fin">
                                <label for="date-fin">Date de fin:</label>
                                <input id="date-fin" name="date_fin" type="date" required value="<?= $escape($formData['date_fin'] ?? '') ?>">
                            </div>
                            <div class="champ-inline champ-inline--sessions">
                                <label for="nombre-sessions">Nombre de sessions prévues :</label>
                                <input id="nombre-sessions" name="nombre_sessions" type="number" min="1" max="1000" required value="<?= $escape($formData['nombre_sessions'] ?? '') ?>">
                            </div>
                        </div>

                        <fieldset class="choix-retribution">
                            <legend>Rétribution?</legend>
                            <div class="boutons-radio">
                                <label class="bouton-radio">
                                    <input type="radio" name="retribution" value="oui" required <?= ($formData['retribution'] ?? '') === 'oui' ? 'checked' : '' ?>>
                                    <span>Oui</span>
                                </label>
                                <label class="bouton-radio">
                                    <input type="radio" name="retribution" value="non" required <?= ($formData['retribution'] ?? '') === 'non' ? 'checked' : '' ?>>
                                    <span>Non</span>
                                </label>
                            </div>
                        </fieldset>

                        <div class="champ-inline champ-inline--type-retribution">
                            <label for="type-retribution">Type de rétribution?</label>
                            <select id="type-retribution" name="type_retribution">
                                <option value="">Sélectionner</option>
                                <option value="financiere" <?= ($formData['type_retribution'] ?? '') === 'financiere' ? 'selected' : '' ?>>Financière</option>
                                <option value="autre" <?= ($formData['type_retribution'] ?? '') === 'autre' ? 'selected' : '' ?>>Autre</option>
                                <?php if (($formData['type_retribution'] ?? '') === 'valeur_existante'): ?>
                                    <option value="valeur_existante" selected><?= $escape($editingExperiment['Type_Retribution']) ?> (conservée)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </section>

                    <hr class="separateur-creation">

                    <section class="restrictions-participation" aria-labelledby="titre-restrictions">
                        <h2 id="titre-restrictions">Restrictions de participation</h2>

                        <div class="restriction-age">
                            <label for="age-minimum">Âge minimum?</label>
                            <input id="age-minimum" name="age_minimum" type="number" min="0" max="120" value="<?= $escape($formData['age_minimum'] ?? '') ?>">
                        </div>

                        <fieldset class="restriction-genre">
                            <legend>Genre?</legend>
                            <label><input type="radio" name="genre" value="homme" <?= ($formData['genre'] ?? '') === 'homme' ? 'checked' : '' ?>> Homme</label>
                            <label><input type="radio" name="genre" value="femme" <?= ($formData['genre'] ?? '') === 'femme' ? 'checked' : '' ?>> Femme</label>
                            <label><input type="radio" name="genre" value="indifferent" <?= ($formData['genre'] ?? '') === 'indifferent' ? 'checked' : '' ?>> n’importe</label>
                        </fieldset>

                        <div class="restriction-langue">
                            <label for="langue">Langue(s)?</label>
                            <select id="langue" name="langue">
                                <option value="">Sélectionner</option>
                                <option value="francais" <?= ($formData['langue'] ?? '') === 'francais' ? 'selected' : '' ?>>Français</option>
                                <option value="anglais" <?= ($formData['langue'] ?? '') === 'anglais' ? 'selected' : '' ?>>Anglais</option>
                            </select>
                        </div>

                        <fieldset class="restriction-lunettes">
                            <legend>Port de lunettes?</legend>
                            <label><input type="radio" name="lunettes" value="oui" <?= ($formData['lunettes'] ?? '') === 'oui' ? 'checked' : '' ?>> Oui</label>
                            <label><input type="radio" name="lunettes" value="non" <?= ($formData['lunettes'] ?? '') === 'non' ? 'checked' : '' ?>> Non</label>
                        </fieldset>

                        <div class="fiche-consentement">
                            <label for="fiche-consentement">Fiche de consentement</label>
                            <label class="fiche-consentement__fichier" for="fiche-consentement">
                                <span id="nom-fiche-consentement"><?= $escape($_FILES['fiche_consentement']['name'] ?? 'Aucun fichier sélectionné') ?></span>
                                <input id="fiche-consentement" name="fiche_consentement" type="file" accept=".pdf,application/pdf">
                            </label>
                        </div>

                        <div class="restrictions-personnalisees" id="restrictions-personnalisees">
                            <?php foreach ($customRestrictionValues as $index => $restriction): ?>
                                <label class="restriction-personnalisee">
                                    Autre restriction <?= (int) $index + 1 ?>
                                    <input
                                        type="text"
                                        name="restriction_personnalisee[]"
                                        maxlength="50"
                                        autocomplete="off"
                                        aria-label="Préciser la restriction <?= (int) $index + 1 ?>"
                                        value="<?= $escape($restriction) ?>"
                                    >
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button class="bouton-action bouton-action--restriction" type="button" id="ajouter-restriction">Ajouter une restriction</button>
                    </section>

                    <hr class="separateur-creation separateur-creation--ressources">

                    <button class="bouton-action bouton-action--ressource" type="button" id="associer-ressource">Associer une ressource numérique</button>
                    <input class="import-ressource__fichier" id="fichiers-ressources" name="ressources_numeriques[]" type="file" accept=".csv,.doc,.docx,.jpeg,.jpg,.mp4,.pdf,.png,.ppt,.pptx,.txt,.xls,.xlsx" multiple tabindex="-1" aria-hidden="true">

                    <section
                        class="ressources-associees"
                        id="ressources-associees"
                        data-existing-count="<?= count($existingDocuments) ?>"
                        aria-labelledby="titre-ressources"
                    >
                        <div class="ressources-associees__entete">
                            <h2 id="titre-ressources">Documents numériques associés</h2>
                            <span class="ressources-associees__compteur" id="compteur-ressources" aria-live="polite">
                                <?php if ($remainingExistingDocumentCount > 0): ?>
                                    <?= $remainingExistingDocumentCount ?> document<?= $remainingExistingDocumentCount > 1 ? 's' : '' ?> associé<?= $remainingExistingDocumentCount > 1 ? 's' : '' ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($existingDocuments !== []): ?>
                            <div class="ressources-associees__existantes">
                                <p>Cochez les documents à supprimer :</p>
                                <ul>
                                    <?php foreach ($existingDocuments as $documentName): ?>
                                        <li>
                                            <span><?= $escape($documentName['Nom_Doc']) ?></span>
                                            <label>
                                                <input
                                                    type="checkbox"
                                                    name="documents_a_supprimer[]"
                                                    value="<?= (int) $documentName['Id_Doc'] ?>"
                                                    <?= in_array((int) $documentName['Id_Doc'], $documentsToDelete, true) ? 'checked' : '' ?>
                                                >
                                                Supprimer
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <p class="ressources-associees__vide" id="message-ressources-vides" <?= $remainingExistingDocumentCount > 0 ? 'hidden' : '' ?>>Aucune ressource associée.</p>
                        <ul class="ressources-associees__liste" id="liste-ressources"></ul>
                    </section>

                    <div class="actions-formulaire">
                        <button class="bouton-action bouton-action--creer" type="submit"><?= $isEditing ? 'Enregistrer les modifications' : 'Créer' ?></button>
                        <a class="bouton-action" href="./GestionExperimentationBureau.php">Annuler</a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="../js/creer-experimentation.js?v=4"></script>
</body>
</html>
