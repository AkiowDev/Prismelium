<?php
declare(strict_types=1);

require_once __DIR__ . '/../BDD_control/current_researcher.php';

$csrfToken = $_SESSION['csrf_token'] ?? null;
if (!is_string($csrfToken) || strlen($csrfToken) !== 64) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrfToken;
}

$actionError = '';
$notice = is_string($_GET['notice'] ?? null) ? $_GET['notice'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? null;
    $action = $_POST['action'] ?? null;
    $rawUserId = $_POST['user_id'] ?? null;
    $rawSessionId = $_POST['session_id'] ?? null;
    $userId = is_string($rawUserId) || is_int($rawUserId)
        ? filter_var($rawUserId, FILTER_VALIDATE_INT)
        : false;
    $sessionId = is_string($rawSessionId) || is_int($rawSessionId)
        ? filter_var($rawSessionId, FILTER_VALIDATE_INT)
        : false;

    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        http_response_code(400);
        $actionError = 'La requête a expiré. Rechargez la page et réessayez.';
    } elseif (!is_string($action) || !in_array($action, ['accept', 'refuse'], true)) {
        http_response_code(400);
        $actionError = 'L’action demandée est invalide.';
    } elseif ($userId === false || $userId < 1 || $sessionId === false || $sessionId < 1) {
        $actionError = 'La demande d’inscription sélectionnée est invalide.';
    } else {
        try {
            $pdo->beginTransaction();
            $updateEnrollment = $pdo->prepare(
                'UPDATE INSCRIPTION i
                 INNER JOIN `SESSION` s ON s.Id_Ses = i.Id_Ses
                 INNER JOIN EXPERIMENTATION e ON e.Id_Exp = s.Id_Exp
                 SET i.Statut_Insc = :status
                 WHERE i.Id_User = :user_id
                   AND i.Id_Ses = :session_id
                   AND i.Statut_Insc IN (\'En attente\', \'Attente gestionnaire\')
                   AND e.Id_Equipe = :team_id'
            );
            $updateEnrollment->execute([
                'status' => $action === 'accept' ? 'Validee' : 'Refusee',
                'user_id' => $userId,
                'session_id' => $sessionId,
                'team_id' => $currentTeamId,
            ]);

            if ($updateEnrollment->rowCount() !== 1) {
                throw new RuntimeException('Cette demande n’est plus en attente ou n’est pas accessible.');
            }

            $logQuery = $pdo->prepare(
                'INSERT INTO JOURNAL_LOG (Type_Log, IP_log, Id_User)
                 VALUES (:type, :ip, :user_id)'
            );
            $logQuery->execute([
                'type' => $action === 'accept' ? 'Validation inscription' : 'Refus inscription',
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 0, 45),
                'user_id' => (int) $currentUser['Id_User'],
            ]);

            $pdo->commit();
            header('Location: ./GestionInscriptionsBureau.php?notice=' . ($action === 'accept' ? 'accepted' : 'refused'));
            exit;
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Enrollment decision failed: ' . $exception->getMessage());
            $actionError = 'La décision n’a pas pu être enregistrée. Réessayez.';
        } catch (RuntimeException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $actionError = $exception->getMessage();
        }
    }
}

