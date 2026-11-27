<?php
session_start();
require_once 'connexionModel.php';

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Récupérer les informations entrées
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    //Vérifier que ce ne soit pas vide
    if (!empty($mail) && !empty($password)) {
        $user = get_user_by_mail($mail);

        //Vérifier que l'utilisateur existe et que le mot de passe soit bon
        if ($user && $password == $user['Mdp_User']) {
            $_SESSION['user_id'] = $user['Id_User'];
            $_SESSION['user_mail'] = $user['Mail_User'];
            
            //Rediriger vers la bonne page en fonction du rôle
            if($user['Id_Role']==3) {
                header('Location: ../accueilParticipant/accueilParticipantView.php');
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