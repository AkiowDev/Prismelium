<?php
declare(strict_types=1);

require_once __DIR__ . '/../BDD_control/current_researcher.php';

$searchTerm = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
$searchQuery = $pdo->prepare(
    'SELECT e.Id_Exp, e.Nom_Exp, e.Date_Debut_Exp, e.Date_Fin_Exp, e.Statut_Exp,
            e.Nb_Sessions_Prevues, COALESCE(s.session_count, 0) AS SessionCount
     FROM EXPERIMENTATION e
     LEFT JOIN (
         SELECT Id_Exp, COUNT(*) AS session_count
         FROM `SESSION`
         GROUP BY Id_Exp
     ) s ON s.Id_Exp = e.Id_Exp
     WHERE e.Id_Equipe = :team_id AND e.Nom_Exp LIKE :search
     ORDER BY e.Date_Debut_Exp DESC, e.Id_Exp DESC'
);
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
    <link rel="stylesheet" href="../css/stylesGEDesktop.css?v=3">
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
                <button type="button" disabled title="Cette action n’est pas encore disponible">Archiver</button>
                <button type="button" disabled title="Cette action n’est pas encore disponible">Supprimer</button>
            </div>
            <img class="separateur-page" src="../assets/divider.svg" alt="" aria-hidden="true">
        </header>

        <section class="section-experiences" aria-labelledby="titre-experiences-bureau">
            <?php if ((string) ($_GET['created'] ?? '') === '1'): ?>
                <p class="etat-succes" role="status">L’expérimentation a bien été créée.</p>
            <?php elseif ((string) ($_GET['updated'] ?? '') === '1'): ?>
                <p class="etat-succes" role="status">L’expérimentation a bien été modifiée.</p>
            <?php endif; ?>
            <?php if ((string) ($_GET['document_cleanup_failed'] ?? '') === '1'): ?>
                <p class="etat-avertissement" role="alert">L’association du document a été supprimée, mais son fichier n’a pas pu être retiré du stockage. Consultez les journaux de l’application.</p>
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
                    <button class="recherche__filtre" type="submit" aria-label="Filtrer les expérimentations">
                        <img src="../assets/filter.svg" alt="">
                    </button>
                </form>
            </div>

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
                                <span><?= $escape($experiment['Nom_Exp']) ?></span>
                                <small>
                                    <?= $escape($experiment['Statut_Exp']) ?> ·
                                    <?= (int) $experiment['SessionCount'] ?>/<?= $experiment['Nb_Sessions_Prevues'] === null ? '—' : (int) $experiment['Nb_Sessions_Prevues'] ?> séance(s)
                                </small>
                                <a
                                    href="./ModifierExperimentationBureau.php?id=<?= (int) $experiment['Id_Exp'] ?>"
                                    aria-label="Modifier l’expérimentation <?= $escape($experiment['Nom_Exp']) ?>"
                                >Modifier</a>
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
</body>
</html>
