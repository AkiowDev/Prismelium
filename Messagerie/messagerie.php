
<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /Prismelium/Connexion/connexionView.php');
    exit;
}

require_once __DIR__ . '/load_database.php';

require_once __DIR__ . '/models/Models.php';

require_once __DIR__ . '/../Connexion/connexionController.php';

$idDestinataire = $_SESSION['user_id'];

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
