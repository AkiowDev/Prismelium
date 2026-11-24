<?php
declare(strict_types=1);

require_once __DIR__ . '/../BDD_control/current_researcher.php';

$csrfToken = $_SESSION['csrf_token'] ?? null;
if (!is_string($csrfToken) || strlen($csrfToken) !== 64) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrfToken;
}

$archiveView = is_string($_GET['archive_view'] ?? null)
    && in_array($_GET['archive_view'], ['hide', 'show'], true)
        ? $_GET['archive_view']
        : 'hide';
$searchTerm = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
$actionError = '';
$selectedIds = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? null;
    $action = $_POST['action'] ?? null;
    $postedIds = $_POST['selected_experiments'] ?? null;

    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        http_response_code(400);
        $actionError = 'La requête a expiré. Rechargez la page et réessayez.';
    } elseif (!is_string($action) || !in_array($action, ['archive', 'delete'], true)) {
        http_response_code(400);
        $actionError = 'L’action demandée est invalide.';
    } elseif (!is_array($postedIds) || $postedIds === [] || count($postedIds) > 100) {
        $actionError = 'Sélectionnez entre 1 et 100 expérimentations.';
    } else {
        foreach ($postedIds as $postedId) {
            if (!is_string($postedId) && !is_int($postedId)) {
                $selectedIds = [];
                break;
            }
            $validatedId = filter_var($postedId, FILTER_VALIDATE_INT);
            if ($validatedId === false || $validatedId < 1) {
                $selectedIds = [];
                break;
            }
            $selectedIds[] = $validatedId;
        }
        $selectedIds = array_values(array_unique($selectedIds));

        if ($selectedIds === []) {
            $actionError = 'La sélection des expérimentations est invalide.';
        } else {
            $idPlaceholders = [];
            $idParameters = ['team_id' => $currentTeamId];
            foreach ($selectedIds as $index => $selectedId) {
                $placeholder = 'experiment_' . $index;
                $idPlaceholders[] = ':' . $placeholder;
                $idParameters[$placeholder] = $selectedId;
            }

            try {
                $selectedQuery = $pdo->prepare(
                    'SELECT Id_Exp, Nom_Exp
                     FROM EXPERIMENTATION
                     WHERE Id_Equipe = :team_id AND Id_Exp IN (' . implode(', ', $idPlaceholders) . ')'
                );
                $selectedQuery->execute($idParameters);
                $selectedExperiments = $selectedQuery->fetchAll();

                if (count($selectedExperiments) !== count($selectedIds)) {
                    $actionError = 'Une ou plusieurs expérimentations sélectionnées ne sont pas accessibles.';
                } elseif ($action === 'archive') {
                    $pdo->beginTransaction();
                    $archiveQuery = $pdo->prepare(
                        'UPDATE EXPERIMENTATION
                         SET Statut_Exp = \'Archivée\'
                         WHERE Id_Equipe = :team_id AND Id_Exp IN (' . implode(', ', $idPlaceholders) . ')'
                    );
                    $archiveQuery->execute($idParameters);

                    $logQuery = $pdo->prepare(
                        'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                         VALUES (:type, :ip, :user_id)'
                    );
                    $logQuery->execute([
                        'type' => 'Archivage expérimentation',
                        'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                        'user_id' => (int) $currentUser['Id_User'],
                    ]);
                    $pdo->commit();

                    $redirectParams = ['archive_view' => $archiveView, 'notice' => 'archived'];
                    if ($searchTerm !== '') {
                        $redirectParams['q'] = $searchTerm;
                    }
                    header('Location: ./GestionExperimentationBureau.php?' . http_build_query($redirectParams));
                    exit;
                } else {
                    $removedDocumentUrls = [];
                    $pdo->beginTransaction();

                    $deleteSessionLinks = static function (string $table, int $sessionId) use ($pdo): void {
                        $statement = $pdo->prepare('DELETE FROM ' . $table . ' WHERE Id_Ses = :session_id');
                        $statement->execute(['session_id' => $sessionId]);
                    };

                    foreach ($selectedIds as $selectedId) {
                        $sessionsQuery = $pdo->prepare(
                            'SELECT Id_Ses FROM `SESSION` WHERE Id_Exp = :experiment_id'
                        );
                        $sessionsQuery->execute(['experiment_id' => $selectedId]);
                        foreach ($sessionsQuery->fetchAll(PDO::FETCH_COLUMN) as $sessionId) {
                            $deleteSessionLinks('INSCRIPTION', (int) $sessionId);
                            $deleteSessionLinks('RESERVATION', (int) $sessionId);
                        }

                        $documentsQuery = $pdo->prepare(
                            'SELECT Url_Doc FROM DOCUMENT WHERE Id_Exp = :experiment_id'
                        );
                        $documentsQuery->execute(['experiment_id' => $selectedId]);
                        foreach ($documentsQuery->fetchAll(PDO::FETCH_COLUMN) as $documentUrl) {
                            $removedDocumentUrls[] = (string) $documentUrl;
                        }

                        $deleteQuestions = $pdo->prepare(
                            'DELETE FROM QUESTION
                             WHERE Id_Quest IN (
                                 SELECT Id_Quest FROM QUESTIONNAIRE WHERE Id_Exp = :experiment_id
                             )'
                        );
                        $deleteQuestions->execute(['experiment_id' => $selectedId]);

                        foreach (['LIVRABLE', 'DOCUMENT', 'LIMITES', 'QUESTIONNAIRE', '`SESSION`'] as $table) {
                            $deleteRelated = $pdo->prepare(
                                'DELETE FROM ' . $table . ' WHERE Id_Exp = :experiment_id'
                            );
                            $deleteRelated->execute(['experiment_id' => $selectedId]);
                        }

                        $deleteExperiment = $pdo->prepare(
                            'DELETE FROM EXPERIMENTATION
                             WHERE Id_Exp = :experiment_id AND Id_Equipe = :team_id'
                        );
                        $deleteExperiment->execute([
                            'experiment_id' => $selectedId,
                            'team_id' => $currentTeamId,
                        ]);
                        if ($deleteExperiment->rowCount() !== 1) {
                            throw new RuntimeException('Une expérimentation sélectionnée n’a pas pu être supprimée.');
                        }
                    }

                    $logQuery = $pdo->prepare(
                        'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                         VALUES (:type, :ip, :user_id)'
                    );
                    $logQuery->execute([
                        'type' => 'Suppression expérimentation',
                        'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                        'user_id' => (int) $currentUser['Id_User'],
                    ]);
                    $pdo->commit();

                    $documentCleanupFailed = false;
                    $storageRoot = realpath(__DIR__ . '/../storage/documents');
                    if ($storageRoot !== false) {
                        foreach (array_unique($removedDocumentUrls) as $documentUrl) {
                            $storagePrefix = 'storage/documents/';
                            if (strncmp($documentUrl, $storagePrefix, strlen($storagePrefix)) !== 0) {
                                continue;
                            }
                            $remainingDocumentQuery = $pdo->prepare(
                                'SELECT COUNT(*) FROM DOCUMENT WHERE Url_Doc = :url'
                            );
                            $remainingDocumentQuery->execute(['url' => $documentUrl]);
                            if ((int) $remainingDocumentQuery->fetchColumn() > 0) {
                                continue;
                            }
                            $storedName = substr($documentUrl, strlen($storagePrefix));
                            if ($storedName === '' || basename($storedName) !== $storedName) {
                                $documentCleanupFailed = true;
                                error_log('Refused to remove document with invalid stored path.');
                                continue;
                            }
                            $storedPath = realpath($storageRoot . DIRECTORY_SEPARATOR . $storedName);
                            if ($storedPath === false) {
                                continue;
                            }
                            $sameDirectory = DIRECTORY_SEPARATOR === '\\'
                                ? strcasecmp(dirname($storedPath), $storageRoot) === 0
                                : dirname($storedPath) === $storageRoot;
                            if (!$sameDirectory) {
                                $documentCleanupFailed = true;
                                error_log('Refused to remove document outside the managed storage directory.');
                                continue;
                            }
                            if (is_file($storedPath) && !unlink($storedPath)) {
                                $documentCleanupFailed = true;
                                error_log('Could not remove a document file after deleting its experiment.');
                            }
                        }
                    }

                    $redirectParams = ['archive_view' => $archiveView, 'notice' => 'deleted'];
                    if ($searchTerm !== '') {
                        $redirectParams['q'] = $searchTerm;
                    }
                    if ($documentCleanupFailed) {
                        $redirectParams['document_cleanup_failed'] = '1';
                    }
                    header('Location: ./GestionExperimentationBureau.php?' . http_build_query($redirectParams));
                    exit;
                }
            } catch (PDOException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('Experiment management action failed: ' . $exception->getMessage());
                $actionError = 'L’action n’a pas pu être effectuée. Réessayez.';
            } catch (RuntimeException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $actionError = $exception->getMessage();
            }
        }
    }
}

