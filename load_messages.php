<?php

require_once 'load_database.php';

$messagesSelect= $pdo->query(
    "SELECT 
    emetteur.Nom_User AS Nom_Expediteur,
    emetteur.Prenom_User AS Prenom_Expediteur,

    destinataire.Nom_User AS Nom_Destinataire,
    destinataire.Prenom_User AS Prenom_Destinataire,

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

?>