<?php

require_once __DIR__ . '/../BDD_control/load_database.php';

function get_bdd(): PDO
{
    return prismelium_pdo();
}

function get_mails(): array
{
    $bdd = get_bdd();
    $query = $bdd->query('SELECT Mail_User FROM utilisateur');
    return $query->fetchAll();
}

function get_user_by_mail(string $mail): array|false
{
    $bdd = get_bdd();
    $query = $bdd->prepare('SELECT * FROM utilisateur WHERE Mail_User = :mail');
    $query->execute(['mail' => $mail]);
    return $query->fetch();
}

?>