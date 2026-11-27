<?php require_once 'questionnaireParticipantController.php' ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Questionnaire - LIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="questionnaireParticipant.css">
</head>
<body>

    <div class="layout-container">
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
    </div>

    <div class="contenu-principal">
        <div class="title">
            <h1>Questionnaire : Nom_Exp</h1>
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

</body>
</html>