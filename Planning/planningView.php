
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prismelium - Planning</title>
    <link rel="stylesheet" href="planning.css">
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
            <a class="actuel" href="#" aria-current="page">
                Expérimentations
            </a>

            <a href="#">Messagerie</a>
            <a href="#">Gestion Planning</a>
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
            <h1>Gestion du Planning</h1>
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
        <h2>Page planning</h2>
    </div>

    <!-- Commandes du planning -->
    <div class="commandes-planning">

        <!-- Sélection d'une expérimentation -->
        <div class="selection-experimentation">
            <select id="experimentation" name="experimentation">
                <option value="">Sélectionner une expérimentation</option>
                <option value="exp67">Expérimentation 67.3</option>
                <option value="exp68">Expérimentation 68.2</option>
                <option value="exp69">Expérimentation 69.1</option>
            </select>
        </div>

        <!-- Navigation entre les semaines -->
        <div class="navigation-semaines">

            <button type="button" id="semaine-precedente" aria-label="Semaine précédente">
                &#9664;
            </button>

            <button type="button" id="semaine-suivante" aria-label="Semaine suivante">
                &#9654;
            </button>

        </div>

    </div>

    <main class="grille-planning">

            <div class="jours-planning">
                <div class="jour">
                    <span>Tue</span>
                    <strong>9/22</strong>
                </div>

                <div class="jour">
                    <span>Wed</span>
                    <strong>9/23</strong>
                </div>

                <div class="jour">
                    <span>Thu</span>
                    <strong>9/24</strong>
                </div>

                <div class="jour">
                    <span>Fri</span>
                    <strong>9/25</strong>
                </div>

                <div class="jour">
                    <span>Mon</span>
                    <strong>9/28</strong>
                </div>

                <div class="jour">
                    <span>Tue</span>
                    <strong>9/29</strong>
                </div>
            </div>

        <div class="grille-creneaux">
        </div>

    </main>
</body>
</html>
