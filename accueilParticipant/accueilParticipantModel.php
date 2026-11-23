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

function get_mails(){
    $bdd = get_bdd();
    $query = $bdd->query('SELECT Mail_User FROM utilisateur');
    return $query->fetchAll();
}

function get_user_by_mail($mail){
    $bdd = get_bdd();
    $query = $bdd->prepare('SELECT * FROM utilisateur WHERE Mail_User = ?');
    $query->execute([$mail]);
    return $query->fetch();
}

?>