<?php

require_once 'load_database.php';

$messagesSelect= $pdo->query(
    "SELECT 
    emetteur.Nom_User AS Nom_Expediteur,
    emetteur.Prenom_User AS Prenom_Expediteur,

    destinataire.Nom_User AS Nom_Destinataire,
    destinataire.Prenom_User AS Prenom_Destinataire,

    messagerie.Id_Msg,
    messagerie.Date_Msg,
    messagerie.Contenu_Msg

    FROM messagerie

    INNER JOIN utilisateur AS emetteur
    ON messagerie.Id_Expediteur = emetteur.Id_User

    INNER JOIN utilisateur AS destinataire
    ON messagerie.Id_Destinataire = destinataire.Id_User

    WHERE messagerie.Id_Destinataire = 12

    ORDER BY messagerie.Date_Msg DESC;"
);


$messages = $messagesSelect->fetchAll(PDO::FETCH_ASSOC);

$message_selectionne = null;

if (isset($_GET['message'])) {


    $id_message = filter_input(INPUT_GET, 'message', FILTER_VALIDATE_INT);

    if ($id_message !== false && $id_message !== null && $id_message > 0) {

        $requete = $pdo->prepare("
            SELECT
                messagerie.*,
                utilisateur.Nom_User AS Nom_Expediteur,
                utilisateur.Prenom_User AS Prenom_Expediteur,
                utilisateur.Mail_User AS Mail_Expediteur
            FROM messagerie
            INNER JOIN utilisateur
                ON utilisateur.Id_User = messagerie.Id_Expediteur
            WHERE messagerie.Id_Msg = :id
        ");

        $requete->execute(['id' => $id_message]);

        $message_selectionne = $requete->fetch(PDO::FETCH_ASSOC);
    }
}
?>