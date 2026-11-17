<?php
$planningDays = [
    ['date' => 'Mercredi 25 mars 2026', 'events' => '3 événements notés', 'current' => true],
    ['date' => 'Jeudi 26 mars 2026', 'events' => '5 événements notés', 'current' => false],
    ['date' => 'Vendredi 27 mars 2026', 'events' => '4 événements notés', 'current' => false],
    ['date' => 'Samedi 28 mars 2026', 'events' => '99 événements notés', 'current' => false],
];

$recentActivities = [
    [
        'type' => 'experimentation',
        'title' => 'Expérimentation créée',
        'description' => 'Étude sur la mémoire visuelle',
        'time' => 'Aujourd’hui, 10:42',
    ],
    [
        'type' => 'inscription',
        'title' => 'Inscription validée',
        'description' => 'Participation confirmée à une étude',
        'time' => 'Aujourd’hui, 09:18',
    ],
    [
        'type' => 'planning',
        'title' => 'Planning mis à jour',
        'description' => 'Séance du 28 mars ajoutée',
        'time' => 'Hier, 16:30',
    ],
    [
        'type' => 'retribution',
        'title' => 'Rétribution enregistrée',
        'description' => 'Versement ajouté au suivi',
        'time' => 'Hier, 14:05',
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu enseignant-chercheur — Bureau</title>
    <link rel="stylesheet" href="../css/stylesGEDesktop.css">
    <link rel="stylesheet" href="../css/stylesMenuEnseignantChercheurDesktop.css">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="#" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="../assets/brain-logo.png" alt="">
                </a>

                <nav class="barre-laterale__navigation">
                    <a href="#">Rétributions</a>
                    <a href="./GestionExperimentationBureau.php">Expérimentations</a>
                    <a href="#planning">Gestion Planning</a>
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
                                    <p class="planning__date"><?= htmlspecialchars($day['date'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="planning__evenements"><?= htmlspecialchars($day['events'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <img
                                    class="planning__statut"
                                    src="../assets/<?= $day['current'] ? 'planning-status-today.svg' : 'planning-status-inactive.svg' ?>"
                                    alt=""
                                    aria-hidden="true"
                                >
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="menu-enseignant-chercheur__tableau">
                    <header class="menu-enseignant-chercheur__entete">
                        <h1 class="menu-enseignant-chercheur__bienvenue">Bienvenue&nbsp;!</h1>
                    </header>

                    <nav class="actions-menu" aria-label="Accès rapides">
                        <a class="actions-menu__bouton" href="./GestionExperimentationBureau.php">gestion des expérimentations</a>
                        <a class="actions-menu__bouton" href="#">Gérer le planning</a>
                        <a class="actions-menu__bouton" href="#">Gérer les inscriptions</a>
                        <a class="actions-menu__bouton" href="#">Gérer les rétributions</a>
                    </nav>

                    <section class="activites" aria-labelledby="titre-activites-bureau">
                        <div class="activites__entete">
                            <h2 class="activites__titre" id="titre-activites-bureau">Dernières activités&nbsp;:</h2>
                        </div>
                        <div class="activites__panneau" role="region" aria-labelledby="titre-activites-bureau">
                            <ul class="activites__liste">
                                <?php foreach ($recentActivities as $activity): ?>
                                    <li class="activites__element">
                                        <span class="activites__marque activites__marque--<?= htmlspecialchars($activity['type'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></span>
                                        <div class="activites__contenu">
                                            <h3><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                            <p><?= htmlspecialchars($activity['description'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                        <span class="activites__heure"><?= htmlspecialchars($activity['time'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
