
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/connexionModel.php';

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données
    $mail = trim($_POST['mail'] ?? '');
    $password = $_POST['password'] ?? '';

    // Vérification des champs
    if ($mail !== '' && $password !== '') {

        // Recherche de l'utilisateur
        $user = get_user_by_mail($mail);

        // Vérification de l'identifiant et du mot de passe
        if ($user && $password === $user['Mdp_User']) {

            // Régénération de l'identifiant de session
            session_regenerate_id(true);

            // Enregistrement des informations utilisateur
            $_SESSION['user_id'] = $user['Id_User'];
            $_SESSION['user_mail'] = $user['Mail_User'];
            $_SESSION['user_role'] = $user['Id_Role'];

            // Redirection selon le rôle
            switch ((int) $user['Id_Role']) {
                case 3:
                    header('Location: ../Participant/accueilParticipant/accueilParticipantController.php');
                    exit();
                case 2:
                    header('Location: ../Gestionnaire/menuGest/menuGest.php');
                    exit();
                case 1:
                    $erreur = "Espace Enseignant-Chercheur non disponible pour le moment.";
                    break;
                default:
                    session_unset();
                    session_destroy();

                    $erreur = "Rôle utilisateur non reconnu.";
                    break;
            }

            if ($erreur === null) {
                exit();
            }
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }

    } else {

        $erreur = "Veuillez remplir tous les champs.";

    }
}
require_once __DIR__ . '/connexionView.php';

?>
