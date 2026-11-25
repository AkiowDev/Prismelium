<?php
declare(strict_types=1);

namespace Prismelium\Models;

use PDO;
use RuntimeException;

final class InscriptionModele
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \prismelium_pdo();
    }

    public function pendingForTeam(int $teamId): array
    {
        $query = $this->pdo->prepare(
            'SELECT i.Id_User, i.Id_Ses, i.Date_Demande, i.Statut_Insc,
                    u.Nom_User, u.Prenom_User, u.Birth_User, u.Mail_User, u.Tel_User,
                    u.Desc_User, u.Genre_User, u.Lang_User,
                    e.Id_Exp, e.Nom_Exp, s.Date_Ses, s.Debut_Ses, s.Fin_Ses
             FROM INSCRIPTION i
             INNER JOIN UTILISATEUR u ON u.Id_User = i.Id_User
             INNER JOIN `SESSION` s ON s.Id_Ses = i.Id_Ses
             INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
             WHERE e.Id_Equipe = :team_id
               AND i.Statut_Insc IN (\'En attente\', \'Attente gestionnaire\')
             ORDER BY i.Date_Demande ASC, i.Id_Ses ASC'
        );
        $query->execute(['team_id' => $teamId]);

        return $query->fetchAll();
    }

    public function consentDocuments(array $experimentIds): array
    {
        if ($experimentIds === []) {
            return [];
        }
        $placeholders = [];
        $parameters = [];
        foreach (array_values(array_unique($experimentIds)) as $index => $id) {
            $key = 'experiment_' . $index;
            $placeholders[] = ':' . $key;
            $parameters[$key] = $id;
        }
        $query = $this->pdo->prepare(
            'SELECT Id_Exp, Nom_Doc, Type_Doc, Url_Doc
             FROM DOCUMENT
             WHERE Id_Exp IN (' . implode(', ', $placeholders) . ')
               AND (Nom_Doc LIKE :consent_name OR Type_Doc LIKE :consent_type)
             ORDER BY Nom_Doc'
        );
        $query->execute($parameters + [
            'consent_name' => '%consent%',
            'consent_type' => '%consent%',
        ]);

        $documents = [];
        foreach ($query->fetchAll() as $document) {
            $url = (string) $document['Url_Doc'];
            $href = null;
            $prefix = 'storage/documents/';
            if (strncmp($url, $prefix, strlen($prefix)) === 0) {
                $name = substr($url, strlen($prefix));
                if ($name !== '' && basename($name) === $name) {
                    $href = \app_url('storage/documents/' . rawurlencode($name));
                }
            } else {
                $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
                if (in_array($scheme, ['http', 'https'], true)) {
                    $href = $url;
                }
            }
            $documents[(int) $document['Id_Exp']][] = [
                'name' => (string) $document['Nom_Doc'],
                'type' => (string) $document['Type_Doc'],
                'href' => $href,
            ];
        }

        return $documents;
    }

    public function decide(int $teamId, int $managerId, int $userId, int $sessionId, string $decision): void
    {
        $this->pdo->beginTransaction();
        try {
            $update = $this->pdo->prepare(
                'UPDATE INSCRIPTION i
                 INNER JOIN `SESSION` s ON s.Id_Ses = i.Id_Ses
                 INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
                 SET i.Statut_Insc = :status
                 WHERE i.Id_User = :user_id
                   AND i.Id_Ses = :session_id
                   AND i.Statut_Insc IN (\'En attente\', \'Attente gestionnaire\')
                   AND e.Id_Equipe = :team_id'
            );
            $update->execute([
                'status' => $decision === 'accept' ? 'Validee' : 'Refusee',
                'user_id' => $userId,
                'session_id' => $sessionId,
                'team_id' => $teamId,
            ]);
            if ($update->rowCount() !== 1) {
                throw new RuntimeException('Cette demande n’est plus en attente ou n’est pas accessible.');
            }
            $log = $this->pdo->prepare(
                'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                 VALUES (:type, :ip, :user_id)'
            );
            $log->execute([
                'type' => $decision === 'accept' ? 'Validation inscription' : 'Refus inscription',
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                'user_id' => $managerId,
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
