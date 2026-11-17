
<?php

require_once __DIR__ . '/load_database.php';

// Chargement du modèle
require_once __DIR__ . '/models/Models.php';

session_start();

$idExpediteur = $_SESSION['user_id'];

$recherche = '';
$utilisateurs = [];
$id_destinataire = null;

if (isset($_POST['rechercher'])) {

    $recherche = trim($_POST['mail_destinataire'] ?? '');

    if ($recherche !== '') {

        $utilisateurs = rechercherDestinataires(
            $pdo,
            $recherche
        );
    }
}

if (isset($_POST['selectionner'])) {

    $id_destinataire = filter_input(
        INPUT_POST,
        'id_destinataire',
        FILTER_VALIDATE_INT
    );

    if ($id_destinataire === false || $id_destinataire <= 0) {
        $id_destinataire = null;
    }

    $destinataire = null;

    if ($id_destinataire !== null) {

    $destinataire = getDestinataire(
        $pdo,
        $id_destinataire
    );
}
}

if(isset($_POST['envoyer'])) {

    $id_destinataire = filter_input(
        INPUT_POST,
        'id_destinataire',
        FILTER_VALIDATE_INT
    );

    $contenu = trim($_POST['contenu'] ?? '');

    if ($id_destinataire !== false && $id_destinataire > 0 && $contenu !== '') {

        envoyerMessage(
            $pdo,
            $idExpediteur,
            $id_destinataire,
            $contenu
        );

        // Réinitialiser les variables après l'envoi
        $id_destinataire = null;
        $contenu = '';
    }

    header('Location: messagerie.php');
    exit;
}

if(isset($_POST['brouillon'])) {

    $id_destinataire = filter_input(
        INPUT_POST,
        'id_destinataire',
        FILTER_VALIDATE_INT
    );

    $contenu = trim($_POST['contenu'] ?? '');

    if ($id_destinataire !== false && $id_destinataire > 0 && $contenu !== '') {

        enregistrerBrouillon(
            $pdo,
            $idExpediteur,
            $id_destinataire,
            $contenu
        );

        // Réinitialiser les variables après l'enregistrement du brouillon
        $id_destinataire = null;
        $contenu = '';
    }

    header('Location: messagerie.php');
    exit;
}

require __DIR__ . '/views/RedigerView.php';

?>
