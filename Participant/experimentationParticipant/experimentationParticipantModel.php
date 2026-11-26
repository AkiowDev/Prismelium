<?php
function get_exp_by_id($exp){
    $bdd = get_bdd();
    $query = $bdd->prepare('SELECT * FROM experimentation ex INNER JOIN equipe eq ON ex.Id_Equipe=eq.Id_Equipe WHERE Id_Exp = ?');
    $query->execute([$exp]);
    return $query->fetch();
}

function get_statutinsc_by_user_exp($user, $exp){
    $bdd = get_bdd();
    $query = $bdd->prepare('SELECT Statut_Insc FROM inscription i INNER JOIN session s ON i.Id_Ses=s.Id_Ses WHERE i.Id_User = ? AND s.Id_Exp = ?');
    $query->execute([$user, $exp]);
    return $query->fetchColumn();
}

function get_ses_by_exp($exp){
    $bdd = get_bdd();
    $query = $bdd->prepare('SELECT s.Id_Ses FROM session s LEFT JOIN inscription i ON s.Id_Ses = i.Id_Ses WHERE s.Id_Exp = ? GROUP BY s.Id_Ses, s.Nb_Place_Max HAVING COUNT(i.Id_User) < s.Nb_Place_Max;');
    $query->execute([$exp]);
    return $query->fetchColumn();
}

function insert_insc($user, $exp) {
    $bdd = get_bdd();
    $ses = get_ses_by_exp($exp);
    if ($ses === false || $ses === null) {
        return false;
    }
    $query = $bdd->prepare(
        'INSERT INTO inscription (Id_User, Id_Ses, Statut_Insc, Date_Demande, Retrib_Valid)
         VALUES (?, ?, ?, CURDATE(), 0)'
    );
    return $query->execute([$user, $ses, 'En attente']);
}

?>