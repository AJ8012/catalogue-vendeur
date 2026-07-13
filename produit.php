<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/database.php');

if (empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_produit = intval($_GET['id']);

// Récupérer le produit + téléphone du vendeur
$req = $bdd->prepare('
    SELECT p.*, u.telephone AS vendeur_telephone
    FROM produits p
    JOIN utilisateurs u ON p.id_utilisateur = u.id
    WHERE p.id = ?
');
$req->execute(array($id_produit));

if ($req->rowCount() == 0) {
    header('Location: index.php');
    exit();
}

$produit = $req->fetch();

// Récupérer les images
$req_img = $bdd->prepare('SELECT image FROM produit_images WHERE produit_id = ? ORDER BY id ASC');
$req_img->execute([$id_produit]);
$images = $req_img->fetchAll();

$message_whatsapp = urlencode("Bonjour, je suis intéressé par le produit : " . $produit['nom']);
?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($produit['nom']); ?> - Catalogue</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="header">
        <h1>Catalogue Vendeur</h1>
        <a href="index.php" class="btn">⬅ Retour à l'accueil</a>
    </div>

    <div class="page-produit">
        <?php if (!empty($images)): ?>
            <div class="galerie-images" style="display:flex; flex-wrap:wrap; gap:10px;">
                <?php foreach ($images as $img): ?>
                    <img src="uploads/<?php echo htmlspecialchars($img['image']); ?>"
                         alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                         style="max-width:200px; height:auto;">
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <img src="uploads/placeholder.png" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($produit['nom']); ?></h2>

        <?php if (!empty($produit['description'])): ?>
            <p class="description"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
        <?php endif; ?>

        <?php if (!empty($produit['prix']) && $produit['prix'] > 0): ?>
            <p class="prix"><?php echo number_format($produit['prix'], 2); ?> €</p>
        <?php else: ?>
            <p class="prix">Prix sur demande</p>
        <?php endif; ?>

      

<a href="https://wa.me/<?php echo $produit['vendeur_telephone']; ?>?text=<?php echo $message_whatsapp; ?>"
   class="btn btn-ajout"
   target="_blank">
   📱 Commander sur WhatsApp
</a>














        <?php if (!empty($_SESSION['id'])): ?>
            <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" class="btn btn-deconnexion">
                Modifier ce produit
            </a>
        <?php endif; ?>
    </div>

</body>
</html>