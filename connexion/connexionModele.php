<?php
declare(strict_types=1);

namespace Prismelium\Models;

use PDO;

final class ConnexionModele
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \prismelium_pdo();
    }

    public function get_user_by_mail(string $email): array|false
    {
        $query = $this->pdo->prepare(
            'SELECT * FROM UTILISATEUR WHERE Mail_User = :email'
        );
        $query->execute(['email' => $email]);

        return $query->fetch();
    }

    public function get_mails(): array
    {
        return $this->pdo->query('SELECT Mail_User FROM UTILISATEUR')->fetchAll();
    }
}
