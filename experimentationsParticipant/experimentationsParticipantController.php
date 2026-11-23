<?php
session_start();
require_once 'experimentationsParticipantModel.php';

$exp = get_experimentations_en_cours();

if (!isset($_SESSION['experiment_colors']) || !is_array($_SESSION['experiment_colors'])) {
    $_SESSION['experiment_colors'] = [];
}

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

$searchTerm = trim((string) ($_GET['q'] ?? ''));

$visibleExperiments = array_filter(
    $experiments,
    static fn (array $experiment): bool => $searchTerm === ''
        || stripos($experiment['title'], $searchTerm) !== false
);