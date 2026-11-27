<?php
declare(strict_types=1);

namespace Prismelium\Models;

use PDO;
use RuntimeException;

final class GestionRetributionsModele
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \prismelium_pdo();
    }

    public function pendingForTeam(int $teamId, string $search): array
    {
        $sql =
            'SELECT i.Id_User, i.Id_Ses, u.Nom_User, u.Prenom_User, e.Nom_Exp
             FROM INSCRIPTION i
             INNER JOIN UTILISATEUR u ON u.Id_User = i.Id_User
             INNER JOIN `SESSION` s ON s.Id_Ses = i.Id_Ses
             INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
             WHERE e.Id_Equipe = :team_id
               AND i.Statut_Insc = \'Validee\'
               AND i.Retrib_Valid = 0
               AND s.Statut_Ses = \'Terminee\'
               AND e.Type_Retribution IS NOT NULL
               AND e.Type_Retribution <> \'\'';
        $parameters = ['team_id' => $teamId];

        if ($search !== '') {
            $sql .=
                ' AND (u.Nom_User LIKE :search_last
                       OR u.Prenom_User LIKE :search_first
                       OR u.Mail_User LIKE :search_email
                       OR CONCAT(u.Prenom_User, \' \', u.Nom_User) LIKE :search_full)';
            $term = '%' . $search . '%';
            $parameters += [
                'search_last' => $term,
                'search_first' => $term,
                'search_email' => $term,
                'search_full' => $term,
            ];
        }

        $sql .= ' ORDER BY u.Nom_User, u.Prenom_User, e.Nom_Exp, i.Id_Ses';
        $query = $this->pdo->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function markAsSent(int $teamId, int $researcherId, int $userId, int $sessionId): void
    {
        $this->pdo->beginTransaction();
        try {
            $update = $this->pdo->prepare(
                'UPDATE INSCRIPTION i
                 INNER JOIN `SESSION` s ON s.Id_Ses = i.Id_Ses
                 INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
                 SET i.Retrib_Valid = 1
                 WHERE i.Id_User = :user_id
                   AND i.Id_Ses = :session_id
                   AND i.Statut_Insc = \'Validee\'
                   AND i.Retrib_Valid = 0
                   AND s.Statut_Ses = \'Terminee\'
                   AND e.Type_Retribution IS NOT NULL
                   AND e.Type_Retribution <> \'\'
                   AND e.Id_Equipe = :team_id'
            );
            $update->execute([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'team_id' => $teamId,
            ]);
            if ($update->rowCount() !== 1) {
                throw new RuntimeException('Cette rétribution n’est plus en attente ou n’est pas accessible.');
            }

            $log = $this->pdo->prepare(
                'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                 VALUES (:type, :ip, :user_id)'
            );
            $log->execute([
                'type' => 'Rétribution envoyée',
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                'user_id' => $researcherId,
            ]);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
