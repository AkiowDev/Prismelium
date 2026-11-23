<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Prismelium</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="connexion.css">
</head>
<body>

    <div class="page">
        <div class="img_haut"></div>
        <div class="img_bas"></div>

        <div class="gauche">
            <div class="logo"></div>

            <form action="connexionController.php" method="POST">
            <?php if (!empty($erreur)): ?>
                <div class="error-msg"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
                
                <div class="champ_mail">
                    <div class="mail">
                        <input type="text" id="mail" name="mail" placeholder="Identifiant..." required>
                    </div>
                </div>
                <div class="champs_id">Vous devez entrer votre identifiant.</div>

                <div class="champ_mdp">
                    <div class="mdp">
                        <input type="password" id="password" name="password" placeholder="Mot de passe..." required>
                    </div>
                </div>

                <a href="#" class="mdp_oublie">Mot de passe oublié ?</a>

                <button type="submit" class="bouton_connexion">
                    <span class="connexion">Se connecter</span>
                </button>
            </form>

            <div class="msg_secu">
                Pour des raisons de sécurité, veuillez vous <span>déconnecter</span> et fermer votre navigateur lorsque vous avez fini d'accéder aux services authentifiés.
            </div>
        </div>
    </div>

</body>
</html>