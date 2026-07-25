<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: login.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
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
            <span class="bienvenue-minimal">👋 <?php echo htmlspecialchars($_SESSION['nom']); ?></span>
            <a href="actions/action_logout.php" class="nav-btn">Se déconnecter</a>
        </nav>
    </div>
</header>

<div class="formulaire-porter">
    <h2>Ajouter un produit</h2>
    <?php if(!empty($_SESSION['erreur'])): ?>
        <p class="message-erreur-porter"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></p>
    <?php endif; ?>
    <form action="actions/ajouter_produit.php" method="POST" enctype="multipart/form-data">
        <div class="champ-porter">
            <label>Nom du produit</label>
            <input type="text" name="nom" placeholder="Ex: T-shirt blanc" required>
        </div>
        <div class="champ-porter">
            <label>Description</label>
            <textarea name="description" placeholder="Décrivez votre produit..."></textarea>
        </div>
        <div class="champ-porter">
            <label>Prix (UM)</label>
            <input type="number" name="prix" step="0.01" placeholder="Ex: 2500">
        </div>
        <div class="champ-porter">
            <label>Photos</label>
            <input type="file" name="images[]" accept="image/*" multiple>
        </div>
        <button type="submit">Mettre en ligne</button>
    </form>
    <div class="lien-bas-porter">
        <a href="index.php">Retour à l'accueil</a>
    </div>
</div>

</body>
</html>