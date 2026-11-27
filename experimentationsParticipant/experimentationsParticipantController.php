<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connexion/connexionModel.php';
require_once 'experimentationsParticipantModel.php';

//Récupérer les informations de l'utilisateur connecté pour les afficher
$current_user_mail = $_SESSION['user_mail'];
$user = get_user_by_mail($current_user_mail);

//Avoir la liste de toutes les experimentations en cours
$exp = get_experimentations_en_cours();

//Pour obtenir une couleur aléatoire pour chaque expérimentation et éviter que la couleur change à tous les rafraichissement
if (!isset($_SESSION['experiment_colors']) || !is_array($_SESSION['experiment_colors'])) {
    $_SESSION['experiment_colors'] = [];
}

//Extraire les informations que l'on souhaite de la bdd et les associer à la couleur
$experiments = [];
foreach ($exp as $row) {
    $title = (string) ($row['Nom_Exp'] ?? '');
    $key = (string) ($row['Id_Exp'] ?? $title);
    $id = (string) ($row['Id_Exp'] ?? '');

    if (!isset($_SESSION['experiment_colors'][$key])) {
        $_SESSION['experiment_colors'][$key] = sprintf('#%06X', random_int(0, 0xFFFFFF));
    }

    $experiments[] = [
        'title' => $title,
        'color' => $_SESSION['experiment_colors'][$key],
        'id' => $id,
    ];
}

//Rechercher une expérimentation en particulier
$searchTerm = trim((string) ($_GET['q'] ?? ''));

//Liste des expérimentations en cours qui correspondent à la recherche
$visibleExperiments = array_filter(
    $experiments,
    static fn (array $experiment): bool => $searchTerm === ''
        || stripos($experiment['title'], $searchTerm) !== false
);