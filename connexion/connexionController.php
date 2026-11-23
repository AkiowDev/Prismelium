<?php
session_start();
require_once 'connexionModel.php';

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($mail) && !empty($password)) {
        $user = get_user_by_mail($mail);

        if ($user && $password == $user['Mdp_User']) {
            $_SESSION['user_id'] = $user['Id_User'];
            $_SESSION['user_mail'] = $user['Mail_User'];
            
            if ($user['Id_Role'] == 3) {
                header('Location: ../accueilParticipant/accueilParticipantView.php');
            } elseif ($user['Id_Role'] == 1) {
                header('Location: ../views/MenuEnseignantChercheurBureau.php');
            } else {
                header('Location: ../accueilView.php');
            }
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

require_once 'connexionView.php';
?>