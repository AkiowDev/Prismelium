<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connexion/connexionModel.php';

//Récupérer les informations de l'utilisateur connecté pour les afficher
$current_user_mail = $_SESSION['user_mail'];
$user = get_user_by_mail($current_user_mail);

$weekOffset = (int) ($_SESSION['planning_week_offset'] ?? 0);

//Permet de pouvoir naviguer entre les semaines grâce aux boutons
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'semaine-precedente') {
        $_SESSION['planning_week_offset'] = $weekOffset - 1;
        header('Location: planningParticipantView.php');
        exit();
    }

    if ($action === 'semaine-suivante') {
        $_SESSION['planning_week_offset'] = $weekOffset + 1;
        header('Location: planningParticipantView.php');
        exit();
    }
}

//Date du jour
$date = new DateTime();
//Obtenir la date du dernier lundi passé
while ($date->format('N') !== '1') {
    $date->sub(new DateInterval('P1D'));
}

//Obtenir le nombre de semaine d'écart entre celle affiché sur le planning et la semaine actuelle
if ($weekOffset > 0) {
    $date->modify('+' . ($weekOffset * 7) . ' days');
} elseif ($weekOffset < 0) {
    $date->modify((abs($weekOffset) * 7) . ' days ago');
}

//Tableau avec les dates afin de les afficher dans la View
$planningDays = [];
$currentDay = clone $date;

for ($i = 0; $i < 6; $i++) {
    $planningDays[] = $currentDay->format('d/m/Y');
    $currentDay->modify('+1 day');
}