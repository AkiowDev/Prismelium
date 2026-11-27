<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu enseignant-chercheur — Bureau</title>
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGEDesktop.css?v=2') ?>">
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesMenuEnseignantChercheurDesktop.css?v=2') ?>">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="<?= app_url('index.php?page=enseignant-chercheur') ?>" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="<?= app_url('images/brain-logo.png') ?>" alt="">
                </a>
                <nav class="barre-laterale__navigation">
                    <a href="<?= app_url('index.php?page=retributions') ?>">Rétributions</a>
                    <a href="<?= app_url('index.php?page=experimentations') ?>">Expérimentations</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>
                <img class="barre-laterale__logo-universite" src="<?= app_url('images/university-logo.png') ?>" alt="Le Mans Université">
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
                                <img class="planning__statut" src="<?= app_url('images/' . ($day['current'] ? 'planning-status-today.svg' : 'planning-status-inactive.svg')) ?>" alt="" aria-hidden="true">
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
                        <a class="actions-menu__bouton" href="<?= app_url('index.php?page=experimentations') ?>">gestion des expérimentations</a>
                        <a class="actions-menu__bouton" href="#">Gérer le planning</a>
                        <a class="actions-menu__bouton" href="<?= app_url('index.php?page=inscriptions') ?>">Gérer les inscriptions</a>
                        <a class="actions-menu__bouton" href="<?= app_url('index.php?page=retributions') ?>">Gérer les rétributions</a>
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
                                            <h3><?= htmlspecialchars($activity['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h3>
                                            <p><?= htmlspecialchars($activity['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                                        </div>
                                        <span class="activites__heure"><?= htmlspecialchars($activity['time'], ENT_QUOTES, 'UTF-8') ?></span>
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
