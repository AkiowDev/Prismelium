<?php
declare(strict_types=1);

namespace Prismelium\Controllers;

use Prismelium\Models\ConnexionModel;

final class ConnexionController extends \Prismelium\Controleur
{
    public function login(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = is_string($_POST['mail'] ?? null) ? trim($_POST['mail']) : '';
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

            if ($email === '' || $password === '') {
                $error = 'Veuillez remplir tous les champs.';
            } else {
                $user = (new ConnexionModel())->get_user_by_mail($email);

                if ($user && hash_equals((string) $user['Mdp_User'], $password)) {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['Id_User'];
                    $_SESSION['user_mail'] = $user['Mail_User'];
                    $_SESSION['user_role'] = $user['Id_Role'];

                    switch ((int) $user['Id_Role']) {
                        case 3:
                            $this->redirect('Participant/accueilParticipant/accueilParticipantController.php');
                            break;

                        case 1:
                            $this->redirect('index.php?page=enseignant-chercheur');
                            break;

                        case 2:
                            $this->redirect('Gestionnaire/menuGest/menuGest.php');
                            break;

                        default:
                            session_unset();
                            session_destroy();
                            $error = 'Rôle utilisateur non reconnu.';
                            break;
                    }
                } else {
                    $error = 'Email ou mot de passe incorrect.';
                }
            }
        }

        $this->render('connexionView', ['erreur' => $error]);
    }
}