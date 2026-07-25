<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: login.php'); exit(); }
require(__DIR__ . '/database.php');
if (empty($_GET['id'])) { header('Location: index.php'); exit(); }
$id_produit = intval($_GET['id']);
$req = $bdd->prepare('SELECT * FROM produits WHERE id = ? AND id_utilisateur = ?');
$req->execute([$id_produit, $_SESSION['id']]);
if ($req->rowCount() == 0) { header('Location: index.php'); exit(); }
$produit = $req->fetch();
$req_img = $bdd->prepare('SELECT id, image FROM produit_images WHERE produit_id = ? ORDER BY id');
$req_img->execute([$id_produit]);
$images = $req_img->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le produit</title>
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
            <span class="bienvenue-minimal"> <?php echo htmlspecialchars($_SESSION['nom']); ?></span>
            <a href="actions/action_logout.php" class="nav-btn">Se déconnecter</a>
        </nav>
    </div>
</header>

<div class="formulaire-porter">
    <h2>Modifier le produit</h2>
    <?php if(!empty($_SESSION['erreur'])): ?>
        <p class="message-erreur-porter"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></p>
    <?php endif; ?>
    <form action="actions/action_modifier_produit.php?id=<?php echo $id_produit; ?>" method="POST" enctype="multipart/form-data">
        <div class="champ-porter">
            <label>Nom</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
        </div>
        <div class="champ-porter">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($produit['description']); ?></textarea>
        </div>
        <div class="champ-porter">
            <label>Prix (UM)</label>
            <input type="number" name="prix" step="0.01" value="<?php echo $produit['prix']; ?>">
        </div>
        <div class="champ-porter">
            <label>Images actuelles</label>
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
                <?php foreach ($images as $img): ?>
                    <div style="position:relative; width:100px; height:100px; overflow:hidden; border:1px solid #ddd;">
                        <img src="<?php echo htmlspecialchars($img['image']); ?>" style="width:100%; height:100%; object-fit:cover;">
                        <a href="actions/action_supprimer_image.php?id=<?php echo $img['id']; ?>&produit=<?php echo $id_produit; ?>" style="position:absolute; top:2px; right:2px; background:rgba(0,0,0,0.7); color:#fff; border:none; border-radius:50%; width:24px; height:24px; text-align:center; line-height:24px; text-decoration:none; font-size:16px;" onclick="return confirm('Supprimer ?')">×</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="champ-porter">
            <label>Ajouter des photos</label>
            <input type="file" name="new_images[]" accept="image/*" multiple>
        </div>
        <button type="submit">Enregistrer</button>
    </form>
    <div style="margin-top:24px; text-align:center;">
        <a href="actions/action_supprimer_produit.php?id=<?php echo $id_produit; ?>" class="btn-supprimer-porter" onclick="return confirm('Supprimer définitivement ?')">Supprimer ce produit</a>
    </div>
    <div class="lien-bas-porter">
        <a href="index.php">Retour</a>
    </div>
</div>

<style>
.btn-supprimer-porter {
    display: inline-block;
    padding: 10px 24px;
    background: #c0392b;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s;
}
.btn-supprimer-porter:hover {
    background: #a93226;
}
</style>

</body>
</html>