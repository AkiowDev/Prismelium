<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning - LIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="planningParticipant.css">
</head>
<body>

    <div class="layout-container">
        <aside class="barre-laterale" aria-label="Navigation principale">
            <a class="barre-laterale__marque" href="http://localhost/Prismelium/Participant/accueilParticipant/accueilParticipantController.php" aria-label="Accueil">
                <img class="barre-laterale__logo-cerveau" src="../../images/brain-logo.png" alt="">
            </a>
            <nav class="barre-laterale__navigation">
                <a href="http://localhost/Prismelium/Participant/experimentationsParticipant/experimentationsParticipantController.php">Expérimentations</a>
                <a href="http://localhost/Prismelium/Messagerie/messagerie.php">Messagerie</a>
                <a href="http://localhost/Prismelium/Participant/planningParticipant/planningParticipantController.php">Planning</a>
                <a href="#">Paramètres</a>
            </nav>
            <img class="barre-laterale__logo-universite" src="../../images/university-logo.png" alt="Le Mans Université">
        </aside>
    </div>

    <div class="contenu-principal">
        <div class="title">
            <h1>Planning</h1>
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

    <div class="commandes-planning">

        <div class="selection-experimentation">
            <select id="experimentation" name="experimentation">
                <option value="">Sélectionner une expérimentation</option>
                <option value="exp67">Expérimentation 67.3</option>
                <option value="exp68">Expérimentation 68.2</option>
                <option value="exp69">Expérimentation 69.1</option>
            </select>
        </div>

        <div class="navigation-semaines">

            <form method="GET" action="planningParticipantController.php">
                <button type="submit" name="action" value="semaine-precedente" aria-label="Semaine précédente">
                    &#9664;
                </button>
            </form>
            
            <form method="GET" action="planningParticipantController.php">
                <button type="submit" name="action" value="semaine-suivante" aria-label="Semaine suivante">
                    &#9654;
                </button>
            </form>

        </div>

    </div>

    <main class="grille-planning">

        <?php $dayLabels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']; ?>

        <div class="jours-planning">
            <?php foreach ($dayLabels as $index => $label): ?>
                <div class="jour">
                    <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    <strong><?= htmlspecialchars($planningDays[$index] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grille-creneaux">
        </div>

    </main>

</body>
</html>