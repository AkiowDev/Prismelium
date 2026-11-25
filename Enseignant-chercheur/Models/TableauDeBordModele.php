<?php
declare(strict_types=1);

namespace Prismelium\Models;

use PDO;

final class TableauDeBordModele
{
    public function __construct(private ?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \prismelium_pdo();
    }

    public function getUpcomingPlanning(int $teamId): array
    {
        $query = $this->pdo->prepare(
            'SELECT s.Date_Ses, COUNT(*) AS SessionCount
             FROM `SESSION` s
             INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
             WHERE e.Id_Equipe = :team_id AND s.Date_Ses >= CURRENT_DATE
             GROUP BY s.Date_Ses
             ORDER BY s.Date_Ses
             LIMIT 4'
        );
        $query->execute(['team_id' => $teamId]);

        return $query->fetchAll();
    }

    public function getRecentActivities(int $userId): array
    {
        $query = $this->pdo->prepare(
            'SELECT Type_Log, Date_Log, Erreur_Log
             FROM JOURNAL_LOG
             WHERE Id_User = :user_id
             ORDER BY Date_Log DESC
             LIMIT 4'
        );
        $query->execute(['user_id' => $userId]);

        return $query->fetchAll();
    }
}
