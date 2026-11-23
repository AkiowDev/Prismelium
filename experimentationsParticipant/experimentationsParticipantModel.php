<?php

function get_bdd(){
    $hostname = 'localhost';
    $user = 'root';
    $password = '';
    $db_name = 'Prismelium';

    $dsn = "mysql:host=$hostname;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);

    return $pdo;
}

function get_experimentations_en_cours(){
    $bdd = get_bdd();
    $query = $bdd->query('SELECT Nom_Exp FROM experimentation WHERE Statut_Exp = \'En cours\'');
    return $query->fetchAll();
}

function get_exp_by_nom($exp){
    $bdd = get_bdd();
    $query = $bdd->prepare('SELECT * FROM experimentation WHERE Nom_Exp = ? AND Statut_Exp = \'En cours\'');
    $query->execute([$exp]);
    return $query->fetch();
}

?>