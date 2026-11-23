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
            <a href="#">Expérimentations</a>
            <a href="#">Messagerie</a>
            <a href="#">Planning</a>
            <a href="#">Paramètres</a>
        </nav>
        <img class="barre-laterale__logo-universite" src="../images/university-logo.png" alt="Le Mans Université">
    </aside>

    <main class="contenu-principal">

        <section class="section-experiences" aria-labelledby="titre-experiences-bureau">
            <div class="barre-outils-experiences">
                <h2 id="titre-experiences-bureau"><?= $expe['Nom_Exp']?></h2>
            </div>
            <div class="desc">Description : <?= $expe['Desc_Exp']?></div>
            <div class="periode">Période : <?= $expe['Date_Debut_Exp']?> - <?= $expe['Date_Fin_Exp']?> </div>
            <div class="equipe">Projet dirigé par : <?= $expe['Nom_Equipe']?></div>
            <div class="statut_insc">Inscrit : <?= $statut_insc?></div>
            
        </section>
    </main>


</body>
</html>