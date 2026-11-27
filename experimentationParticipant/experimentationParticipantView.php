<?php require_once 'experimentationParticipantController.php'; ?>

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
        <a class="barre-laterale__marque" href="http://localhost/Prismelium/Prismelium/accueilParticipant/accueilParticipantView.php" aria-label="Accueil">
            <img class="barre-laterale__logo-cerveau" src="../images/brain-logo.png" alt="">
        </a>
        <nav class="barre-laterale__navigation">
            <a href="http://localhost/Prismelium/Prismelium/experimentationsParticipant/experimentationsParticipantView.php">Expérimentations</a>
            <a href="#">Messagerie</a>
            <a href="http://localhost/Prismelium/Prismelium/planningParticipant/planningParticipantView.php">Planning</a>
            <a href="#">Paramètres</a>
        </nav>
        <img class="barre-laterale__logo-universite" src="../images/university-logo.png" alt="Le Mans Université">
    </aside>

    <main class="contenu-principal">

        <section class="section-experience" aria-labelledby="titre-experiences-bureau">
            <div class="haut-de-page">
                <div class="titre-page">
                    <h2><?= $expe['Nom_Exp']?></h2>
                </div>
                <div class="profil">
                    <img class="photo-profil"
                        src="../images/profil.png"
                        alt="Photo de profil utilisateur">
                    <div class="nom_user">
                        <h2><?php echo htmlspecialchars($user['Prenom_User'] ?? ''), " ", htmlspecialchars($user['Nom_User'] ?? ''); ?></h2>
                    </div>
                    <img class="i-button"
                        src="../images/button_i.png"
                        alt="Bouton i orange">
                </div>
            </div>
            <div class="desc">Description : <?= $expe['Desc_Exp']?></div>
            <div class="periode">Période : <?= $expe['Date_Debut_Exp']?> - <?= $expe['Date_Fin_Exp']?> </div>
            <div class="equipe">Projet dirigé par : <?= $expe['Nom_Equipe']?></div>
            <div class="statut_insc">Inscrit : <?= $statut_insc?></div>

            <?php if (trim((string) $statut_insc) === 'Non'): ?>
                <form method="POST" action="experimentationParticipantController.php">
                    <input type="hidden" name="id_exp" value="<?= htmlspecialchars((string) $exp_id, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" name="action" value="inscription" class="btn-inscription">
                        Inscription
                    </button>
                </form>
            <?php endif; ?>

            <?php if (trim((string) $statut_insc) !== 'Non'): ?>
                <form method="POST" action="experimentationParticipantController.php">
                    <input type="hidden" name="id_exp" value="<?= htmlspecialchars((string) $exp_id, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" name="action" value="questionnaire" class="btn-questionnaire">
                        Questionnaire
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>