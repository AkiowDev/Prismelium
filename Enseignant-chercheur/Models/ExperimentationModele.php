<?php
declare(strict_types=1);

namespace Prismelium\Models;

use PDO;
use RuntimeException;

final class ExperimentationModele
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \prismelium_pdo();
    }

    public function findForTeam(int $experimentId, int $teamId): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT Id_Exp, Nom_Exp, Desc_Exp, Date_Debut_Exp, Date_Fin_Exp,
                    Duree_Exp_Min, Nb_Sessions_Prevues, Type_Retribution, Statut_Exp
             FROM EXPERIMENTATION
             WHERE Id_Exp = :experiment_id AND Id_Equipe = :team_id'
        );
        $query->execute(['experiment_id' => $experimentId, 'team_id' => $teamId]);
        $experiment = $query->fetch();

        return $experiment ?: null;
    }

    public function participantsForDashboard(int $experimentId, int $teamId): array
    {
        $query = $this->pdo->prepare(
            'SELECT u.Id_User, u.Nom_User, u.Prenom_User, i.Statut_Insc, i.Date_Demande
             FROM INSCRIPTION i
             INNER JOIN UTILISATEUR u ON u.Id_User = i.Id_User
             INNER JOIN `SESSION` s ON s.Id_Ses = i.Id_Ses
             INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
             WHERE e.Id_Exp = :experiment_id AND e.Id_Equipe = :team_id
             ORDER BY i.Date_Demande, u.Nom_User, u.Prenom_User'
        );
        $query->execute(['experiment_id' => $experimentId, 'team_id' => $teamId]);

        return $query->fetchAll();
    }

    public function resourcesForDashboard(int $experimentId, int $teamId): array
    {
        $query = $this->pdo->prepare(
            'SELECT DISTINCT r.Id_Res, r.Nom_Res, r.Type_Res
             FROM RESSOURCE r
             INNER JOIN RESERVATION res ON res.Id_Res = r.Id_Res
             INNER JOIN `SESSION` s ON s.Id_Ses = res.Id_Ses
             INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
             WHERE e.Id_Exp = :experiment_id AND e.Id_Equipe = :team_id
             ORDER BY r.Nom_Res, r.Id_Res'
        );
        $query->execute(['experiment_id' => $experimentId, 'team_id' => $teamId]);

        return $query->fetchAll();
    }

    public function sessionCapacityForTeam(int $experimentId, int $teamId): int
    {
        $query = $this->pdo->prepare(
            'SELECT COALESCE(SUM(s.Nb_Place_Max), 0)
             FROM `SESSION` s
             INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
             WHERE e.Id_Exp = :experiment_id AND e.Id_Equipe = :team_id'
        );
        $query->execute(['experiment_id' => $experimentId, 'team_id' => $teamId]);

        return (int) $query->fetchColumn();
    }

    public function listForTeam(int $teamId, string $search, string $archiveView): array
    {
        $sql =
            'SELECT e.Id_Exp, e.Nom_Exp, e.Date_Debut_Exp, e.Date_Fin_Exp, e.Statut_Exp,
                    e.Nb_Sessions_Prevues, COALESCE(s.session_count, 0) AS SessionCount
             FROM EXPERIMENTATION e
             LEFT JOIN (
                 SELECT Id_Exp, COUNT(*) AS session_count
                 FROM `SESSION`
                 GROUP BY Id_Exp
             ) s ON s.Id_Exp = e.Id_Exp
             WHERE e.Id_Equipe = :team_id AND e.Nom_Exp LIKE :search';
        if ($archiveView === 'hide') {
            $sql .= " AND e.Statut_Exp <> 'Archivée'";
        }
        $sql .= ' ORDER BY e.Date_Debut_Exp DESC, e.Id_Exp DESC';

        $query = $this->pdo->prepare($sql);
        $query->execute(['team_id' => $teamId, 'search' => '%' . $search . '%']);

        return $query->fetchAll();
    }

    public function getLimits(int $experimentId): array
    {
        $query = $this->pdo->prepare(
            'SELECT c.Nom_Contrainte, l.Val_Min, l.Val_Max
             FROM LIMITES l
             INNER JOIN CONTRAINTE c ON c.Id_Contrainte = l.Id_Contrainte
             WHERE l.Id_Exp = :experiment_id'
        );
        $query->execute(['experiment_id' => $experimentId]);

        $limits = [];
        foreach ($query->fetchAll() as $limit) {
            $limits[$limit['Nom_Contrainte']] = [
                'minimum' => $limit['Val_Min'],
                'maximum' => $limit['Val_Max'],
            ];
        }

        return $limits;
    }

    public function getDocuments(int $experimentId): array
    {
        $query = $this->pdo->prepare(
            'SELECT Id_Doc, Nom_Doc, Url_Doc
             FROM DOCUMENT
             WHERE Id_Exp = :experiment_id
             ORDER BY Id_Doc'
        );
        $query->execute(['experiment_id' => $experimentId]);

        return $query->fetchAll();
    }

    public function save(int $teamId, int $userId, array $data, ?int $experimentId): bool
    {
        $isEditing = $experimentId !== null;
        $storedFiles = [];
        $removedDocumentUrls = [];
        $storageDirectory = dirname(__DIR__, 2) . '/storage/documents';

        if (!is_dir($storageDirectory)
            && !mkdir($storageDirectory, 0750, true)
            && !is_dir($storageDirectory)) {
            throw new RuntimeException('Le dossier de stockage des documents ne peut pas être créé.');
        }

        try {
            $this->pdo->beginTransaction();
            $rewardType = $data['reward_type'];

            if ($isEditing) {
                $experiment = $this->findForTeam($experimentId, $teamId);
                if ($experiment === null) {
                    throw new RuntimeException('Expérimentation introuvable pour votre équipe.');
                }
                if ($experiment['Statut_Exp'] === 'Archivée') {
                    throw new RuntimeException('Une expérimentation archivée ne peut pas être modifiée.');
                }

                $sessions = $this->pdo->prepare(
                    'SELECT COUNT(*) AS session_count, MIN(Date_Ses) AS first_session, MAX(Date_Ses) AS last_session
                     FROM `SESSION`
                     WHERE Id_Exp = :experiment_id'
                );
                $sessions->execute(['experiment_id' => $experimentId]);
                $summary = $sessions->fetch();

                if ((int) $data['session_count'] < (int) $summary['session_count']) {
                    throw new RuntimeException('Le nombre de séances prévues ne peut pas être inférieur aux séances déjà créées.');
                }
                if (($summary['first_session'] !== null && $data['date_start'] > $summary['first_session'])
                    || ($summary['last_session'] !== null && $data['date_end'] < $summary['last_session'])) {
                    throw new RuntimeException('Les dates doivent inclure les séances déjà planifiées.');
                }

                $update = $this->pdo->prepare(
                    'UPDATE EXPERIMENTATION
                     SET Nom_Exp = :name, Desc_Exp = :description,
                         Date_Debut_Exp = :date_start, Date_Fin_Exp = :date_end,
                         Duree_Exp_Min = :duration_minutes, Nb_Sessions_Prevues = :session_count,
                         Type_Retribution = :reward_type
                     WHERE Id_Exp = :experiment_id AND Id_Equipe = :team_id'
                );
                $update->execute([
                    'name' => $data['title'],
                    'description' => $data['description'] !== '' ? $data['description'] : null,
                    'date_start' => $data['date_start'] . ' 00:00:00',
                    'date_end' => $data['date_end'] . ' 23:59:59',
                    'duration_minutes' => $data['duration'],
                    'session_count' => $data['session_count'],
                    'reward_type' => $rewardType,
                    'experiment_id' => $experimentId,
                    'team_id' => $teamId,
                ]);

                $existingDocuments = $this->getDocuments($experimentId);
                $documentsById = [];
                foreach ($existingDocuments as $document) {
                    $documentsById[(int) $document['Id_Doc']] = $document;
                }

                foreach ($data['documents_to_delete'] as $documentId) {
                    if (!isset($documentsById[$documentId])) {
                        throw new RuntimeException('Un document sélectionné n’est plus associé à cette expérimentation.');
                    }
                    $delete = $this->pdo->prepare(
                        'DELETE FROM DOCUMENT
                         WHERE Id_Doc = :document_id AND Id_Exp = :experiment_id'
                    );
                    $delete->execute(['document_id' => $documentId, 'experiment_id' => $experimentId]);
                    if ($delete->rowCount() !== 1) {
                        throw new RuntimeException('Un document sélectionné n’est plus associé à cette expérimentation.');
                    }
                    $removedDocumentUrls[$documentId] = (string) $documentsById[$documentId]['Url_Doc'];
                }

                $deleteLimits = $this->pdo->prepare('DELETE FROM LIMITES WHERE Id_Exp = :experiment_id');
                $deleteLimits->execute(['experiment_id' => $experimentId]);
            } else {
                $insert = $this->pdo->prepare(
                    'INSERT INTO EXPERIMENTATION
                        (Nom_Exp, Desc_Exp, Date_Debut_Exp, Date_Fin_Exp, Duree_Exp_Min,
                         Nb_Sessions_Prevues, Type_Retribution, Id_Equipe)
                     VALUES
                        (:name, :description, :date_start, :date_end, :duration_minutes,
                         :session_count, :reward_type, :team_id)'
                );
                $insert->execute([
                    'name' => $data['title'],
                    'description' => $data['description'] !== '' ? $data['description'] : null,
                    'date_start' => $data['date_start'] . ' 00:00:00',
                    'date_end' => $data['date_end'] . ' 23:59:59',
                    'duration_minutes' => $data['duration'],
                    'session_count' => $data['session_count'],
                    'reward_type' => $rewardType,
                    'team_id' => $teamId,
                ]);
                $experimentId = (int) $this->pdo->lastInsertId();
            }

            $this->saveLimits($experimentId, $data['limits']);
            $this->saveDocuments($experimentId, $data['uploads'], $storageDirectory, $storedFiles);

            $log = $this->pdo->prepare(
                'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                 VALUES (:type, :ip, :user_id)'
            );
            $log->execute([
                'type' => $isEditing ? 'Modification expérimentation' : 'Création expérimentation',
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                'user_id' => $userId,
            ]);

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            foreach ($storedFiles as $path) {
                if (is_file($path) && !unlink($path)) {
                    error_log('Could not remove an uncommitted experiment upload.');
                }
            }
            throw $exception;
        }

        return $this->removeUnlinkedFiles($removedDocumentUrls, $storageDirectory);
    }

    public function archiveOrDelete(int $teamId, int $userId, array $experimentIds, string $action): bool
    {
        $removedDocumentUrls = [];
        $this->pdo->beginTransaction();
        try {
            $placeholders = [];
            $parameters = ['team_id' => $teamId];
            foreach ($experimentIds as $index => $id) {
                $key = 'experiment_' . $index;
                $placeholders[] = ':' . $key;
                $parameters[$key] = $id;
            }
            $inClause = implode(', ', $placeholders);
            $check = $this->pdo->prepare(
                'SELECT Id_Exp FROM EXPERIMENTATION
                 WHERE Id_Equipe = :team_id AND Id_Exp IN (' . $inClause . ')'
            );
            $check->execute($parameters);
            if (count($check->fetchAll(PDO::FETCH_COLUMN)) !== count($experimentIds)) {
                throw new RuntimeException('Une ou plusieurs expérimentations sélectionnées ne sont pas accessibles.');
            }

            if ($action === 'archive') {
                $query = $this->pdo->prepare(
                    'UPDATE EXPERIMENTATION SET Statut_Exp = \'Archivée\'
                     WHERE Id_Equipe = :team_id AND Id_Exp IN (' . $inClause . ')'
                );
                $query->execute($parameters);
            } else {
                foreach ($experimentIds as $id) {
                    $sessions = $this->pdo->prepare(
                        'SELECT Id_Ses FROM `SESSION` WHERE Id_Exp = :experiment_id'
                    );
                    $sessions->execute(['experiment_id' => $id]);
                    $pdo = $this->pdo;
                    $deleteLinks = static function (string $table, int $sessionId) use ($pdo): void {
                        $statement = $pdo->prepare('DELETE FROM ' . $table . ' WHERE Id_Ses = :session_id');
                        $statement->execute(['session_id' => $sessionId]);
                    };
                    foreach ($sessions->fetchAll(PDO::FETCH_COLUMN) as $sessionId) {
                        $deleteLinks('INSCRIPTION', (int) $sessionId);
                        $deleteLinks('RESERVATION', (int) $sessionId);
                    }

                    $documents = $this->pdo->prepare(
                        'SELECT Url_Doc FROM DOCUMENT WHERE Id_Exp = :experiment_id'
                    );
                    $documents->execute(['experiment_id' => $id]);
                    foreach ($documents->fetchAll(PDO::FETCH_COLUMN) as $url) {
                        $removedDocumentUrls[] = (string) $url;
                    }
                    $deleteQuestions = $this->pdo->prepare(
                        'DELETE FROM QUESTION
                         WHERE Id_Quest IN (
                             SELECT Id_Quest FROM QUESTIONNAIRE WHERE Id_Exp = :experiment_id
                         )'
                    );
                    $deleteQuestions->execute(['experiment_id' => $id]);
                    foreach (['LIVRABLE', 'DOCUMENT', 'LIMITES', 'QUESTIONNAIRE', '`SESSION`'] as $table) {
                        $delete = $this->pdo->prepare('DELETE FROM ' . $table . ' WHERE Id_Exp = :experiment_id');
                        $delete->execute(['experiment_id' => $id]);
                    }
                    $deleteExperiment = $this->pdo->prepare(
                        'DELETE FROM EXPERIMENTATION WHERE Id_Exp = :experiment_id AND Id_Equipe = :team_id'
                    );
                    $deleteExperiment->execute(['experiment_id' => $id, 'team_id' => $teamId]);
                    if ($deleteExperiment->rowCount() !== 1) {
                        throw new RuntimeException('Une expérimentation sélectionnée n’a pas pu être supprimée.');
                    }
                }
            }

            $log = $this->pdo->prepare(
                'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                 VALUES (:type, :ip, :user_id)'
            );
            $log->execute([
                'type' => $action === 'archive' ? 'Archivage expérimentation' : 'Suppression expérimentation',
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                'user_id' => $userId,
            ]);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }

        return $action === 'delete'
            ? $this->removeUnlinkedFiles($removedDocumentUrls, dirname(__DIR__, 2) . '/storage/documents')
            : false;
    }

    private function saveLimits(int $experimentId, array $limits): void
    {
        $findConstraint = $this->pdo->prepare(
            'SELECT Id_Contrainte FROM CONTRAINTE WHERE Nom_Contrainte = :name'
        );
        $insertConstraint = $this->pdo->prepare(
            'INSERT INTO CONTRAINTE (Nom_Contrainte, Desc_Contrainte)
             VALUES (:name, :description)'
        );
        $insertLimit = $this->pdo->prepare(
            'INSERT INTO LIMITES (Id_Exp, Id_Contrainte, Val_Min, Val_Max)
             VALUES (:experiment_id, :constraint_id, :minimum, :maximum)'
        );
        $fixedNames = [
            'Age minimum', 'Francais', 'Anglais', 'Femme', 'Homme',
            'Sans lunettes', 'Avec lunettes',
        ];

        foreach ($limits as $limit) {
            $findConstraint->execute(['name' => $limit['name']]);
            $constraintId = $findConstraint->fetchColumn();
            if ($constraintId === false) {
                if (in_array($limit['name'], $fixedNames, true)) {
                    throw new RuntimeException('Une restriction choisie n’existe plus dans la base de données.');
                }
                $insertConstraint->execute([
                    'name' => $limit['name'],
                    'description' => 'Restriction personnalisée créée depuis une expérimentation.',
                ]);
                $constraintId = $this->pdo->lastInsertId();
            }
            $insertLimit->execute([
                'experiment_id' => $experimentId,
                'constraint_id' => (int) $constraintId,
                'minimum' => $limit['minimum'],
                'maximum' => $limit['maximum'],
            ]);
        }
    }

    private function saveDocuments(int $experimentId, array $uploads, string $directory, array &$storedFiles): void
    {
        $insert = $this->pdo->prepare(
            'INSERT INTO DOCUMENT (Nom_Doc, Type_Doc, Poids_Doc, Url_Doc, Id_Exp)
             VALUES (:name, :type, :size, :url, :experiment_id)'
        );
        foreach ($uploads as $upload) {
            $name = bin2hex(random_bytes(16)) . '.' . $upload['extension'];
            $path = $directory . DIRECTORY_SEPARATOR . $name;
            if (!move_uploaded_file($upload['tmp_path'], $path)) {
                throw new RuntimeException('Un document n’a pas pu être enregistré.');
            }
            $storedFiles[] = $path;
            $insert->execute([
                'name' => $upload['name'],
                'type' => $upload['extension'],
                'size' => $upload['size'],
                'url' => 'storage/documents/' . $name,
                'experiment_id' => $experimentId,
            ]);
        }
    }

    private function removeUnlinkedFiles(array $documentUrls, string $storageDirectory): bool
    {
        $storageRoot = realpath($storageDirectory);
        if ($storageRoot === false) {
            return false;
        }

        $failed = false;
        foreach (array_unique($documentUrls) as $url) {
            $prefix = 'storage/documents/';
            if (strncmp($url, $prefix, strlen($prefix)) !== 0) {
                continue;
            }
            $query = $this->pdo->prepare('SELECT COUNT(*) FROM DOCUMENT WHERE Url_Doc = :url');
            $query->execute(['url' => $url]);
            if ((int) $query->fetchColumn() > 0) {
                continue;
            }
            $name = substr($url, strlen($prefix));
            if ($name === '' || basename($name) !== $name) {
                $failed = true;
                error_log('Refused to remove document with invalid stored path.');
                continue;
            }
            $path = realpath($storageRoot . DIRECTORY_SEPARATOR . $name);
            if ($path === false) {
                continue;
            }
            $sameDirectory = DIRECTORY_SEPARATOR === '\\'
                ? strcasecmp(dirname($path), $storageRoot) === 0
                : dirname($path) === $storageRoot;
            if (!$sameDirectory) {
                $failed = true;
                error_log('Refused to remove document outside the managed storage directory.');
                continue;
            }
            if (is_file($path) && !unlink($path)) {
                $failed = true;
                error_log('Could not remove a document file after its database record was deleted.');
            }
        }

        return $failed;
    }
}
