<?php
declare(strict_types=1);

require_once __DIR__ . '/../BDD_control/current_researcher.php';

$planningQuery = $pdo->prepare(
    'SELECT s.Date_Ses, COUNT(*) AS SessionCount
     FROM `SESSION` s
     INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
     WHERE e.Id_Equipe = :team_id AND s.Date_Ses >= CURRENT_DATE
     GROUP BY s.Date_Ses
     ORDER BY s.Date_Ses
     LIMIT 4'
);
$planningQuery->execute(['team_id' => $currentTeamId]);
$planningDays = [];
foreach ($planningQuery->fetchAll() as $day) {
    $dayDate = new DateTimeImmutable($day['Date_Ses']);
    $planningDays[] = [
        'date' => $dayDate->format('d/m/Y'),
        'events' => (int) $day['SessionCount'] . ' séance(s)',
        'current' => $dayDate->format('Y-m-d') === date('Y-m-d'),
    ];
}

$activityQuery = $pdo->prepare(
    'SELECT Type_Log, Date_Log, Erreur_Log
     FROM JOURNAL_LOG
     WHERE Id_User = :user_id
     ORDER BY Date_Log DESC
     LIMIT 4'
);
$activityQuery->execute(['user_id' => (int) $currentUser['Id_User']]);
$recentActivities = [];
foreach ($activityQuery->fetchAll() as $activity) {
    $type = (string) $activity['Type_Log'];
    $normalizedType = strtolower($type);
    $category = str_contains($normalizedType, 'inscription')
        ? 'inscription'
        : (str_contains($normalizedType, 'retribution') || str_contains($normalizedType, 'rétribution')
            ? 'retribution'
            : (str_contains($normalizedType, 'planning') || str_contains($normalizedType, 'séance')
                ? 'planning'
                : 'experimentation'));

    $recentActivities[] = [
        'type' => $category,
        'title' => $type,
        'description' => $activity['Erreur_Log'] ?: 'Activité enregistrée pour votre compte.',
        'time' => (new DateTimeImmutable($activity['Date_Log']))->format('d/m/Y H:i'),
    ];
}

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu enseignant-chercheur — Bureau</title>
    <link rel="stylesheet" href="../css/stylesGEDesktop.css?v=2">
    <link rel="stylesheet" href="../css/stylesMenuEnseignantChercheurDesktop.css?v=2">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="./MenuEnseignantChercheurBureau.php" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="../assets/brain-logo.png" alt="">
                </a>

                <nav class="barre-laterale__navigation">
                    <a href="#">Rétributions</a>
                    <a href="./GestionExperimentationBureau.php">Expérimentations</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>

                <img class="barre-laterale__logo-universite" src="../assets/university-logo.png" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal menu-enseignant-chercheur">
                <section class="planning" id="planning" aria-labelledby="titre-planning-bureau">
                    <h2 class="planning__titre" id="titre-planning-bureau">Planning</h2>

                    <div class="planning__liste">
                        <?php foreach ($planningDays as $day): ?>
                            <article class="planning__jour">
                                <div class="planning__informations">
                                    <p class="planning__date"><?= $escape($day['date']) ?></p>
                                    <p class="planning__evenements"><?= $escape($day['events']) ?></p>
                                </div>
                                <img
                                    class="planning__statut"
                                    src="../assets/<?= $day['current'] ? 'planning-status-today.svg' : 'planning-status-inactive.svg' ?>"
                                    alt=""
                                    aria-hidden="true"
                                >
                            </article>
                        <?php endforeach; ?>
                        <?php if ($planningDays === []): ?>
                            <p class="planning__vide">Aucune séance à venir.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <div class="menu-enseignant-chercheur__tableau">
                    <header class="menu-enseignant-chercheur__entete">
                        <h1 class="menu-enseignant-chercheur__bienvenue">Bienvenue !</h1>
                    </header>

                    <nav class="actions-menu" aria-label="Accès rapides">
                        <a class="actions-menu__bouton" href="./GestionExperimentationBureau.php">gestion des expérimentations</a>
                        <a class="actions-menu__bouton" href="#">Gérer le planning</a>
                        <a class="actions-menu__bouton" href="#">Gérer les inscriptions</a>
                        <a class="actions-menu__bouton" href="#">Gérer les rétributions</a>
                    </nav>

                    <section class="activites" aria-labelledby="titre-activites-bureau">
                        <div class="activites__entete">
                            <h2 class="activites__titre" id="titre-activites-bureau">Dernières activités:</h2>
                        </div>
                        <div class="activites__panneau" role="region" aria-labelledby="titre-activites-bureau">
                            <ul class="activites__liste">
                                <?php foreach ($recentActivities as $activity): ?>
                                    <li class="activites__element">
                                        <span class="activites__marque activites__marque--<?= htmlspecialchars($activity['type'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></span>
                                        <div class="activites__contenu">
                                            <h3><?= $escape($activity['title']) ?></h3>
                                            <p><?= $escape($activity['description']) ?></p>
                                        </div>
                                        <span class="activites__heure"><?= $escape($activity['time']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if ($recentActivities === []): ?>
                                    <li class="activites__vide">Aucune activité enregistrée pour le moment.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
