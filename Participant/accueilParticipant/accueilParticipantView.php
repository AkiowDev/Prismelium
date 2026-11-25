<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - LIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="accueilParticipant.css">
</head>
<body>

    <div class="layout-container">
        <aside class="barre-laterale" aria-label="Navigation principale">
            <a class="barre-laterale__marque" href="#" aria-label="Accueil">
                <img class="barre-laterale__logo-cerveau" src="../../images/brain-logo.png" alt="">
            </a>
            <nav class="barre-laterale__navigation">
                <a href="http://localhost/Prismelium/Prismelium/experimentationsParticipant/experimentationsParticipantView.php">Expérimentations</a>
                <a href="#">Messagerie</a>
                <a href="#">Planning</a>
                <a href="#">Paramètres</a>
            </nav>
            <img class="barre-laterale__logo-universite" src="../../images/university-logo.png" alt="Le Mans Université">
        </aside>

        <main class="main">
            <div class="haut-page">
                <div class="bienvenue">
                    <h1>Bienvenue <?php echo htmlspecialchars($user['Prenom_User'] ?? ''); ?></h1>
                </div>
                <div class="profil">
                    <img class="photo-profil"
                        src="../../images/profil.png"
                        alt="Photo de profil utilisateur">
                    <div class="nom_user">
                        <h2><?php echo htmlspecialchars($user['Prenom_User'] ?? ''), " ", htmlspecialchars($user['Nom_User'] ?? ''); ?></h2>
                    </div>
                    <img class="i-button"
                        src="../../images/button_i.png"
                        alt="Bouton i orange">

                </div>
            </div>

            <div class="titre-page">Page d'accueil</div>
            
            <div class="contenu-accueil">
                <section class="planning" id="planning" aria-labelledby="titre-planning-bureau">
                    <h2 class="planning__titre" id="titre-planning-bureau">Planning</h2>

                    <div class="planning__liste">
                        <?php if (!empty($planningDays)): ?>
                            <?php foreach ($planningDays as $day): ?>
                                <article class="planning__jour">
                                    <div class="planning__informations">
                                        <p class="planning__date"><?= htmlspecialchars($day['date'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="planning__evenements"><?= htmlspecialchars($day['events'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                    <img
                                        class="planning__statut"
                                        src="../../images/<?= $day['current'] ? 'planning-status-today.svg' : 'planning-status-inactive.svg' ?>"
                                        alt=""
                                        aria-hidden="true"
                                    >
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
                            
                <div class="cartes-container">
                    <a href="#" class="carte carte--planning">
                        <div class="carte__visuel">
                            <img src="../../images/planning.png" alt="Illustration Planning">
                        </div>
                        <div class="carte__pied">
                            <span class="carte__titre">Planning</span>
                        </div>
                    </a>

                    <a href="#" class="carte carte--experimentation">
                        <div class="carte__visuel">
                            <img src="../../images/experimentations.png" alt="Illustration Prochaine expérimentation">
                        </div>
                        <div class="carte__pied">
                            <span class="carte__titre">Prochaine expérimentation</span>
                        </div>
                    </a>
                </div>
            </div>

        </main>
    </div>

</body>
</html>