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
    return $query->fetch();
}

?>