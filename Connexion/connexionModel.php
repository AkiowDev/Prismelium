<?php
declare(strict_types=1);

namespace Prismelium\Models {
    use PDO;

    final class ConnexionModel
    {
        private PDO $pdo;

        public function __construct(?PDO $pdo = null)
        {
            if ($pdo === null && !function_exists('prismelium_pdo')) {
                require_once __DIR__ . '/../load_database.php';
            }

            $this->pdo = $pdo ?? \prismelium_pdo();
        }

        public function get_user_by_mail(string $email): array|false
        {
            $query = $this->pdo->prepare(
                'SELECT * FROM UTILISATEUR WHERE Mail_User = :email'
            );
            $query->execute(['email' => $email]);

            return $query->fetch(PDO::FETCH_ASSOC);
        }

        public function get_mails(): array
        {
            return $this->pdo->query('SELECT Mail_User FROM UTILISATEUR')->fetchAll();
        }
    }
}

namespace {
    function get_bdd(): PDO
    {
        require_once __DIR__ . '/../load_database.php';

        return \prismelium_pdo();
    }

    function get_user_by_mail(string $email): array|false
    {
        return (new \Prismelium\Models\ConnexionModel())->get_user_by_mail($email);
    }

    function get_mails(): array
    {
        return (new \Prismelium\Models\ConnexionModel())->get_mails();
    }
}