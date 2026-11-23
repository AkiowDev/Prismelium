<?php
declare(strict_types=1);

require_once __DIR__ . '/load_database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$currentUserId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);

if ($currentUserId === false || $currentUserId < 1) {
    header('Location: ../connexion/connexionController.php');
    exit;
}

$currentUserQuery = $pdo->prepare(
    'SELECT Id_User, Nom_User, Prenom_User, Id_Equipe, Id_Role
     FROM UTILISATEUR
     WHERE Id_User = :user_id'
);
$currentUserQuery->execute(['user_id' => $currentUserId]);
$currentUser = $currentUserQuery->fetch();

if (!$currentUser) {
    session_unset();
    session_destroy();
    header('Location: ../connexion/connexionController.php');
    exit;
}

if ((int) $currentUser['Id_Role'] !== 1 || empty($currentUser['Id_Equipe'])) {
    http_response_code(403);
    exit('Cette page est réservée aux enseignants-chercheurs associés à une équipe.');
}

$currentTeamId = (int) $currentUser['Id_Equipe'];