$enrollmentQuery = $pdo->prepare(
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
$enrollmentQuery->execute(['team_id' => $currentTeamId]);
$enrollments = $enrollmentQuery->fetchAll();

$consentDocumentsByExperiment = [];
if ($enrollments !== []) {
    $experimentIds = array_values(array_unique(array_map(
        static fn (array $enrollment): int => (int) $enrollment['Id_Exp'],
        $enrollments
    )));
    $documentPlaceholders = [];
    $documentParameters = [];
    foreach ($experimentIds as $index => $experimentId) {
        $placeholder = 'experiment_' . $index;
        $documentPlaceholders[] = ':' . $placeholder;
        $documentParameters[$placeholder] = $experimentId;
    }

    $documentsQuery = $pdo->prepare(
        'SELECT Id_Exp, Nom_Doc, Type_Doc, Url_Doc
         FROM DOCUMENT
         WHERE Id_Exp IN (' . implode(', ', $documentPlaceholders) . ')
           AND (Nom_Doc LIKE :consent_name OR Type_Doc LIKE :consent_type)
         ORDER BY Nom_Doc'
    );
    $documentsQuery->execute($documentParameters + [
        'consent_name' => '%consent%',
        'consent_type' => '%consent%',
    ]);

    foreach ($documentsQuery->fetchAll() as $document) {
        $documentUrl = (string) $document['Url_Doc'];
        $documentHref = null;
        $storagePrefix = 'storage/documents/';
        if (strncmp($documentUrl, $storagePrefix, strlen($storagePrefix)) === 0) {
            $storedName = substr($documentUrl, strlen($storagePrefix));
            if ($storedName !== '' && basename($storedName) === $storedName) {
                $documentHref = '../storage/documents/' . rawurlencode($storedName);
            }
        } else {
            $urlScheme = strtolower((string) parse_url($documentUrl, PHP_URL_SCHEME));
            if (in_array($urlScheme, ['http', 'https'], true)) {
                $documentHref = $documentUrl;
            }
        }

        $consentDocumentsByExperiment[(int) $document['Id_Exp']][] = [
            'name' => (string) $document['Nom_Doc'],
            'type' => (string) $document['Type_Doc'],
            'href' => $documentHref,
        ];
    }
}

$escape = static fn ($value): string => htmlspecialchars(
    is_scalar($value) ? (string) $value : '',
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$formatAge = static function (string $birthDate): string {
    try {
        return (new DateTimeImmutable($birthDate))->diff(new DateTimeImmutable('today'))->y . ' ans';
    } catch (Exception $exception) {
        return 'Âge indisponible';
    }
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des inscriptions — Bureau</title>
    <link rel="stylesheet" href="../css/stylesGEDesktop.css?v=4">
    <link rel="stylesheet" href="../css/stylesGestionInscriptionsDesktop.css?v=1">
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
                    <a href="./GestionExperimentationBureau.php">Expérimentations</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>

                <img class="barre-laterale__logo-universite" src="../assets/university-logo.png" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal page-inscriptions">
                <header class="entete-page entete-inscriptions">
                    <h1>Gestion des inscriptions</h1>
                    <img class="separateur-page" src="../assets/divider.svg" alt="" aria-hidden="true">
                </header>

                <?php if ($notice === 'accepted'): ?>
                    <p class="inscriptions-notice inscriptions-notice--success" role="status">La demande d’inscription a été validée.</p>
                <?php elseif ($notice === 'refused'): ?>
                    <p class="inscriptions-notice inscriptions-notice--success" role="status">La demande d’inscription a été refusée.</p>
                <?php endif; ?>
                <?php if ($actionError !== ''): ?>
                    <p class="inscriptions-notice inscriptions-notice--error" role="alert"><?= $escape($actionError) ?></p>
                <?php endif; ?>

                <section class="liste-inscriptions" aria-labelledby="titre-liste-inscriptions">
                    <h2 id="titre-liste-inscriptions">Liste des inscriptions</h2>
                    <?php if ($enrollments !== []): ?>
                        <div class="grille-inscriptions">
                            <?php foreach ($enrollments as $enrollment): ?>
                                <?php
                                $fullName = trim($enrollment['Prenom_User'] . ' ' . $enrollment['Nom_User']);
                                $modalData = [
                                    'userId' => (int) $enrollment['Id_User'],
                                    'sessionId' => (int) $enrollment['Id_Ses'],
                                    'name' => $fullName,
                                    'age' => $formatAge((string) $enrollment['Birth_User']),
                                    'email' => (string) $enrollment['Mail_User'],
                                    'experiment' => (string) $enrollment['Nom_Exp'],
                                    'session' => (new DateTimeImmutable($enrollment['Date_Ses']))->format('d/m/Y')
                                        . ' · ' . substr((string) $enrollment['Debut_Ses'], 0, 5)
                                        . '–' . substr((string) $enrollment['Fin_Ses'], 0, 5),
                                    'phone' => (string) ($enrollment['Tel_User'] ?? ''),
                                    'gender' => (string) $enrollment['Genre_User'],
                                    'language' => (string) $enrollment['Lang_User'],
                                    'description' => (string) ($enrollment['Desc_User'] ?? ''),
                                    'documents' => $consentDocumentsByExperiment[(int) $enrollment['Id_Exp']] ?? [],
                                ];
                                $modalJson = json_encode(
                                    $modalData,
                                    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
                                );
                                if ($modalJson === false) {
                                    throw new RuntimeException('Les détails d’une inscription ne peuvent pas être affichés.');
                                }
                                ?>
                                <button
                                    class="carte-inscription"
                                    type="button"
                                    data-inscription="<?= $escape($modalJson) ?>"
                                    aria-label="Ouvrir la demande de <?= $escape($fullName) ?>"
                                >
                                    <img src="../assets/inscription-profile.png" alt="" aria-hidden="true">
                                    <span class="carte-inscription__nom"><?= $escape($fullName) ?></span>
                                    <span class="carte-inscription__experience"><?= $escape($enrollment['Nom_Exp']) ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="inscriptions-vides">Aucune demande d’inscription en attente.</p>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>

    <dialog class="overlay-inscription" id="overlay-inscription" aria-labelledby="overlay-nom">
        <form class="overlay-inscription__contenu" method="post" id="decision-inscription">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <input type="hidden" name="user_id" id="overlay-user-id">
            <input type="hidden" name="session_id" id="overlay-session-id">

            <div class="overlay-inscription__profil">
                <div class="overlay-inscription__identite">
                    <img src="../assets/inscription-profile.png" alt="" aria-hidden="true">
                    <button class="bouton-profil" type="button" id="bouton-profil" aria-expanded="false">
                        Voir profile
                    </button>
                </div>
                <div class="overlay-inscription__informations">
                    <h2 id="overlay-nom"></h2>
                    <p><strong>Âge:</strong> <span id="overlay-age"></span></p>
                    <p><strong>Email:</strong> <a id="overlay-email"></a></p>
                </div>
            </div>

            <dl class="overlay-inscription__profil-detail" id="overlay-profil-detail" hidden>
                <div><dt>Téléphone</dt><dd id="overlay-telephone"></dd></div>
                <div><dt>Genre</dt><dd id="overlay-genre"></dd></div>
                <div><dt>Langue</dt><dd id="overlay-langue"></dd></div>
                <div><dt>Profil</dt><dd id="overlay-description"></dd></div>
            </dl>

            <section class="overlay-inscription__demande" aria-labelledby="overlay-experience-label">
                <h3 id="overlay-experience-label">Expérimentation voulu :</h3>
                <p id="overlay-experience"></p>
                <p class="overlay-inscription__session" id="overlay-session"></p>
            </section>

            <section class="overlay-inscription__consentements" aria-labelledby="overlay-consentements-title">
                <h3 id="overlay-consentements-title">Liste des consentements :</h3>
                <ul id="overlay-documents"></ul>
                <p id="overlay-documents-vides">Aucun document de consentement associé.</p>
            </section>

            <div class="overlay-inscription__actions">
                <button class="bouton-decision bouton-decision--valider" type="submit" name="action" value="accept">Valider</button>
                <button class="bouton-decision bouton-decision--refuser" type="submit" name="action" value="refuse">Refuser</button>
            </div>
        </form>
    </dialog>

    <script src="../js/gestion-inscriptions.js?v=1"></script>
</body>
</html>
