<?php
/** @var array $contacts */

?>

<div class="contacts-container">

    <h2>Mes contacts</h2>

    <form method="GET" action="contacts.php" class="recherche-contacts">
        <input
            type="text"
            name="recherche"
            placeholder="Rechercher un nom, prénom ou e-mail..."
            value="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit">Rechercher</button>
    </form>

    <div class="liste-contacts">

        <?php if (count($contacts) > 0): ?>

            <?php foreach ($contacts as $contact): ?>

                <div class="contact">

                    <h3>
                        <?= htmlspecialchars(
                            $contact['Prenom_User'] . ' ' . $contact['Nom_User'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars(
                            $contact['Mail_User'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>Aucun contact trouvé.</p>

        <?php endif; ?>

    </div>

</div>
