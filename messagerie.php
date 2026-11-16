<?php
require_once 'load_database.php';
require_once 'load_messages.php'
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="messagerie.css">
    <title>Prismelium - Messagerie</title>
</head>
<body class="body_messagerie">
    <div class="barre_laterale">
    <div id="barre_laterale_logo">
        <img
            src="https://cdn-web.univ-lemans.fr/um_images/logos/logo.svg"
            id="logo"
            alt="Logo Le Mans Université"
        >
    </div>
    <nav class="sidebar-menu">

        <a href="#" class="menu-item active">
            <span class="icon"><img src="img/rediger.png" alt="Rédiger"></span>
            <span>Rédiger</span>
        </a>

        <a href="#" class="menu-item">
            <span class="icon"><img src="img/courriel.png" alt="Courriel"></span>
            <span>Courriel</span>
        </a>

        <a href="#" class="menu-item">
            <span class="icon"><img src="img/contacts.png" alt="Contacts"></span>
            <span>Contacts</span>
        </a>

        <a href="#" class="menu-item">
            <span class="icon"><img src="img/parametres.png" alt="Paramètres"></span>
            <span>Paramètres</span>
        </a>

    </nav>
    </div>
    <div class="barre_choix">
        <div class="adresse_utilisateur">
            <p>Prenom.Nom@univ-lemans.fr</p>
        </div>

        <div class="liste_dossiers">

            <div class="dossier actif">
                <span class="icon"><img src="img/reception.png" alt="Boîte de réception"></span>
                <span>Boîte de réception</span>
            </div>

            <div class="dossier">
                <span class="icon"><img src="img/brouillon.png" alt="Brouillons"></span>
                <span>Brouillons</span>
            </div>

            <div class="dossier">
                <span class="icon"><img src="img/envoyes.png" alt="Envoyés"></span>
                <span>Envoyés</span>
            </div>

            <div class="dossier">
                <span class="icon"><img src="img/pourriel.png" alt="Pourriels"></span>
                <span>Pourriels</span>
            </div>

            <div class="dossier">
                <span class="icon"><img src="img/corbeille.png" alt="Corbeille"></span>
                <span>Corbeille</span>
            </div>
        </div>
    </div>
    <div class="barre_messages">

    <!-- Liste des messages -->
    <div class="liste_messages">

    <div class="entete_messages">

        <div class="outil_message">
        <span class="icone_outil">➤</span>
        <span>Sélectionner</span>
        </div>

        <div class="outil_message">
        <span class="icone_outil">●</span>
        <span>Fils</span>
        </div>

        <div class="outil_message">
        <span class="icone_outil">☷</span>
        <span>Options</span>
        </div>

        <div class="outil_message">
        <span class="icone_outil">⟳</span>
        <span>Actualiser</span>
        </div>

    </div>
        <?php foreach ($messages as $message): ?>

            <div class="message">

                <div class="ligne_message">

                    <p class="expediteur">
                        <?= htmlspecialchars($message['Nom_Expediteur'] . ' ' . $message['Prenom_Expediteur'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="date">
                        <?= htmlspecialchars($message['Date_Msg'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </div>

                <p class="objet">
                    • <?= htmlspecialchars($message['Contenu_Msg'], ENT_QUOTES, 'UTF-8') ?>
                </p>

            </div>

        <?php endforeach; ?>

    </div>

</div>

    </div>
    </div>
    <div class="contenu_message">

    <div class="entete_contenu">
        <h2>Réunion de demain</h2>
    </div>

    <div class="informations_message">
        <p><strong>John Doe</strong></p>
        <p>john.doe@univ-lemans.fr</p>
        <p class="date_message">Aujourd'hui, 14:26</p>
    </div>

    <div class="corps_message">
        <p>Bonjour,</p>

        <p>
            Je vous confirme notre réunion de demain à 10h.
            Nous nous retrouverons dans la salle habituelle.
        </p>

        <p>Bonne journée,</p>
        <p>John Doe</p>
    </div>

</div>

</div>
</body>
</html>