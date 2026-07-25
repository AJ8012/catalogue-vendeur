<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3FXBWCRQQR"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-3FXBWCRQQR');</script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header-minimal">
    <div class="header-inner">
        <div class="logo"><a href="index.php">Catalogue Vendeur</a></div>
        <nav class="nav-links">
            <a href="signup.php" class="nav-btn">S'inscrire</a>
        </nav>
    </div>
</header>

<div class="formulaire-porter">
    <h1>Connexion</h1>
    <h2>Veuillez vous identifier</h2>
    <?php if(!empty($_SESSION['erreur'])): ?>
        <p class="message-erreur-porter"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></p>
    <?php endif; ?>
    <form action="actions/action_login.php" method="POST">
        <div class="champ-porter">
            <label>Téléphone</label>
            <input type="tel" name="telephone" maxlength="8" placeholder="+222" required>
        </div>
        <div class="champ-porter">
            <label>Mot de passe (4 chiffres)</label>
            <input type="password" name="mdp" maxlength="4" placeholder="1234" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
    <div class="lien-bas-porter">
        Pas encore inscrit ? <a href="signup.php">Créez un compte</a>
    </div>
</div>

</body>
</html>