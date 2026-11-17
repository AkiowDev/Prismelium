
<?php
/** @var array $messages */
/** @var array|null $message_selectionne */
/** @var string $type_messages */
/** @var string|null $page */
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="messagerie.css">

    <title>Prismelium - Messagerie</title>
</head>

<body class="body_messagerie">

    <!-- PREMIÈRE COLONNE : BARRE LATÉRALE -->

    <div class="barre_laterale">

        <div id="barre_laterale_logo">
            <img
                src="https://cdn-web.univ-lemans.fr/um_images/logos/logo.svg"
                id="logo"
                alt="Logo Le Mans Université"
            >
        </div>

        <nav class="sidebar-menu">

            <a href="rediger.php" class="menu-item active">
                <span class="icon">
                    <img src="img/rediger.png" alt="Rédiger">
                </span>
                <span>Rédiger</span>
            </a>

            <a href="contacts.php" class="menu-item">
                <span class="icon">
                    <img src="img/contacts.png" alt="Contacts">
                </span>
                <span>Contacts</span>
            </a>

            <a href="#" class="menu-item">
                <span class="icon">
                    <img src="img/parametres.png" alt="Paramètres">
                </span>
                <span>Paramètres</span>
            </a>

            <a href="Deconnexion.php" class="menu-item">
                <span class="icon">
                    <img src="img/Deconnexion.png" alt="Deconnexion">
                </span>
                <span>Deconnexion</span>
            </a>

        </nav>

    </div>


    <!-- DEUXIÈME COLONNE : DOSSIERS -->

    <div class="barre_choix">

        <div class="adresse_utilisateur">
            <p><strong><?= htmlspecialchars($_SESSION['user_mail'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        </div>

        <div class="liste_dossiers">

            <a href="messagerie.php?type=recus"
                class="dossier <?= $type_messages === 'recus' ? 'actif' : '' ?>">
                <span class="icon">
                    <img src="img/reception.png" alt="Boîte de réception">
                </span>
                <span>Boîte de réception</span>
            </a>

            <a href="messagerie.php?type=brouillon"
                class="dossier <?= $type_messages === 'brouillon' ? 'actif' : '' ?>">
                <span class="icon">
                    <img src="img/brouillon.png" alt="Brouillon">
                </span>
                <span>Brouillons</span>
            </a>

            <a href="messagerie.php?type=envoyes"
            class="dossier <?= $type_messages === 'envoyes' ? 'actif' : '' ?>">
                <span class="icon">
                    <img src="img/envoyes.png" alt="Envoyés">
                </span>
                <span>Envoyés</span>
            </a>


            <div class="dossier">
                <span class="icon">
                    <img src="img/pourriel.png" alt="Pourriels">
                </span>
                <span>Pourriels</span>
            </div>

            <div class="dossier">
                <span class="icon">
                    <img src="img/corbeille.png" alt="Corbeille">
                </span>
                <span>Corbeille</span>
            </div>

        </div>

    </div>


    <!-- TROISIÈME COLONNE : LISTE DES MESSAGES -->

    <div class="barre_messages">

        <div class="entete_messages">
        </div>

        <div class="liste_messages">

            <?php foreach ($messages as $message): ?>

                <a
                    href="messagerie.php?type=<?= htmlspecialchars($type_messages, ENT_QUOTES, 'UTF-8') ?>&message=<?= (int)$message['Id_Msg'] ?>"
                    class="message <?= (int)$message['lu_Msg'] === 0 ? 'non_lu' : 'lu' ?>"
                >

                    <div class="ligne_message">

                        <p class="expediteur">
                            <?php
                                if ($type_messages === 'envoyes' || $type_messages === 'brouillon') {
                                    echo htmlspecialchars(
                                        $message['Prenom_Destinataire'] . ' ' . $message['Nom_Destinataire'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                } else {
                                    echo htmlspecialchars(
                                        $message['Prenom_Expediteur'] . ' ' . $message['Nom_Expediteur'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                }
                                
                            ?>
                        </p>

                    </div>

                    <p class="objet">
                        • <?= htmlspecialchars(
                            $message['Contenu_Msg'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </a>

            <?php endforeach; ?>

        </div>

    </div>



<!-- QUATRIÈME COLONNE : CONTENU DU MESSAGE -->

<div class="contenu_message">

    <div class="entete_contenu">
            <h2>
                <?php
                if (($page ?? '') === 'contacts') {
                    echo 'Contacts';
                } elseif ($type_messages === 'envoyes') {
                    echo 'Messages envoyés';
                } elseif ($type_messages === 'brouillon') {
                    echo 'Brouillons';
                } else {
                    echo 'Boîte de réception';
                }
                ?>
            </h2>
        </div>


    <?php if (($page ?? '') === 'contacts'): ?>

        <?php require __DIR__ . '/ContactsView.php'; ?>

    <?php else: ?>

        
        <?php if ($message_selectionne): ?>

            <div class="informations_message">

                <p>
                    <strong>
                        De :
                        <?= htmlspecialchars(
                            $message_selectionne['Nom_Expediteur'] . ' ' . $message_selectionne['Prenom_Expediteur'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </p>

                <p>
                    <?= htmlspecialchars($message_selectionne['Mail_Expediteur'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <?= htmlspecialchars($message_selectionne['Date_Msg'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>
                        À :
                        <?= htmlspecialchars(
                            $message_selectionne['Nom_Destinataire'] . ' ' . $message_selectionne['Prenom_Destinataire'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </p>

            </div>

            <div class="corps_message">
                <p>
                    <?= nl2br(htmlspecialchars(
                        $message_selectionne['Contenu_Msg'],
                        ENT_QUOTES,
                        'UTF-8'
                    )) ?>
                </p>
            </div>

        <?php else: ?>

            <div class="pas_de_message">
                <img
                    src="img/lmu_messVide.png"
                    id="messagerie_vide"
                    alt="Logo Le Mans Université"
                >
            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>


</body>
</html>
