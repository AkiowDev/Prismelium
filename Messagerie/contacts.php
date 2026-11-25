<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /Prismelium/Connexion/connexionView.php');
    exit;
}

require_once __DIR__ . '/load_database.php';
require_once __DIR__ . '/models/Models.php';

// Récupérer le texte de recherche
$recherche = trim($_GET['recherche'] ?? '');

// Récupérer les contacts
$contacts = recupererContacts($pdo, $recherche);

// Préparer l'affichage de la messagerie
$type_messages = 'contact';
$message_selectionne = null;

$messages = recupererMessages($pdo, $_SESSION['user_id']);

$page = 'contacts';

require __DIR__ . '/views/MessagerieView.php';

?>