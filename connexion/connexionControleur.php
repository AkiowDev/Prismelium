<?php
declare(strict_types=1);

namespace Prismelium\Controllers;

use Prismelium\Controleur;
use Prismelium\Models\ConnexionModele;

final class ConnexionControleur extends Controleur
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
                $user = (new ConnexionModele())->get_user_by_mail($email);
                if ($user && hash_equals((string) $user['Mdp_User'], $password)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['Id_User'];
                    $_SESSION['user_mail'] = $user['Mail_User'];

                    if ((int) $user['Id_Role'] === 3) {
                        $this->redirect('/accueilParticipant/accueilParticipantView.php');
                    }
                    if ((int) $user['Id_Role'] === 1) {
                        $this->redirect('index.php?page=enseignant-chercheur');
                    }
                    $this->redirect('/accueilView.php');
                }

                $error = 'Email ou mot de passe incorrect.';
            }
        }

        $this->render('connexionView', ['erreur' => $error]);
    }
}
