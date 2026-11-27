<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des rétributions — Bureau</title>
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGEDesktop.css?v=4') ?>">
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGestionRetributionsDesktop.css?v=1') ?>">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="<?= app_url('index.php?page=enseignant-chercheur') ?>" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="<?= app_url('images/brain-logo.png') ?>" alt="">
                </a>
                <nav class="barre-laterale__navigation">
                    <a href="<?= app_url('index.php?page=retributions') ?>" aria-current="page">Rétributions</a>
                    <a href="<?= app_url('index.php?page=experimentations') ?>">Expérimentations</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>
                <img class="barre-laterale__logo-universite" src="<?= app_url('images/university-logo.png') ?>" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal page-retributions">
                <header class="entete-page entete-retributions">
                    <h1>Gestion des rétributions</h1>
                </header>

                <section class="recherche-retributions" aria-labelledby="titre-recherche-retributions">
                    <h2 id="titre-recherche-retributions">Rechercher un utilisateur</h2>
                    <form method="get" action="<?= app_url('index.php') ?>">
                        <input type="hidden" name="page" value="retributions">
                        <label for="recherche-utilisateur">Rechercher un utilisateur par adresse électronique ou nom de ceux-ci :</label>
                        <input
                            id="recherche-utilisateur"
                            name="q"
                            type="search"
                            value="<?= htmlspecialchars($search, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                            maxlength="120"
                        >
                    </form>
                </section>

                <?php if ($notice === 'sent'): ?>
                    <p class="retributions-notice retributions-notice--success" role="status">La rétribution a été marquée comme envoyée.</p>
                <?php endif; ?>
                <?php if ($actionError !== ''): ?>
                    <p class="retributions-notice retributions-notice--error" role="alert"><?= htmlspecialchars($actionError, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                <?php endif; ?>

                <section class="liste-retributions" aria-labelledby="titre-liste-retributions">
                    <h2 id="titre-liste-retributions">Liste des personnes en attente de rétributions</h2>
                    <?php if ($cards !== []): ?>
                        <div class="grille-retributions">
                            <?php foreach ($cards as $card): ?>
                                <button
                                    class="carte-retribution"
                                    type="button"
                                    data-user-id="<?= $card['userId'] ?>"
                                    data-session-id="<?= $card['sessionId'] ?>"
                                    aria-label="Envoyer la rétribution à <?= htmlspecialchars($card['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> pour <?= htmlspecialchars($card['experiment'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                                >
                                    <img src="<?= app_url('images/inscription-profile.png') ?>" alt="" aria-hidden="true">
                                    <span><?= htmlspecialchars($card['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="retributions-vides">Aucune personne n’est en attente d’une rétribution.</p>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>

    <dialog class="overlay-retribution" id="overlay-retribution" aria-labelledby="confirmation-retribution">
        <form method="post" action="<?= app_url('index.php?page=retributions') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="user_id" id="retribution-user-id">
            <input type="hidden" name="session_id" id="retribution-session-id">
            <p id="confirmation-retribution">Voulez-vous envoyer la rétribution à ce participant?</p>
            <button type="submit">Valider</button>
        </form>
    </dialog>
    <script src="<?= app_url('Enseignant-chercheur/js/gestion-retributions.js?v=1') ?>"></script>
</body>
</html>
