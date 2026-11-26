<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$weekOffset = (int) ($_SESSION['planning_week_offset'] ?? 0);

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

$date = new DateTime();
while ($date->format('N') !== '1') {
    $date->sub(new DateInterval('P1D'));
}

if ($weekOffset > 0) {
    $date->modify('+' . ($weekOffset * 7) . ' days');
} elseif ($weekOffset < 0) {
    $date->modify((abs($weekOffset) * 7) . ' days ago');
}

$planningDays = [];
$currentDay = clone $date;

for ($i = 0; $i < 6; $i++) {
    $planningDays[] = $currentDay->format('d/m/Y');
    $currentDay->modify('+1 day');
}