
<?php

require_once 'load_database.php';

// Récupération de l'identifiant du message sélectionné
$message_selectionne = null;
$id_message = null;

if (isset($_GET['message'])) {

    $id_message = filter_input(INPUT_GET, 'message', FILTER_VALIDATE_INT);

    if ($id_message !== false && $id_message !== null && $id_message > 0) {

        // 1. Marquer le message comme lu
        $marquer_lu = $pdo->prepare("
            UPDATE messagerie
            SET lu_Msg = 1
            WHERE Id_Msg = :id
            AND Id_Destinataire = 12
        ");

        $marquer_lu->execute(['id' => $id_message]);

        // 2. Récupérer les informations du message sélectionné
        $requete = $pdo->prepare("
            SELECT
                emetteur.Nom_User AS Nom_Expediteur,
                emetteur.Prenom_User AS Prenom_Expediteur,
                emetteur.Mail_User AS Mail_Expediteur,
                emetteur.Id_User AS Id_Expediteur,

                destinataire.Nom_User AS Nom_Destinataire,
                destinataire.Prenom_User AS Prenom_Destinataire,
                destinataire.Mail_User AS Mail_Destinataire,
                destinataire.Id_User AS Id_Destinataire,

                messagerie.Id_Msg,
                messagerie.Date_Msg,
                messagerie.Contenu_Msg,
                messagerie.Statut_Msg,
                messagerie.lu_Msg

            FROM messagerie

            INNER JOIN utilisateur AS emetteur
                ON messagerie.Id_Expediteur = emetteur.Id_User

            INNER JOIN utilisateur AS destinataire
                ON messagerie.Id_Destinataire = destinataire.Id_User

            WHERE messagerie.Id_Msg = :id
            AND messagerie.Id_Destinataire = 12
        ");

        $requete->execute(['id' => $id_message]);

        $message_selectionne = $requete->fetch(PDO::FETCH_ASSOC);
    }
}

// 3. Récupérer la liste des messages
$messagesSelect = $pdo->query("
    SELECT
        emetteur.Nom_User AS Nom_Expediteur,
        emetteur.Prenom_User AS Prenom_Expediteur,

        destinataire.Nom_User AS Nom_Destinataire,
        destinataire.Prenom_User AS Prenom_Destinataire,

        messagerie.Id_Msg,
        messagerie.Date_Msg,
        messagerie.Contenu_Msg,
        messagerie.lu_Msg

    FROM messagerie

    INNER JOIN utilisateur AS emetteur
        ON messagerie.Id_Expediteur = emetteur.Id_User

    INNER JOIN utilisateur AS destinataire
        ON messagerie.Id_Destinataire = destinataire.Id_User

    WHERE messagerie.Id_Destinataire = 12

    ORDER BY messagerie.Date_Msg DESC
");

$messages = $messagesSelect->fetchAll(PDO::FETCH_ASSOC);

?>
