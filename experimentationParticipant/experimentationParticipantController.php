<?php
session_start();
require_once "experimentationParticipantModel.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/connexionController.php');
    exit();
}
$current_user_id = (int) $_SESSION['user_id'];

$exp_id = $_POST['id_exp'] ?? $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'inscription') {
    $validExpId = filter_var($exp_id, FILTER_VALIDATE_INT);

    if ($current_user_id && $validExpId) {
        insert_insc($current_user_id, $validExpId);
    }

    header('Location: experimentationParticipantView.php?id=' . urlencode((string) $validExpId));
    exit();
}

$expe = get_exp_by_id($exp_id);
$statut_insc = get_statutinsc_by_user_exp($current_user_id, $exp_id);
if (empty($statut_insc)) {
    $statut_insc = "Non";
}