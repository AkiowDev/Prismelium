<?php
session_start();
require_once 'connexionModel.php';

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($mail) && !empty($password)) {
        $user = get_user_by_mail($mail);

        // On vérifie si l'utilisateur existe et si le mot de passe est correct
        if ($user && $password == $user['Mdp_User']) {
            // Connexion réussie : stockage en session
            $_SESSION['user_id'] = $user['Id_User']; // Adaptez selon votre clé primaire
            $_SESSION['user_mail'] = $user['Mail_User'];
            
            header('Location: accueilView.php'); // Redirection vers la page protégée
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