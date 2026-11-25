<?php
$escape = static fn (mixed $value): string => htmlspecialchars(
    is_scalar($value) ? (string) $value : '',
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$customRestrictionValues = is_array($formData['restriction_personnalisee'] ?? null)
    ? $formData['restriction_personnalisee']
    : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEditing ? 'Modifier' : 'Créer' ?> une expérimentation — Bureau</title>
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGEDesktop.css?v=3') ?>">
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesCreerExperimentationDesktop.css?v=6') ?>">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
            <aside class="barre-laterale" aria-label="Navigation principale">
                <a class="barre-laterale__marque" href="<?= app_url('index.php?page=enseignant-chercheur') ?>" aria-label="Accueil">
                    <img class="barre-laterale__logo-cerveau" src="<?= app_url('images/brain-logo.png') ?>" alt="">
                </a>
                <nav class="barre-laterale__navigation">
                    <a href="#">Rétributions</a>
                    <a class="actuel" href="<?= app_url('index.php?page=experimentations') ?>" aria-current="page">Expérimentations</a>
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>
                <img class="barre-laterale__logo-universite" src="<?= app_url('images/university-logo.png') ?>" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal page-creation">
                <header class="page-creation__entete">
                    <h1><?= $isEditing ? 'Modifier' : 'Créer' ?> une expérimentation</h1>
                </header>
                <?php if ($formError !== ''): ?>
                    <p class="formulaire-erreur" role="alert"><?= $escape($formError) ?></p>
                <?php endif; ?>

                <form class="formulaire-creation" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="experiment_id" value="<?= (int) $experimentId ?>">
                    <?php endif; ?>
                    <section class="informations-experimentation" aria-label="Informations de l’expérimentation">
                        <div class="champ champ--titre">
                            <label for="titre-experimentation">Titre de l’expérimentation</label>
                            <input id="titre-experimentation" name="titre" type="text" maxlength="100" required value="<?= $escape($formData['titre'] ?? '') ?>">
                        </div>
                        <div class="champ champ--description">
                            <label for="description-experimentation">Description :</label>
                            <textarea id="description-experimentation" name="description" rows="3" maxlength="1000"><?= $escape($formData['description'] ?? '') ?></textarea>
                        </div>
                        <div class="details-experimentation">
                            <div class="champ-inline champ-inline--date-debut">
                                <label for="date-debut">Date de début:</label>
                                <input id="date-debut" name="date_debut" type="date" required value="<?= $escape($formData['date_debut'] ?? '') ?>">
                            </div>
                            <div class="champ-inline champ-inline--duree">
                                <label for="duree">Durée (min) :</label>
                                <input id="duree" name="duree" type="number" min="1" max="1439" required value="<?= $escape($formData['duree'] ?? '') ?>">
                            </div>
                            <div class="champ-inline champ-inline--date-fin">
                                <label for="date-fin">Date de fin:</label>
                                <input id="date-fin" name="date_fin" type="date" required value="<?= $escape($formData['date_fin'] ?? '') ?>">
                            </div>
                            <div class="champ-inline champ-inline--sessions">
                                <label for="nombre-sessions">Nombre de sessions prévues :</label>
                                <input id="nombre-sessions" name="nombre_sessions" type="number" min="1" max="1000" required value="<?= $escape($formData['nombre_sessions'] ?? '') ?>">
                            </div>
                        </div>

                        <fieldset class="choix-retribution">
                            <legend>Rétribution?</legend>
                            <div class="boutons-radio">
                                <label class="bouton-radio">
                                    <input type="radio" name="retribution" value="oui" required <?= ($formData['retribution'] ?? '') === 'oui' ? 'checked' : '' ?>>
                                    <span>Oui</span>
                                </label>
                                <label class="bouton-radio">
                                    <input type="radio" name="retribution" value="non" required <?= ($formData['retribution'] ?? '') === 'non' ? 'checked' : '' ?>>
                                    <span>Non</span>
                                </label>
                            </div>
                        </fieldset>
                        <div class="champ-inline champ-inline--type-retribution">
                            <label for="type-retribution">Type de rétribution?</label>
                            <select id="type-retribution" name="type_retribution">
                                <option value="">Sélectionner</option>
                                <option value="financiere" <?= ($formData['type_retribution'] ?? '') === 'financiere' ? 'selected' : '' ?>>Financière</option>
                                <option value="autre" <?= ($formData['type_retribution'] ?? '') === 'autre' ? 'selected' : '' ?>>Autre</option>
                                <?php if (($formData['type_retribution'] ?? '') === 'valeur_existante'): ?>
                                    <option value="valeur_existante" selected><?= $escape($editingExperiment['Type_Retribution'] ?? '') ?> (conservée)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </section>

                    <hr class="separateur-creation">

                    <section class="restrictions-participation" aria-labelledby="titre-restrictions">
                        <h2 id="titre-restrictions">Restrictions de participation</h2>
                        <div class="restriction-age">
                            <label for="age-minimum">Âge minimum?</label>
                            <input id="age-minimum" name="age_minimum" type="number" min="0" max="120" value="<?= $escape($formData['age_minimum'] ?? '') ?>">
                        </div>
                        <fieldset class="restriction-genre">
                            <legend>Genre?</legend>
                            <label><input type="radio" name="genre" value="homme" <?= ($formData['genre'] ?? '') === 'homme' ? 'checked' : '' ?>> Homme</label>
                            <label><input type="radio" name="genre" value="femme" <?= ($formData['genre'] ?? '') === 'femme' ? 'checked' : '' ?>> Femme</label>
                            <label><input type="radio" name="genre" value="indifferent" <?= ($formData['genre'] ?? '') === 'indifferent' ? 'checked' : '' ?>> n’importe</label>
                        </fieldset>
                        <div class="restriction-langue">
                            <label for="langue">Langue(s)?</label>
                            <select id="langue" name="langue">
                                <option value="">Sélectionner</option>
                                <option value="francais" <?= ($formData['langue'] ?? '') === 'francais' ? 'selected' : '' ?>>Français</option>
                                <option value="anglais" <?= ($formData['langue'] ?? '') === 'anglais' ? 'selected' : '' ?>>Anglais</option>
                            </select>
                        </div>
                        <fieldset class="restriction-lunettes">
                            <legend>Port de lunettes?</legend>
                            <label><input type="radio" name="lunettes" value="oui" <?= ($formData['lunettes'] ?? '') === 'oui' ? 'checked' : '' ?>> Oui</label>
                            <label><input type="radio" name="lunettes" value="non" <?= ($formData['lunettes'] ?? '') === 'non' ? 'checked' : '' ?>> Non</label>
                        </fieldset>
                        <div class="fiche-consentement">
                            <label for="fiche-consentement">Fiche de consentement</label>
                            <label class="fiche-consentement__fichier" for="fiche-consentement">
                                <span id="nom-fiche-consentement"><?= $escape($_FILES['fiche_consentement']['name'] ?? 'Aucun fichier sélectionné') ?></span>
                                <input id="fiche-consentement" name="fiche_consentement" type="file" accept=".pdf,application/pdf">
                            </label>
                        </div>
                        <div class="restrictions-personnalisees" id="restrictions-personnalisees">
                            <?php foreach ($customRestrictionValues as $index => $restriction): ?>
                                <label class="restriction-personnalisee">
                                    Autre restriction <?= (int) $index + 1 ?>
                                    <input type="text" name="restriction_personnalisee[]" maxlength="50" autocomplete="off" aria-label="Préciser la restriction <?= (int) $index + 1 ?>" value="<?= $escape($restriction) ?>">
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button class="bouton-action bouton-action--restriction" type="button" id="ajouter-restriction">Ajouter une restriction</button>
                    </section>

                    <hr class="separateur-creation separateur-creation--ressources">
                    <button class="bouton-action bouton-action--ressource" type="button" id="associer-ressource">Associer une ressource numérique</button>
                    <input class="import-ressource__fichier" id="fichiers-ressources" name="ressources_numeriques[]" type="file" accept=".csv,.doc,.docx,.jpeg,.jpg,.mp4,.pdf,.png,.ppt,.pptx,.txt,.xls,.xlsx" multiple tabindex="-1" aria-hidden="true">

                    <section class="ressources-associees" id="ressources-associees" data-existing-count="<?= count($existingDocuments) ?>" aria-labelledby="titre-ressources">
                        <div class="ressources-associees__entete">
                            <h2 id="titre-ressources">Documents numériques associés</h2>
                            <span class="ressources-associees__compteur" id="compteur-ressources" aria-live="polite">
                                <?php if ($remainingExistingDocumentCount > 0): ?>
                                    <?= $remainingExistingDocumentCount ?> document<?= $remainingExistingDocumentCount > 1 ? 's' : '' ?> associé<?= $remainingExistingDocumentCount > 1 ? 's' : '' ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($existingDocuments !== []): ?>
                            <div class="ressources-associees__existantes">
                                <p>Cochez les documents à supprimer :</p>
                                <ul>
                                    <?php foreach ($existingDocuments as $document): ?>
                                        <li>
                                            <span><?= $escape($document['Nom_Doc']) ?></span>
                                            <label>
                                                <input type="checkbox" name="documents_a_supprimer[]" value="<?= (int) $document['Id_Doc'] ?>" <?= in_array((int) $document['Id_Doc'], $documentsToDelete, true) ? 'checked' : '' ?>>
                                                Supprimer
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <p class="ressources-associees__vide" id="message-ressources-vides" <?= $remainingExistingDocumentCount > 0 ? 'hidden' : '' ?>>Aucune ressource associée.</p>
                        <ul class="ressources-associees__liste" id="liste-ressources"></ul>
                    </section>

                    <div class="actions-formulaire">
                        <button class="bouton-action bouton-action--creer" type="submit"><?= $isEditing ? 'Enregistrer les modifications' : 'Créer' ?></button>
                        <a class="bouton-action" href="<?= app_url('index.php?page=experimentations') ?>">Annuler</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <script src="<?= app_url('Enseignant-chercheur/js/creer-experimentation.js?v=4') ?>"></script>
</body>
</html>
