<?php

function get_experimentations_en_cours(){
    $bdd = get_bdd();
    $query = $bdd->query("SELECT Id_Exp, Nom_Exp FROM experimentation WHERE Statut_Exp = 'En cours'");
    return $query->fetchAll();
}

function get_exp_by_nom($exp){
    $bdd = get_bdd();
    $query = $bdd->prepare("SELECT * FROM experimentation WHERE Nom_Exp = ? AND Statut_Exp = 'En cours'");
    $query->execute([$exp]);
    return $query->fetch();
}

?>