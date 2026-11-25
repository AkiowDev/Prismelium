<?php
session_start();
require_once '../connexion/connexionModel.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/connexionController.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_mail = $_SESSION['user_mail'];

$user = get_user_by_mail($current_user_mail);

$planningDays = [
    ['date' => date('d/m/Y'), 'events' => '3 événements notés', 'current' => true],
    ['date' => date('d/m/Y', strtotime('+1 day')), 'events' => '5 événements notés', 'current' => false],
    ['date' => date('d/m/Y', strtotime('+2 day')), 'events' => '4 événements notés', 'current' => false],
    ['date' => date('d/m/Y', strtotime('+3 day')), 'events' => '99 événements notés', 'current' => false],
];
?>