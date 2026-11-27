<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connexion/connexionModel.php';
require_once 'experimentationParticipantModel.php';

//Récupérer les informations de l'utilisateur connecté pour les afficher
$current_user_mail = $_SESSION['user_mail'];
$user = get_user_by_mail($current_user_mail);
$current_user_id = $user['Id_User'];

//Récupérer l'ID de l'expérimentation dans l'url de la page
$exp_id = $_POST['id_exp'] ?? $_GET['id'] ?? null;

//Permet à l'utilisateur de s'inscrire à une expérimentation avec une insertion dans la base de données
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'inscription') {
    $validExpId = filter_var($exp_id, FILTER_VALIDATE_INT);

    if ($current_user_id && $validExpId) {
        insert_insc($current_user_id, $validExpId);
    }

    header('Location: experimentationParticipantView.php?id=' . urlencode((string) $validExpId));
    exit();
}

//Récupérer le statut d'inscription dans la base
$expe = get_exp_by_id($exp_id);
$statut_insc = get_statutinsc_by_user_exp($current_user_id, $exp_id);
//Modifier la variable si jamais l'inscription n'existe pas au lieu qu'elle soit vide
if (empty($statut_insc)) { 
    $statut_insc = "Non";
}