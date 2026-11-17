<?php
$experiments = [
    ['title' => 'Expérimentation', 'color' => '#7a72c0'],
    ['title' => 'Expérimentation', 'color' => '#d54444'],
    ['title' => 'Expérimentation', 'color' => '#81d04f'],
    ['title' => 'Expérimentation', 'color' => '#a2caff'],
    ['title' => 'Expérimentation', 'color' => '#ffb01f'],
    ['title' => 'Expérimentation', 'color' => '#fff7a1'],
    ['title' => 'Expérimentation', 'color' => '#c5cad2'],
    ['title' => 'Expérimentation', 'color' => '#1f8107'],
    ['title' => 'Expérimentation', 'color' => '#cac262'],
];

$searchTerm = trim((string) ($_GET['q'] ?? ''));

$visibleExperiments = array_filter(
    $experiments,
    static fn (array $experiment): bool => $searchTerm === ''
        || stripos($experiment['title'], $searchTerm) !== false
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des expérimentations — Bureau</title>
    <link rel="stylesheet" href="../css/stylesGEDesktop.css">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
    <aside class="barre-laterale" aria-label="Navigation principale">
        <a class="barre-laterale__marque" href="#" aria-label="Accueil">
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
                <button type="button">Créer</button>
                <button type="button">Archiver</button>
                <button type="button">modifier</button>
                <button type="button">supprimer</button>
            </div>
            <img class="separateur-page" src="../assets/divider.svg" alt="" aria-hidden="true">
        </header>

        <section class="section-experiences" aria-labelledby="titre-experiences-bureau">
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
                    <button class="recherche__filtre" type="button" aria-label="Filtrer les expérimentations">
                        <img src="../assets/filter.svg" alt="">
                    </button>
                </form>
            </div>

            <?php if ($visibleExperiments !== []): ?>
                <div class="grille-experiences">
                    <?php foreach ($visibleExperiments as $index => $experiment): ?>
                        <article class="carte-experience" aria-label="<?= htmlspecialchars($experiment['title'], ENT_QUOTES, 'UTF-8') ?> <?= $index + 1 ?>">
                            <div
                                class="carte-experience__couleur"
                                style="--couleur-carte: <?= htmlspecialchars($experiment['color'], ENT_QUOTES, 'UTF-8') ?>"
                                aria-hidden="true"
                            ></div>
                            <div class="carte-experience__intitule">
                                <?= htmlspecialchars($experiment['title'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="etat-vide">Aucune expérimentation ne correspond à « <?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?> ».</p>
            <?php endif; ?>
        </section>
    </main>
</div>

    </div>
</body>
</html>
