<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
            <a href="login.php" class="nav-btn">Connexion</a>
        </nav>
    </div>
</header>

<div class="formulaire-porter">
    <h2>Créer un compte</h2>
    <?php if(!empty($_SESSION['erreur'])): ?>
        <p class="message-erreur-porter"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></p>
    <?php endif; ?>
    <form action="actions/action_signup.php" method="POST">
        <div class="champ-porter">
            <label>Nom complet</label>
            <input type="text" name="nom" placeholder="The Rock" required>
        </div>
        <div class="champ-porter">
            <label>Téléphone (8 chiffres)</label>
            <input type="tel" name="telephone" pattern="[0-9]{8}" maxlength="8" placeholder="12345678" required>
        </div>
        <div class="champ-porter">
            <label>Mot de passe (4 chiffres)</label>
            <input type="password" name="mdp" pattern="[0-9]{4}" maxlength="4" placeholder="1234" required>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    <div class="lien-bas-porter">
        Déjà inscrit ? <a href="login.php">Connectez-vous</a>
    </div>
</div>

</body>
</html>