
<link rel="stylesheet" href="rediger.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <title>Prismelium - Rédiger</title>
<div class="rediger_message">

    <h2>Nouveau message</h2>

    <!-- Recherche du destinataire -->
    <form action="rediger.php" method="POST">

        <label for="mail_destinataire">
            Adresse mail du destinataire :
        </label>

        <input
            type="text"
            id="mail_destinataire"
            name="mail_destinataire"
            value="<?= htmlspecialchars($recherche ?? '') ?>"
            placeholder="Saisir une adresse mail"
            required
        >

        <button type="submit" name="rechercher">
            Rechercher
        </button>

    </form>

    <!-- Affichage des résultats -->
    <?php if (!empty($utilisateurs)): ?>

        <h3>Résultats :</h3>

        <?php foreach ($utilisateurs as $utilisateur): ?>

            <form action="rediger.php" method="POST">

                <input
                    type="hidden"
                    name="id_destinataire"
                    value="<?= (int)$utilisateur['Id_User'] ?>"
                >

                <button type="submit" name="selectionner">
                    <?= htmlspecialchars($utilisateur['Mail_User']) ?>
                </button>

            </form>

        <?php endforeach; ?>

    <?php endif; ?>

    <!-- Zone de rédaction -->
    <?php if (!empty($id_destinataire)): ?>

        <form action="rediger.php" method="POST">

            <input
                type="hidden"
                name="id_destinataire"
                value="<?= (int)$id_destinataire ?>"
            >

            <?php if (!empty($destinataire)): ?>

            <h3>
                Envoyer à
                <?= htmlspecialchars($destinataire['Prenom_User']) ?>
                <?= htmlspecialchars($destinataire['Nom_User']) ?> :
            </h3>

<?php endif; ?>

            <textarea id="contenu" name="contenu" required></textarea>

            <button type="submit" name="envoyer">
                Envoyer
            </button>
            <button type="submit" name="brouillon">
                Enregistrer comme brouillon
            </button>
        </form>

    <?php endif; ?>

</div>
