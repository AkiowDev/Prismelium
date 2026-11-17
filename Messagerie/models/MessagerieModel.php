
<?php

// Récupérer tous les messages reçus
function recupererMessages($pdo, $idDestinataire)
{
    $requete = $pdo->prepare("
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

        WHERE messagerie.Id_Destinataire = :destinataire

        ORDER BY messagerie.Date_Msg DESC
    ");

    $requete->execute([
        'destinataire' => $idDestinataire
    ]);

    return $requete->fetchAll(PDO::FETCH_ASSOC);
}


// Récupérer un message précis
function recupererMessage($pdo, $idMessage, $idDestinataire)
{
    $requete = $pdo->prepare("
        SELECT
            emetteur.Nom_User AS Nom_Expediteur,
            emetteur.Prenom_User AS Prenom_Expediteur,
            emetteur.Mail_User AS Mail_Expediteur,

            destinataire.Nom_User AS Nom_Destinataire,
            destinataire.Prenom_User AS Prenom_Destinataire,
            destinataire.Mail_User AS Mail_Destinataire,

            messagerie.Id_Msg,
            messagerie.Date_Msg,
            messagerie.Contenu_Msg,
            messagerie.lu_Msg

        FROM messagerie

        INNER JOIN utilisateur AS emetteur
            ON messagerie.Id_Expediteur = emetteur.Id_User

        INNER JOIN utilisateur AS destinataire
            ON messagerie.Id_Destinataire = destinataire.Id_User

        WHERE messagerie.Id_Msg = :id
        AND messagerie.Id_Destinataire = :destinataire
    ");

    $requete->execute([
        'id' => $idMessage,
        'destinataire' => $idDestinataire
    ]);

    return $requete->fetch(PDO::FETCH_ASSOC);
}


// Marquer un message comme lu
function marquerMessageCommeLu($pdo, $idMessage, $idDestinataire)
{
    $requete = $pdo->prepare("
        UPDATE messagerie
        SET lu_Msg = 1
        WHERE Id_Msg = :id
        AND Id_Destinataire = :destinataire
    ");

    $requete->execute([
        'id' => $idMessage,
        'destinataire' => $idDestinataire
    ]);
}

?>
