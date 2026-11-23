<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="gestionnaire.css">
    <title>Prismelium - Gestionnaire</title>
</head>
<body>
    <div class="barre_laterale">

        <a class="barre-laterale__marque" href="#" aria-label="Accueil">
            <img
                class="barre-laterale__logo-cerveau"
                src="img/brain-logo.png"
                alt="Logo Prismelium"
            >
        </a>

        <nav class="barre-laterale__navigation" aria-label="Navigation principale">
            <a href="#">Liste des ressources</a>
            <a href="#">Expérimentations</a>
            <a href="#">Gestion Planning</a>
            <a href="#">Messagerie</a>
            <a href="#">Paramètres</a>
        </nav>

        <img
            class="barre-laterale__logo-universite"
            src="img/university-logo.png"
            alt="Le Mans Université"
        >

    </div>

    <div class="contenu-principal">
        <div class="title">
            <h1>Bienvenue John !</h1>
        </div>
        <div class="profil">
            <img class="photo-profil"
                src="img/profil.png"
                alt="Photo de profil utilisateur">
            <div class="nom_user">
                <h2>John Doe</h2>
            </div>
            <img class="i-button"
                src="img/button_i.png"
                alt="Bouton i orange">
        </div>
    </div>

    <div class="indic-page">
        <h2>Page Gestionnaire</h2>
    </div>

    <div class="planning">

    <div class="planning__entete">
        <h2>Planning</h2>
        <button class="planning__bouton" type="button">
            →
        </button>
    </div>

    <div class="planning__evenements">

        <div class="planning__jour">
            <div>
                <p>Mercredi 1 octobre 2026</p>
                <span>3 événements notés</span>
            </div>
            <div class="planning__statut planning__statut--actif"></div>
        </div>

        <div class="planning__jour">
            <div>
                <p>Jeudi 2 octobre 2026</p>
                <span>5 événements notés</span>
            </div>
            <div class="planning__statut"></div>
        </div>

        <div class="planning__jour">
            <div>
                <p>Vendredi 3 octobre 2026</p>
                <span>4 événements notés</span>
            </div>
            <div class="planning__statut"></div>
        </div>

        <div class="planning__jour">
            <div>
                <p>Samedi 4 octobre 2026</p>
                <span>0 événements notés</span>
            </div>
            <div class="planning__statut"></div>
        </div>

    </div>

</div>
</body>
</html>