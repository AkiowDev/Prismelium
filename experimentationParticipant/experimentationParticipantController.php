<?php

require_once "experimentationParticipantModel.php";
require_once "../accueilParticipant/accueilParticipantController.php";

$exp_id = $_GET['id'] ?? null;

$expe = get_exp_by_id($exp_id);

$statut_insc = get_statutinsc_by_user_exp($current_user_id, $exp_id); //TODO: Récupérer l'id de l'utilisateur connecté
if ($statut_insc == 'Validee') {
    $statut_insc = "Oui";
} else if ($statut_insc == 'En attente') {
    $statut_insc = "En attente";
} else if ($statut_insc == 'Attente gestionnaire') {
    $statut_insc = "Attente gestionnaire";
} else {
    $statut_insc = "Non";
}

?>