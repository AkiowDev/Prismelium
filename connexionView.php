<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Prismelium</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            height: 100vh;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .login-wrapper {
            width: 450px;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .logo-container {
            margin-bottom: 30px;
        }
        .logo-container img {
            width: 160px; /* Ajustez la taille selon votre image */
            height: auto;
        }
        .error {
            color: #d9534f;
            background: #fdf7f7;
            padding: 10px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #b0b0b0;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus {
            border-color: #2b396e;
        }
        input::placeholder {
            color: #888888;
        }
        .input-hint {
            font-size: 13px;
            color: #555555;
            margin-top: 2px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #2b396e; /* Bleu foncé du LIUM */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 5px;
        }
        button[type="submit"]:hover {
            background-color: #1f2a52;
        }
        .forgot-password {
            font-size: 13px;
            color: #2b82cc;
            text-decoration: none;
            margin-top: -5px;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        .security-notice {
            font-size: 11px;
            color: #000000;
            line-height: 1.4;
            margin-top: 40px;
        }
        .security-notice span {
            color: #e67e22; /* Couleur orange pour "déconnecter" */
            font-weight: bold;
            cursor: pointer;
        }
        .img_haut {
            width: 100%;
            height: 492px;
            background: url("../images/v56_47.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            opacity: 1;
            position: absolute;
            top: 0px;
            left: 0px;
            overflow: hidden;
        }
        .img_bas {
            width: 100%;
            height: 492px;
            background: url("../images/v56_47.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            opacity: 1;
            position: absolute;
            top: 492px;
            left: 984px;
            overflow: hidden;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Connexion</h2>

        <!-- Affichage des messages d'erreur si existants -->
        <?php if (!empty($erreur)): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <!-- Le formulaire pointe vers le contrôleur -->
        <form action="connexionController.php" method="POST">
            <label for="mail">Adresse email :</label>
            <input type="email" id="mail" name="mail" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
    </div>

</body>
</html>