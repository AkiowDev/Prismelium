
<?php

function get_bdd() {

    static $bdd = null;

    if ($bdd === null) {

        $hostname = 'localhost';
        $user = 'root';
        $password = '';
        $db_name = 'Prismelium';

        $dsn = "mysql:host=$hostname;dbname=$db_name;charset=utf8mb4";

        try {

            $bdd = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

        } catch (PDOException $e) {

            error_log("Erreur de connexion à la base de données : " . $e->getMessage());

            throw new RuntimeException(
                "Impossible de se connecter à la base de données."
            );
        }
    }

    return $bdd;
}


function get_mails() {

    $bdd = get_bdd();

    $query = $bdd->query(
        'SELECT Mail_User FROM utilisateur'
    );

    return $query->fetchAll();
}


function get_user_by_mail($mail) {

    $bdd = get_bdd();

    $query = $bdd->prepare(
        'SELECT * FROM utilisateur WHERE Mail_User = ?'
    );

    $query->execute([$mail]);

    return $query->fetch();
}

?>