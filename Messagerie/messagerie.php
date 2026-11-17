
<?php

// Connexion à la base de données
require_once __DIR__ . '/load_database.php';

// Chargement du modèle
require_once __DIR__ . '/models/Models.php';

// Identifiant du destinataire (temporaire)
$idDestinataire = 12;

// Initialisation des variables
$messages = [];
$message_selectionne = null;

// Vérification du message sélectionné
$idMessage = filter_input(
    INPUT_GET,
    'message',
    FILTER_VALIDATE_INT
);

if ($idMessage !== false && $idMessage !== null && $idMessage > 0) {

    // Vérifier que le message appartient au destinataire
    $message_selectionne = recupererMessage(
        $pdo,
        $idMessage,
        $idDestinataire
    );

    if ($message_selectionne) {

        // Marquer le message comme lu
        marquerMessageCommeLu(
            $pdo,
            $idMessage,
            $idDestinataire
        );

        // Actualiser son statut dans les données affichées
        $message_selectionne['lu_Msg'] = 1;
    }
}

// Récupérer la liste des messages
$messages = recupererMessages(
    $pdo,
    $idDestinataire
);

// Affichage de la vue
require __DIR__ . '/views/MessagerieView.php';

?>
