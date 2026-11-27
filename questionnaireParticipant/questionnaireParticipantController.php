<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connexion/connexionModel.php';

//Récupérer les informations de l'utilisateur connecté pour les afficher
$current_user_mail = $_SESSION['user_mail'];
$user = get_user_by_mail($current_user_mail);

?>