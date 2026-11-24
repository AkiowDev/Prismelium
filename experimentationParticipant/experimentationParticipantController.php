<?php
session_start();
require_once "experimentationParticipantModel.php";

$exp_id = $_GET['id'] ?? null;

$expe = get_exp_by_id($exp_id);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/connexionController.php');
    exit();
}
$current_user_id = $_SESSION['user_id'];

$statut_insc = get_statutinsc_by_user_exp($current_user_id, $exp_id);
if (empty($statut_insc)) {
    $statut_insc = "Non";
}

?>