
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
        AND messagerie.Statut_Msg = 'envoye'

        ORDER BY messagerie.Date_Msg DESC
    ");

    $requete->execute([
        'destinataire' => $idDestinataire
    ]);
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function recupererBrouillons($pdo, $idDestinataire)
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

        WHERE messagerie.Id_Expediteur = :emetteur
        AND messagerie.Statut_Msg = 'brouillon'

        ORDER BY messagerie.Date_Msg DESC
    ");

    $requete->execute([
        'emetteur' => $idDestinataire
    ]);
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function recupererMessagesEnvoyes($pdo, $idExpediteur)
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

    WHERE messagerie.Id_Expediteur = :expediteur
    AND messagerie.Statut_Msg = 'envoye'

    ORDER BY messagerie.Date_Msg DESC
    ");

    $requete->execute([
        'expediteur' => $idExpediteur
    ]);

    return $requete->fetchAll(PDO::FETCH_ASSOC);
}


function recupererMessage($pdo, $idMessage, $idUtilisateur, $type_messages)
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
        AND (
            (:type = 'brouillon' AND messagerie.Statut_Msg = 'brouillon')
            OR
            (:type IN ('recus', 'envoyes') AND messagerie.Statut_Msg = 'envoye')
        )
        AND (
        (:type = 'recus' AND messagerie.Id_Destinataire = :utilisateur)
        OR
        (:type = 'envoyes' AND messagerie.Id_Expediteur = :utilisateur)
        OR
        (:type = 'brouillon' AND messagerie.Id_Expediteur = :utilisateur)
        )
    ");

    $requete->execute([
        'id' => $idMessage,
        'type' => $type_messages,
        'utilisateur' => $idUtilisateur
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

function rechercherDestinataires($pdo, $recherche)
{
    $requete = $pdo->prepare("
        SELECT Id_User, Mail_User
        FROM UTILISATEUR
        WHERE Mail_User LIKE :recherche
        ORDER BY Mail_User ASC
    ");

    $requete->execute([
        'recherche' => '%' . $recherche . '%'
    ]);

    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function envoyerMessage($pdo, $idExpediteur, $idDestinataire, $contenu)
{
    $requete = $pdo->prepare("
        INSERT INTO messagerie (Id_Expediteur, Id_Destinataire, Contenu_Msg, Statut_Msg)
        VALUES (:expediteur, :destinataire, :contenu, 'envoye')
    ");

    $requete->execute([
        'expediteur' => $idExpediteur,
        'destinataire' => $idDestinataire,
        'contenu' => $contenu
    ]);
}

function enregistrerBrouillon($pdo, $idExpediteur, $idDestinataire, $contenu)
{
    $requete = $pdo->prepare("
        INSERT INTO messagerie (Id_Expediteur, Id_Destinataire, Contenu_Msg, Statut_Msg)
        VALUES (:expediteur, :destinataire, :contenu, 'brouillon')
    ");

    $requete->execute([
        'expediteur' => $idExpediteur,
        'destinataire' => $idDestinataire,
        'contenu' => $contenu
    ]);
}

function getDestinataire($pdo, $id_destinataire)
{
    $requete = $pdo->prepare("
        SELECT Prenom_User, Nom_User
        FROM UTILISATEUR
        WHERE Id_User = :id
    ");

    $requete->execute([
        'id' => $id_destinataire
    ]);

    return $requete->fetch(PDO::FETCH_ASSOC);
}


// Récupérer et rechercher les contacts
function recupererContacts($pdo, $recherche = '')
{
    $requete = $pdo->prepare("
        SELECT Id_User, Nom_User, Prenom_User, Mail_User
        FROM UTILISATEUR
        WHERE Nom_User LIKE :recherche_nom
        OR Prenom_User LIKE :recherche_prenom
        OR Mail_User LIKE :recherche_mail
        ORDER BY Nom_User ASC, Prenom_User ASC
    ");

    $recherche = '%' . $recherche . '%';

    $requete->execute([
        'recherche_nom' => $recherche,
        'recherche_prenom' => $recherche,
        'recherche_mail' => $recherche
    ]);

    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

?>