$searchSql =
    'SELECT e.Id_Exp, e.Nom_Exp, e.Date_Debut_Exp, e.Date_Fin_Exp, e.Statut_Exp,
            e.Nb_Sessions_Prevues, COALESCE(s.session_count, 0) AS SessionCount
     FROM EXPERIMENTATION e
     LEFT JOIN (
         SELECT Id_Exp, COUNT(*) AS session_count
         FROM `SESSION`
         GROUP BY Id_Exp
     ) s ON s.Id_Exp = e.Id_Exp
     WHERE e.Id_Equipe = :team_id
       AND e.Nom_Exp LIKE :search';
if ($archiveView === 'hide') {
    $searchSql .= " AND e.Statut_Exp <> 'Archivée'";
}
$searchSql .= ' ORDER BY e.Date_Debut_Exp DESC, e.Id_Exp DESC';
$searchQuery = $pdo->prepare($searchSql);
$searchQuery->execute([
    'team_id' => $currentTeamId,
    'search' => '%' . $searchTerm . '%',
]);
$experiments = $searchQuery->fetchAll();
$colors = ['#7a72c0', '#d54444', '#81d04f', '#a2caff', '#ffb01f', '#fff7a1', '#c5cad2', '#1f8107', '#cac262'];
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des expérimentations — Bureau</title>
    <link rel="stylesheet" href="../css/stylesGEDesktop.css?v=4">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
    <aside class="barre-laterale" aria-label="Navigation principale">
        <a class="barre-laterale__marque" href="./MenuEnseignantChercheurBureau.php" aria-label="Accueil">
            <img class="barre-laterale__logo-cerveau" src="../assets/brain-logo.png" alt="">
        </a>

        <nav class="barre-laterale__navigation">
            <a href="#">Rétributions</a>
            <a class="actuel" href="#" aria-current="page">Expérimentations</a>
            <a href="#">Gestion Planning</a>
            <a href="#">Messagerie</a>
            <a href="#">Paramètres</a>
        </nav>

        <img class="barre-laterale__logo-universite" src="../assets/university-logo.png" alt="Le Mans Université">
    </aside>

    <main class="contenu-principal">
        <header class="entete-page">
            <h1>Gestion des expérimentations</h1>
            <img class="separateur-page" src="../assets/divider.svg" alt="" aria-hidden="true">
            <div class="actions-experiences" aria-label="Actions sur les expérimentations">
                <a class="actions-experiences__lien" href="./CreerExperimentationBureau.php">Créer</a>
                <form id="selection-actions" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                    <input type="hidden" name="archive_view" value="<?= $escape($archiveView) ?>">
                    <?php if ($searchTerm !== ''): ?>
                        <input type="hidden" name="q" value="<?= $escape($searchTerm) ?>">
                    <?php endif; ?>
                    <button class="actions-experiences__archiver" type="submit" name="action" value="archive">Archiver la sélection</button>
                    <button class="actions-experiences__supprimer" type="submit" name="action" value="delete">Supprimer la sélection</button>
                </form>
            </div>
            <img class="separateur-page" src="../assets/divider.svg" alt="" aria-hidden="true">
        </header>

        <section class="section-experiences" aria-labelledby="titre-experiences-bureau">
            <?php if ((string) ($_GET['created'] ?? '') === '1'): ?>
                <p class="etat-succes" role="status">L’expérimentation a bien été créée.</p>
            <?php elseif ((string) ($_GET['updated'] ?? '') === '1'): ?>
                <p class="etat-succes" role="status">L’expérimentation a bien été modifiée.</p>
            <?php endif; ?>
            <?php if ((string) ($_GET['notice'] ?? '') === 'archived'): ?>
                <p class="etat-succes" role="status">La sélection a bien été archivée.</p>
            <?php elseif ((string) ($_GET['notice'] ?? '') === 'deleted'): ?>
                <p class="etat-succes" role="status">La sélection et ses données propres ont bien été supprimées.</p>
            <?php endif; ?>
            <?php if ($actionError !== ''): ?>
                <p class="formulaire-erreur" role="alert"><?= $escape($actionError) ?></p>
            <?php endif; ?>
            <?php if ((string) ($_GET['document_cleanup_failed'] ?? '') === '1'): ?>
                <p class="etat-avertissement" role="alert">Les données ont été supprimées, mais un fichier numérique local n’a pas pu être retiré du stockage. Consultez les journaux de l’application.</p>
            <?php endif; ?>
            <div class="barre-outils-experiences">
                <h2 id="titre-experiences-bureau">Liste des expérimentations</h2>
                <form class="recherche" method="get" role="search">
                    <label class="texte-masque" for="recherche-experience-bureau">Rechercher une expérimentation</label>
                    <img class="recherche__icone" src="../assets/search.png" alt="">
                    <input
                        id="recherche-experience-bureau"
                        name="q"
                        type="search"
                        placeholder="Rechercher"
                        value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <label class="texte-masque" for="filtre-archive">Expérimentations archivées</label>
                    <select id="filtre-archive" name="archive_view" class="recherche__archive">
                        <option value="hide" <?= $archiveView === 'hide' ? 'selected' : '' ?>>Masquer les archivées</option>
                        <option value="show" <?= $archiveView === 'show' ? 'selected' : '' ?>>Afficher les archivées</option>
                    </select>
                    <button class="recherche__filtre" type="submit" aria-label="Filtrer les expérimentations">
                        <img src="../assets/filter.svg" alt="">
                    </button>
                </form>
            </div>
            <p class="selection-experiences" id="compteur-selection" aria-live="polite">Aucune expérimentation sélectionnée.</p>

            <?php if ($experiments !== []): ?>
                <div class="grille-experiences">
                    <?php foreach ($experiments as $index => $experiment): ?>
                        <?php $color = $colors[$index % count($colors)]; ?>
                        <article class="carte-experience" aria-label="<?= $escape($experiment['Nom_Exp']) ?>">
                            <div
                                class="carte-experience__couleur"
                                style="--couleur-carte: <?= $escape($color) ?>"
                                aria-hidden="true"
                            ></div>
                            <div class="carte-experience__intitule">
                                <label class="selection-experience">
                                    <input
                                        type="checkbox"
                                        name="selected_experiments[]"
                                        value="<?= (int) $experiment['Id_Exp'] ?>"
                                        form="selection-actions"
                                        <?= in_array((int) $experiment['Id_Exp'], $selectedIds, true) ? 'checked' : '' ?>
                                        aria-label="Sélectionner <?= $escape($experiment['Nom_Exp']) ?>"
                                    >
                                    <span>Sélectionner</span>
                                </label>
                                <span><?= $escape($experiment['Nom_Exp']) ?></span>
                                <small>
                                    <?= $escape($experiment['Statut_Exp']) ?> ·
                                    <?= (int) $experiment['SessionCount'] ?>/<?= $experiment['Nb_Sessions_Prevues'] === null ? '—' : (int) $experiment['Nb_Sessions_Prevues'] ?> séance(s)
                                </small>
                                <?php if ($experiment['Statut_Exp'] !== 'Archivée'): ?>
                                    <a
                                        href="./ModifierExperimentationBureau.php?id=<?= (int) $experiment['Id_Exp'] ?>"
                                        aria-label="Modifier l’expérimentation <?= $escape($experiment['Nom_Exp']) ?>"
                                    >Modifier</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="etat-vide">Aucune expérimentation ne correspond à « <?= $escape($searchTerm) ?> ».</p>
            <?php endif; ?>
        </section>
    </main>
</div>

    </div>
    <script src="../js/gestion-experimentations.js?v=1"></script>
</body>
</html>
