

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expérimentation — LIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="experimentationParticipant.css">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
    <aside class="barre-laterale" aria-label="Navigation principale">
        <a class="barre-laterale__marque" href="../accueilParticipant/accueilParticipantController.php" aria-label="Accueil">
            <img class="barre-laterale__logo-cerveau" src="../../images/brain-logo.png" alt="">
        </a>
        <nav class="barre-laterale__navigation">
            <a href="../experimentationsParticipant/experimentationsParticipantController.php">Expérimentations</a>
            <a href="../../Messagerie/messagerie.php">Messagerie</a>
            <a href="../planningParticipant/planningParticipantController.php">Planning</a>
            <a href="#">Paramètres</a>
        </nav>
        <img class="barre-laterale__logo-universite" src="../../images/university-logo.png" alt="Le Mans Université">
    </aside>

    <main class="contenu-principal">

        <section class="section-experience" aria-labelledby="titre-experiences-bureau">
            <div class="titre-page">
                <h2><?= htmlspecialchars($expe['Nom_Exp'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <div class="desc">Description : <?= htmlspecialchars($expe['Desc_Exp'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="periode">Période : <?= htmlspecialchars($expe['Date_Debut_Exp'] ?? '', ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($expe['Date_Fin_Exp'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="equipe">Projet dirigé par : <?= htmlspecialchars($expe['Nom_Equipe'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="statut_insc">Inscrit : <?= htmlspecialchars((string) $statut_insc, ENT_QUOTES, 'UTF-8') ?></div>

            <?php if (trim((string) $statut_insc) === 'Non'): ?>
                <form method="POST" action="experimentationParticipantController.php">
                    <input type="hidden" name="id_exp" value="<?= htmlspecialchars((string) $exp_id, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" name="action" value="inscription" class="btn-inscription">
                        Inscription
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>
        </div>
    </div>
</body>
</html>