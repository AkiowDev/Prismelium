<?php
declare(strict_types=1);

namespace Prismelium\Controllers;

use DateTimeImmutable;
use PDOException;
use Prismelium\Controleur;
use Prismelium\GardeEnseignantChercheur;
use Prismelium\Models\InscriptionModele;
use RuntimeException;

final class InscriptionControleur extends Controleur
{
    public function index(): void
    {
        $manager = GardeEnseignantChercheur::requireResearcher();
        $model = new InscriptionModele();
        $error = '';
        $notice = is_string($_GET['notice'] ?? null) ? $_GET['notice'] : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
            $sessionId = filter_var($_POST['session_id'] ?? null, FILTER_VALIDATE_INT);
            $decision = $_POST['action'] ?? null;

            if (!\csrf_is_valid($_POST['csrf_token'] ?? null)) {
                http_response_code(400);
                $error = 'La requête a expiré. Rechargez la page et réessayez.';
            } elseif (!is_string($decision) || !in_array($decision, ['accept', 'refuse'], true)) {
                http_response_code(400);
                $error = 'L’action demandée est invalide.';
            } elseif ($userId === false || $userId < 1 || $sessionId === false || $sessionId < 1) {
                $error = 'La demande d’inscription sélectionnée est invalide.';
            } else {
                try {
                    $model->decide(
                        (int) $manager['Id_Equipe'],
                        (int) $manager['Id_User'],
                        $userId,
                        $sessionId,
                        $decision
                    );
                    $this->redirect('index.php?page=inscriptions&notice='
                        . ($decision === 'accept' ? 'accepted' : 'refused'));
                } catch (PDOException $exception) {
                    error_log('Enrollment decision failed: ' . $exception->getMessage());
                    $error = 'La décision n’a pas pu être enregistrée. Réessayez.';
                } catch (RuntimeException $exception) {
                    $error = $exception->getMessage();
                }
            }
        }

        $enrollments = $model->pendingForTeam((int) $manager['Id_Equipe']);
        $experimentIds = array_values(array_unique(array_map(
            static fn (array $enrollment): int => (int) $enrollment['Id_Exp'],
            $enrollments
        )));
        $documents = $model->consentDocuments($experimentIds);
        $cards = [];

        foreach ($enrollments as $enrollment) {
            try {
                $age = (new DateTimeImmutable((string) $enrollment['Birth_User']))
                    ->diff(new DateTimeImmutable('today'))->y . ' ans';
            } catch (\Exception $exception) {
                $age = 'Âge indisponible';
            }
            $fullName = trim($enrollment['Prenom_User'] . ' ' . $enrollment['Nom_User']);
            $sessionDate = new DateTimeImmutable((string) $enrollment['Date_Ses']);
            $cardData = [
                'userId' => (int) $enrollment['Id_User'],
                'sessionId' => (int) $enrollment['Id_Ses'],
                'name' => $fullName,
                'age' => $age,
                'email' => (string) $enrollment['Mail_User'],
                'experiment' => (string) $enrollment['Nom_Exp'],
                'session' => $sessionDate->format('d/m/Y')
                    . ' · ' . substr((string) $enrollment['Debut_Ses'], 0, 5)
                    . '–' . substr((string) $enrollment['Fin_Ses'], 0, 5),
                'phone' => (string) ($enrollment['Tel_User'] ?? ''),
                'gender' => (string) $enrollment['Genre_User'],
                'language' => (string) $enrollment['Lang_User'],
                'description' => (string) ($enrollment['Desc_User'] ?? ''),
                'documents' => $documents[(int) $enrollment['Id_Exp']] ?? [],
            ];
            $json = json_encode($cardData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            if ($json === false) {
                throw new RuntimeException('Les détails d’une inscription ne peuvent pas être affichés.');
            }
            $cards[] = [
                'id' => (int) $enrollment['Id_User'],
                'name' => $fullName,
                'experiment' => (string) $enrollment['Nom_Exp'],
                'data' => $json,
            ];
        }

        $this->render('inscriptions/liste', [
            'cards' => $cards,
            'csrfToken' => \csrf_token(),
            'actionError' => $error,
            'notice' => $notice,
        ]);
    }
}
