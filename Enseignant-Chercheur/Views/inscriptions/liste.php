<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des inscriptions — Bureau</title>
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGEDesktop.css?v=4') ?>">
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGestionInscriptionsDesktop.css?v=2') ?>">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="<?= app_url('index.php?page=enseignant-chercheur') ?>" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="<?= app_url('images/brain-logo.png') ?>" alt="">
                </a>
                <nav class="barre-laterale__navigation">
                    <a href="<?= app_url('index.php?page=retributions') ?>">Rétributions</a>
                    <a href="<?= app_url('index.php?page=experimentations') ?>">Expérimentations</a>
                    <a href="<?= app_url('index.php?page=inscriptions') ?>" aria-current="page">Inscriptions</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>
                <img class="barre-laterale__logo-universite" src="<?= app_url('images/university-logo.png') ?>" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal page-inscriptions">
                <header class="entete-page entete-inscriptions">
                    <h1>Gestion des inscriptions</h1>
                    <img class="separateur-page" src="<?= app_url('images/divider.svg') ?>" alt="" aria-hidden="true">
                </header>
                <?php if ($notice === 'accepted'): ?>
                    <p class="inscriptions-notice inscriptions-notice--success" role="status">La demande d’inscription a été validée.</p>
                <?php elseif ($notice === 'refused'): ?>
                    <p class="inscriptions-notice inscriptions-notice--success" role="status">La demande d’inscription a été refusée.</p>
                <?php endif; ?>
                <?php if ($actionError !== ''): ?>
                    <p class="inscriptions-notice inscriptions-notice--error" role="alert"><?= htmlspecialchars($actionError, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                <?php endif; ?>

                <section class="liste-inscriptions" aria-labelledby="titre-liste-inscriptions">
                    <h2 id="titre-liste-inscriptions">Liste des inscriptions</h2>
                    <?php if ($cards !== []): ?>
                        <div class="grille-inscriptions">
                            <?php foreach ($cards as $card): ?>
                                <button class="carte-inscription" type="button" data-inscription="<?= htmlspecialchars($card['data'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" aria-label="Ouvrir la demande de <?= htmlspecialchars($card['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                    <img src="<?= app_url('images/inscription-profile.png') ?>" alt="" aria-hidden="true">
                                    <span class="carte-inscription__nom"><?= htmlspecialchars($card['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                                    <span class="carte-inscription__experience"><?= htmlspecialchars($card['experiment'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="user_id" id="overlay-user-id">
            <input type="hidden" name="session_id" id="overlay-session-id">
            <div class="overlay-inscription__profil">
                <div class="overlay-inscription__identite">
                    <img src="<?= app_url('images/inscription-profile.png') ?>" alt="" aria-hidden="true">
                    <button class="bouton-profil" type="button" id="bouton-profil" aria-expanded="false">Voir profile</button>
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
    <script src="<?= app_url('Enseignant-chercheur/js/gestion-inscriptions.js?v=1') ?>"></script>
</body>
</html>
