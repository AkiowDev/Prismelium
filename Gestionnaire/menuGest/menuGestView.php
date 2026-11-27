<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="menuGest.css">
    <title>Prismelium - Gestionnaire</title>
</head>
<body>
    <div class="barre_laterale">

        <a class="barre-laterale__marque" href="#" aria-label="Accueil">
            <img
                class="barre-laterale__logo-cerveau"
                src="../../images/brain-logo.png"
                alt="Logo Prismelium"
            >
        </a>

        <nav class="barre-laterale__navigation" aria-label="Navigation principale">
            <a href="#">Liste des ressources</a>
            <a href="#">Expérimentations</a>
            <a href="#">Gestion Planning</a>
            <a href="http://localhost/Prismelium/Messagerie/messagerie.php">Messagerie</a>
            <a href="#">Paramètres</a>
        </nav>

        <img
            class="barre-laterale__logo-universite"
            src="../../images/university-logo.png"
            alt="Le Mans Université"
        >

    </div>

    <div class="contenu-principal">
        <div class="title">
            <h1>Bienvenue <?php echo htmlspecialchars($user['Prenom_User'] ?? ''); ?></h1>
        </div>
        <div class="profil">
            <img class="photo-profil"
                    src="../../images/profil.png"
                    alt="Photo de profil utilisateur">
                <div class="nom_user">
                    <h2><?php echo htmlspecialchars($user['Prenom_User'] ?? ''), " ", htmlspecialchars($user['Nom_User'] ?? ''); ?></h2>
                </div>
                <a href="../../Messagerie/Deconnexion.php">
                    <img class="i-button"
                        src="../../images/button_i.png"
                        alt="Bouton i orange">
                </a>
        </div>
    </div>

    <div class="indic-page">
        <h2>Page Gestionnaire</h2>
    </div>
            
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
</div>
</body>
</html>