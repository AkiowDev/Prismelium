<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - LIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="accueilParticipant.css">
</head>
<body>

    <div class="page">
        <form action="connexionController.php" method="POST">

        <div class="gauche">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="#" aria-label="Accueil">
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
        </div>

        <div class="bienvenue">Bienvenue <?php echo $user['Prenom_User'] ;?></div>
        <div class="page">Page d'accueil</div>

       
       <div class="recherche"><input type="text" id="recherche" name="recherche" placeholder="Rechercher" required></div>
    </div>

</body>
</html>