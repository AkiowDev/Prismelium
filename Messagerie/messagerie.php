
<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /Prismelium/Connexion/connexionView.php');
    exit;
}

require_once __DIR__ . '/load_database.php';

require_once __DIR__ . '/models/Models.php';


$idDestinataire = $_SESSION['user_id'];
$idUtilisateur = $_SESSION['user_id'];

// Initialisation des variables
$messages = [];
$message_selectionne = null;

$type_messages = $_GET['type'] ?? 'recus';

if (!in_array($type_messages, ['recus', 'envoyes', 'brouillon', 'contacts'], true)) {
    $type_messages = 'recus';
}

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
        $_SESSION['user_id'],
        $type_messages
    );

    if ($message_selectionne && $type_messages === 'recus') {

    marquerMessageCommeLu(
        $pdo,
        $idMessage,
        $idDestinataire
    );

    $message_selectionne['lu_Msg'] = 1;
    }
}

    // Récupérer la liste des messages
    if ($type_messages === 'envoyes') {
        $messages = recupererMessagesEnvoyes($pdo, $_SESSION['user_id']);
    } else if ($type_messages === 'recus'){
        $messages = recupererMessages($pdo, $_SESSION['user_id']);
    } else if ($type_messages === 'brouillon'){
        $messages = recupererBrouillons($pdo, $_SESSION['user_id']);
    } 

// Affichage de la vue
require __DIR__ . '/views/MessagerieView.php';

?>