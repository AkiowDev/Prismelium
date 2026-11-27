<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des expérimentations — Bureau</title>
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGEDesktop.css?v=4') ?>">
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
                    <a class="actuel" href="<?= app_url('index.php?page=experimentations') ?>" aria-current="page">Expérimentations</a>
                    <a href="<?= app_url('index.php?page=inscriptions') ?>">Inscriptions</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>
                <img class="barre-laterale__logo-universite" src="<?= app_url('images/university-logo.png') ?>" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal">
                <header class="entete-page">
                    <h1>Gestion des expérimentations</h1>
                    <img class="separateur-page" src="<?= app_url('images/divider.svg') ?>" alt="" aria-hidden="true">
                    <div class="actions-experiences" aria-label="Actions sur les expérimentations">
                        <a class="actions-experiences__lien" href="<?= app_url('index.php?page=experimentations-creer') ?>">Créer</a>
                        <form id="selection-actions" method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="archive_view" value="<?= htmlspecialchars($archiveView, ENT_QUOTES, 'UTF-8') ?>">
                            <?php if ($searchTerm !== ''): ?>
                                <input type="hidden" name="q" value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                            <button class="actions-experiences__archiver" type="submit" name="action" value="archive">Archiver la sélection</button>
                            <button class="actions-experiences__supprimer" type="submit" name="action" value="delete">Supprimer la sélection</button>
                        </form>
                    </div>
                    <img class="separateur-page" src="<?= app_url('images/divider.svg') ?>" alt="" aria-hidden="true">
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
                        <p class="formulaire-erreur" role="alert"><?= htmlspecialchars($actionError, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php if ((string) ($_GET['document_cleanup_failed'] ?? '') === '1'): ?>
                        <p class="etat-avertissement" role="alert">Les données ont été supprimées, mais un fichier numérique local n’a pas pu être retiré du stockage. Consultez les journaux de l’application.</p>
                    <?php endif; ?>

                    <div class="barre-outils-experiences">
                        <h2 id="titre-experiences-bureau">Liste des expérimentations</h2>
                        <form class="recherche" method="get" role="search">
                            <label class="texte-masque" for="recherche-experience-bureau">Rechercher une expérimentation</label>
                            <img class="recherche__icone" src="<?= app_url('images/search.png') ?>" alt="">
                            <input id="recherche-experience-bureau" name="q" type="search" placeholder="Rechercher" value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>">
                            <label class="texte-masque" for="filtre-archive">Expérimentations archivées</label>
                            <select id="filtre-archive" name="archive_view" class="recherche__archive">
                                <option value="hide" <?= $archiveView === 'hide' ? 'selected' : '' ?>>Masquer les archivées</option>
                                <option value="show" <?= $archiveView === 'show' ? 'selected' : '' ?>>Afficher les archivées</option>
                            </select>
                            <button class="recherche__filtre" type="submit" aria-label="Filtrer les expérimentations">
                                <img src="<?= app_url('images/filter.svg') ?>" alt="">
                            </button>
                        </form>
                    </div>
                    <p class="selection-experiences" id="compteur-selection" aria-live="polite">Aucune expérimentation sélectionnée.</p>

                    <?php if ($experiments !== []): ?>
                        <?php $colors = ['#7a72c0', '#d54444', '#81d04f', '#a2caff', '#ffb01f', '#fff7a1', '#c5cad2', '#1f8107', '#cac262']; ?>
                        <div class="grille-experiences">
                            <?php foreach ($experiments as $index => $experiment): ?>
                                <?php $color = $colors[$index % count($colors)]; ?>
                                <article class="carte-experience" aria-label="<?= htmlspecialchars($experiment['Nom_Exp'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                    <div class="carte-experience__couleur" style="--couleur-carte: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></div>
                                    <div class="carte-experience__intitule">
                                        <label class="selection-experience">
                                            <input type="checkbox" name="selected_experiments[]" value="<?= (int) $experiment['Id_Exp'] ?>" form="selection-actions" <?= in_array((int) $experiment['Id_Exp'], $selectedIds, true) ? 'checked' : '' ?> aria-label="Sélectionner <?= htmlspecialchars($experiment['Nom_Exp'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                            <span>Sélectionner</span>
                                        </label>
                                        <a
                                            class="carte-experience__tableau"
                                            href="<?= app_url('index.php?page=experimentations-tableau&id=' . (int) $experiment['Id_Exp']) ?>"
                                            aria-label="Ouvrir le tableau de bord de <?= htmlspecialchars($experiment['Nom_Exp'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                                        ><?= htmlspecialchars($experiment['Nom_Exp'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a>
                                        <small>
                                            <?= htmlspecialchars($experiment['Statut_Exp'], ENT_QUOTES, 'UTF-8') ?> ·
                                            <?= (int) $experiment['SessionCount'] ?>/<?= $experiment['Nb_Sessions_Prevues'] === null ? '—' : (int) $experiment['Nb_Sessions_Prevues'] ?> séance(s)
                                        </small>
                                        <?php if ($experiment['Statut_Exp'] !== 'Archivée'): ?>
                                            <a href="<?= app_url('index.php?page=experimentations-modifier&id=' . (int) $experiment['Id_Exp']) ?>" aria-label="Modifier l’expérimentation <?= htmlspecialchars($experiment['Nom_Exp'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Modifier</a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="etat-vide">Aucune expérimentation ne correspond à « <?= htmlspecialchars($searchTerm, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> ».</p>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
    <script src="<?= app_url('Enseignant-chercheur/js/gestion-experimentations.js?v=1') ?>"></script>
</body>
</html>
