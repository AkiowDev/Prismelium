<?php require_once 'experimentationsParticipantController.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expérimentations — LIUM</title>
    <link rel="stylesheet" href="experimentationsParticipant.css">
</head>
<body>
    <div class="application">
        <div class="version-bureau">
    <aside class="barre-laterale" aria-label="Navigation principale">
        <a class="barre-laterale__marque" href="http://localhost/Prismelium/Prismelium/accueilParticipant/accueilParticipantView.php" aria-label="Accueil">
            <img class="barre-laterale__logo-cerveau" src="../images/brain-logo.png" alt="">
        </a>
        <nav class="barre-laterale__navigation">
            <a href="#">Expérimentations</a>
            <a href="#">Messagerie</a>
            <a href="#">Planning</a>
            <a href="#">Paramètres</a>
        </nav>
        <img class="barre-laterale__logo-universite" src="../images/university-logo.png" alt="Le Mans Université">
    </aside>

    <main class="contenu-principal">

        <section class="section-experiences" aria-labelledby="titre-experiences-bureau">
            <div class="barre-outils-experiences">
                <h2 id="titre-experiences-bureau">Liste des expérimentations</h2>
                <form class="recherche" method="get" role="search">
                    <label class="texte-masque" for="recherche-experience-bureau">Rechercher une expérimentation</label>
                    <img class="recherche__icone" src="../images/search.png" alt="">
                    <input
                        id="recherche-experience-bureau"
                        name="q"
                        type="search"
                        placeholder="Rechercher"
                        value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') //Voir controller apres html/css fini ?>"
                    >
                    <button class="recherche__filtre" type="button" aria-label="Filtrer les expérimentations">
                        <img src="../images/filter.svg" alt="">
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