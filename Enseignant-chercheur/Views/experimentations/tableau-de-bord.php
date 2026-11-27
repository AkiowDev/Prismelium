<?php
$escape = static fn (mixed $value): string => htmlspecialchars(
    is_scalar($value) ? (string) $value : '',
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$participantPreview = array_slice($participants, 0, 8);
$additionalParticipants = array_slice($participants, 8);
$resourcePreview = array_slice($resources, 0, 8);
$additionalResources = array_slice($resources, 8);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de bord — <?= $escape($experiment['Nom_Exp']) ?></title>
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesGEDesktop.css?v=4') ?>">
    <link rel="stylesheet" href="<?= app_url('Enseignant-chercheur/css/stylesTableauExperimentationDesktop.css?v=1') ?>">
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
                    <a href="#">Gestion Planning</a>
                    <a href="#">Messagerie</a>
                    <a href="#">Paramètres</a>
                </nav>
                <img class="barre-laterale__logo-universite" src="<?= app_url('images/university-logo.png') ?>" alt="Le Mans Université">
            </aside>

            <main class="contenu-principal page-tableau-experience">
                <header class="tableau-experience__entete">
                    <div class="tableau-experience__titre">
                        <h1>Tableau de bord</h1>
                    </div>
                    <img class="tableau-experience__separateur" src="<?= app_url('images/dashboard-divider.svg') ?>" alt="" aria-hidden="true">
                    <div class="tableau-experience__actions" aria-label="Actions sur l’expérimentation">
                        <button class="tableau-experience__action" type="button" disabled>signaler incident</button>
                        <button class="tableau-experience__action" type="button" disabled>signaler annulation</button>
                        <button class="tableau-experience__action" type="button" disabled>Communiquer</button>
                    </div>
                    <img class="tableau-experience__separateur" src="<?= app_url('images/dashboard-divider.svg') ?>" alt="" aria-hidden="true">
                </header>

                <section class="informations-experience" aria-labelledby="titre-experience">
                    <h2 id="titre-experience"><?= $escape($experiment['Nom_Exp']) ?></h2>
                    <div class="description-experience">
                        <h3>Description :</h3>
                        <p><?= $escape($experiment['Desc_Exp'] ?? 'Aucune description renseignée.') ?></p>
                    </div>
                    <div class="details-experience">
                        <div>
                            <p><strong>Date de début:</strong> <?= (new DateTimeImmutable((string) $experiment['Date_Debut_Exp']))->format('d/m/Y') ?></p>
                            <p><strong>Date de fin:</strong> <?= (new DateTimeImmutable((string) $experiment['Date_Fin_Exp']))->format('d/m/Y') ?></p>
                        </div>
                        <p><strong>Durée :</strong> <?= $escape($duration) ?></p>
                    </div>
                    <p class="experience-responsable"><strong>Projet dirigé par :</strong> <?= $escape($researcherName) ?></p>
                </section>

                <img class="tableau-experience__separateur" src="<?= app_url('images/dashboard-divider.svg') ?>" alt="" aria-hidden="true">

                <section class="statistiques-experience" aria-label="Statistiques de l’expérimentation">
                    <p>nombre de participants: <strong><?= (int) $participantCount ?></strong></p>
                    <p>taux de participation: <strong><?= $participationRate === null ? '—' : (int) $participationRate . '%' ?></strong></p>
                    <p>nombre d’inscriptions: <strong><?= (int) $enrollmentCount ?></strong></p>
                </section>

                <div class="panneaux-experience">
                    <section class="panneau-experience" aria-labelledby="titre-participants">
                        <h2 id="titre-participants">Participants inscrits</h2>
                        <?php if ($participants !== []): ?>
                            <ul class="grille-participants">
                                <?php foreach ($participantPreview as $participant): ?>
                                    <?php $isValidated = $participant['Statut_Insc'] === 'Validee'; ?>
                                    <li class="carte-participant <?= $isValidated ? 'carte-participant--valide' : 'carte-participant--non-valide' ?>">
                                        <img src="<?= app_url('images/dashboard-participant-status.svg') ?>" alt="" aria-hidden="true">
                                        <span class="carte-participant__identite">
                                            <span class="carte-participant__nom"><?= $escape(trim($participant['Prenom_User'] . ' ' . $participant['Nom_User'])) ?></span>
                                            <span class="carte-participant__statut"><?= $isValidated ? 'Validé' : 'Non validé' ?></span>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ($additionalParticipants !== []): ?>
                                <details class="experience-plus">
                                    <summary>Voir plus</summary>
                                    <ul class="grille-participants">
                                        <?php foreach ($additionalParticipants as $participant): ?>
                                            <?php $isValidated = $participant['Statut_Insc'] === 'Validee'; ?>
                                            <li class="carte-participant <?= $isValidated ? 'carte-participant--valide' : 'carte-participant--non-valide' ?>">
                                                <img src="<?= app_url('images/dashboard-participant-status.svg') ?>" alt="" aria-hidden="true">
                                                <span class="carte-participant__identite">
                                                    <span class="carte-participant__nom"><?= $escape(trim($participant['Prenom_User'] . ' ' . $participant['Nom_User'])) ?></span>
                                                    <span class="carte-participant__statut"><?= $isValidated ? 'Validé' : 'Non validé' ?></span>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="experience-liste-vide">Aucun participant inscrit.</p>
                        <?php endif; ?>
                    </section>

                    <section class="panneau-experience" aria-labelledby="titre-ressources">
                        <h2 id="titre-ressources">Ressources associés</h2>
                        <?php if ($resources !== []): ?>
                            <ul class="grille-ressources">
                                <?php foreach ($resourcePreview as $resource): ?>
                                    <li class="carte-ressource">
                                        <img src="<?= app_url('images/dashboard-resource.svg') ?>" alt="" aria-hidden="true">
                                        <span>
                                            <span class="carte-ressource__nom"><?= $escape($resource['Nom_Res']) ?></span>
                                            <span class="carte-ressource__type">Type : <?= $escape($resource['Type_Res']) ?></span>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ($additionalResources !== []): ?>
                                <details class="experience-plus">
                                    <summary>Voir plus</summary>
                                    <ul class="grille-ressources">
                                        <?php foreach ($additionalResources as $resource): ?>
                                            <li class="carte-ressource">
                                                <img src="<?= app_url('images/dashboard-resource.svg') ?>" alt="" aria-hidden="true">
                                                <span>
                                                    <span class="carte-ressource__nom"><?= $escape($resource['Nom_Res']) ?></span>
                                                    <span class="carte-ressource__type">Type : <?= $escape($resource['Type_Res']) ?></span>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="experience-liste-vide">Aucune ressource associée.</p>
                        <?php endif; ?>
                    </section>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
