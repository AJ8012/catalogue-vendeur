<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue - Connexion</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3FXBWCRQQR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-3FXBWCRQQR');
    </script>

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="formulaire">
        <h1>Catalogue Vendeurs</h1>
        <h2>Connexion</h2>

        <?php
        if (!empty($_SESSION['id'])) {
            header('Location: index.php');
            exit();
        }

        if (!empty($_SESSION['erreur'])): ?>
            <p class="message-erreur"><?php echo $_SESSION['erreur']; ?></p>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form action="actions/action_login.php" method="POST">
            <div class="champ">
                <label>Numéro de téléphone :</label>
                <input type="tel" name="telephone" maxlength="8" placeholder="12345678" required>
            </div>

            <div class="champ">
                <label>Mot de passe (4 chiffres) :</label>
                <input type="password" name="mdp" maxlength="4" placeholder="1234" required>
            </div>

            <button type="submit">Se connecter</button>
        </form>

        <div class="lien-bas">
            Pas encore inscrit ? <a href="signup.php">Inscrivez-vous ici</a>
        </div>
    </div>

</body>
</html>