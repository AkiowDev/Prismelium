<?php
declare(strict_types=1);

namespace Prismelium\Models;

use PDO;

final class UtilisateurModele
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \prismelium_pdo();
    }

    public function findResearcherById(int $userId): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT Id_User, Nom_User, Prenom_User, Id_Equipe, Id_Role
             FROM UTILISATEUR
             WHERE Id_User = :user_id'
        );
        $query->execute(['user_id' => $userId]);
        $user = $query->fetch();

        return $user ?: null;
    }
}
