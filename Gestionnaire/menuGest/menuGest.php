<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_mail'])){
    header('Location: ../../Connexion/connexionController.php');
    exit();
}

$current_user_mail = $_SESSION['user_mail'];

require_once __DIR__ . '/../../Connexion/connexionModel.php';

$user = get_user_by_mail($current_user_mail);

if (!$user) {

    session_unset();
    session_destroy();

    header('Location: ../../Connexion/connexionController.php');
    exit();
}

if ((int) $user['Id_Role'] !== 2) {

    header('Location: ../../Connexion/connexionController.php');
    exit();
}

$planningDays = [
    ['date' => date('d/m/Y'), 'events' => '3 événements notés', 'current' => true],
    ['date' => date('d/m/Y', strtotime('+1 day')), 'events' => '5 événements notés', 'current' => false],
    ['date' => date('d/m/Y', strtotime('+2 day')), 'events' => '4 événements notés', 'current' => false],
    ['date' => date('d/m/Y', strtotime('+3 day')), 'events' => '99 événements notés', 'current' => false],
];

require_once __DIR__ . '/menuGestView.php';

?>