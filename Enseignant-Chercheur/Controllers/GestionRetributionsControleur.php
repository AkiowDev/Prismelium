<?php
declare(strict_types=1);

namespace Prismelium\Controllers;

use PDOException;
use Prismelium\Controleur;
use Prismelium\GardeEnseignantChercheur;
use Prismelium\Models\GestionRetributionsModele;
use RuntimeException;

final class GestionRetributionsControleur extends Controleur
{
    public function index(): void
    {
        $researcher = GardeEnseignantChercheur::requireResearcher();
        $model = new GestionRetributionsModele();
        $error = '';
        $notice = is_string($_GET['notice'] ?? null) ? $_GET['notice'] : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
            $sessionId = filter_var($_POST['session_id'] ?? null, FILTER_VALIDATE_INT);

            if (!\csrf_is_valid($_POST['csrf_token'] ?? null)) {
                http_response_code(400);
                $error = 'La requête a expiré. Rechargez la page et réessayez.';
            } elseif ($userId === false || $userId < 1 || $sessionId === false || $sessionId < 1) {
                http_response_code(400);
                $error = 'La rétribution sélectionnée est invalide.';
            } else {
                try {
                    $model->markAsSent(
                        (int) $researcher['Id_Equipe'],
                        (int) $researcher['Id_User'],
                        $userId,
                        $sessionId
                    );
                    $this->redirect('index.php?page=retributions&notice=sent');
                } catch (PDOException $exception) {
                    error_log('Reward confirmation failed: ' . $exception->getMessage());
                    $error = 'La rétribution n’a pas pu être confirmée. Réessayez.';
                } catch (RuntimeException $exception) {
                    $error = $exception->getMessage();
                }
            }
        }

        $search = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
        if (strlen($search) > 120) {
            $search = substr($search, 0, 120);
        }

        $cards = [];
        foreach ($model->pendingForTeam((int) $researcher['Id_Equipe'], $search) as $reward) {
            $name = trim((string) $reward['Prenom_User'] . ' ' . (string) $reward['Nom_User']);
            $cards[] = [
                'userId' => (int) $reward['Id_User'],
                'sessionId' => (int) $reward['Id_Ses'],
                'name' => $name,
                'experiment' => (string) $reward['Nom_Exp'],
            ];
        }

        $this->render('retributions/liste', [
            'cards' => $cards,
            'search' => $search,
            'csrfToken' => \csrf_token(),
            'actionError' => $error,
            'notice' => $notice,
        ]);
    }
}
