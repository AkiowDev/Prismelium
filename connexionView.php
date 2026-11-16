<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - LIUM</title>
    <!-- Importation de la police Poppins depuis Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
        }
        .page {
            width: 100%;
            height: 1024px;
            background: rgba(255,255,255,1);
            opacity: 1;
            position: absolute;
            top: 0px;
            left: 0px;
            overflow: hidden;
        }
        .img_haut {
            width: calc(100% - 29px);
            height: 492px;
            background: url("../images/img-connexion.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            opacity: 1;
            position: absolute;
            top: 0px;
            left: 29px;
            overflow: hidden;
            clip-path: inset(0 0 0 700px);
        }
        .img_bas {
            width: calc(100% - 729px);
            height: 532px;
            background: url("../images/img-connexion.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            opacity: 1;
            position: absolute;
            top: 493px;
            left: 729px;
            overflow: hidden;
            width: 100%;
        }
        .gauche {
            width: 597px;
            height: 100%;
            background: rgba(255,255,255,1);
            opacity: 1;
            position: absolute;
            top: 0px;
            left: 0px;
            overflow: hidden;
        }
        .logo {
            width: 251px;
            height: 161px;
            background: url("../images/logo-lium.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
            opacity: 1;
            position: absolute;
            top: 165px;
            left: 173px;
            overflow: hidden;
        }
        .champ_mail {
            width: 430px;
            height: 72px;
            background: rgba(161,161,161,1);
            opacity: 1;
            position: absolute;
            top: 362px;
            left: 83px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            overflow: hidden;
        }
        .mail {
            width: 424px;
            height: 67px;
            background: rgba(255,255,255,1);
            position: absolute;
            top: 2px;
            left: 3px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            display: flex;
            align-items: center;
        }
        .mail input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding-left: 20px;
            color: rgba(154,154,154,1);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 22px;
        }
        .champ_mdp {
            width: 430px;
            height: 63px;
            background: rgba(161,161,161,1);
            opacity: 1;
            position: absolute;
            top: 512px;
            left: 83px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            overflow: hidden;
        }
        .mdp {
            width: 424px;
            height: 58px;
            background: rgba(255,255,255,1);
            position: absolute;
            top: 2px;
            left: 3px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            display: flex;
            align-items: center;
        }
        .mdp input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding-left: 20px;
            color: rgba(154,154,154,1);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 22px;
        }
        .champs_id {
            width: 421px;
            color: rgba(154,154,154,1);
            position: absolute;
            top: 434px;
            left: 93px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 17px;
            text-align: left;
        }
        .bouton_connexion {
            width: 424px;
            height: 67px;
            background: rgba(40,47,99,1);
            opacity: 1;
            position: absolute;
            top: 651px;
            left: 86px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            border: none;
            cursor: pointer;
        }
        .connexion {
            width: 100%;
            color: rgba(255,255,255,1);
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 0px;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }
        .msg_secu {
            width: 522px;
            color: #000000;
            position: absolute;
            top: 823px;
            left: 37px;
            font-family: 'Poppins', sans-serif;
            font-weight: bold;
            font-size: 17px;
            text-align: left;
            line-height: 1.4;
        }
        .msg_secu span {
            color: #e67e22;
        }
        .mdp_oublie {
            width: 185px;
            color: rgba(96,160,255,1);
            position: absolute;
            top: 739px;
            left: 86px;
            font-family: 'Poppins', sans-serif;
            font-weight: normal;
            font-size: 17px;
            text-align: left;
            text-decoration: none;
            display: inline-block;
        }
        .error-msg {
            position: absolute;
            top: 580px;
            left: 90px;
            color: #d9534f;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="page">
        <div class="img_haut"></div>
        <div class="img_bas"></div>

        <div class="gauche">
            <div class="logo"></div>

            <?php if (!empty($erreur)): ?>
                <div class="error-msg"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form action="connexionController.php" method="POST">
                
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