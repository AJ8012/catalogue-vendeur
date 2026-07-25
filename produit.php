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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produit['nom']); ?> - Catalogue</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3FXBWCRQQR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-3FXBWCRQQR');
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ======== HEADER MINIMAL ======== -->
    <header class="header-minimal">
        <div class="header-inner">
            <div class="logo">
                <a href="index.php">Catalogue Vendeur</a>
            </div>
            <nav class="nav-links">
                <?php if (!empty($_SESSION['id'])): ?>
                    <span class="bienvenue-minimal">👋 <?php echo htmlspecialchars($_SESSION['nom']); ?></span>
                    <a href="ajouter_produit.php" class="nav-btn">Ajouter</a>
                    <a href="actions/action_logout.php" class="nav-btn">Se déconnecter</a>
                <?php else: ?>
                    <a href="login.php" class="nav-btn">Connexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- ======== PAGE PRODUIT ======== -->
    <main class="page-produit-portier">
        <div class="produit-layout">

            <!-- COLONNE GAUCHE : IMAGE(S) -->
            <div class="produit-images">
                <?php if (!empty($images)): ?>
                    <!-- Image principale (la première) -->
                    <div class="image-principale">
                        <img id="main-image" src="<?php echo htmlspecialchars($images[0]['image'] . '?f_auto=1'); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                    </div>
                    <!-- Miniatures (si plusieurs images) -->
                    <?php if (count($images) > 1): ?>
                        <div class="miniatures">
                            <?php foreach ($images as $img): ?>
                                <img src="<?php echo htmlspecialchars($img['image'] . '?f_auto=1&w=100&h=100&crop=fill'); ?>" alt="" class="miniature" onclick="document.getElementById('main-image').src = this.src.replace('?f_auto=1&w=100&h=100&crop=fill', '?f_auto=1')">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="image-principale">
                        <img src="uploads/placeholder.png?f_auto=1" alt="Image non disponible">
                    </div>
                <?php endif; ?>
            </div>

            <!-- COLONNE DROITE : INFOS -->
            <div class="produit-infos">
                <h1 class="produit-marque"><?php echo htmlspecialchars($produit['nom']); ?></h1>
                <?php if (!empty($produit['description'])): ?>
                    <p class="produit-description"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
                <?php endif; ?>
                <div class="produit-prix">
                    <?php if (!empty($produit['prix']) && $produit['prix'] > 0): ?>
                        <?php echo number_format($produit['prix'], 0, ',', ' '); ?> UM
                    <?php else: ?>
                        Prix sur demande
                    <?php endif; ?>
                </div>

                <!-- BOUTON WHATSAPP (UNIQUEMENT ICI) -->
                <a href="https://wa.me/<?php echo $produit['vendeur_telephone'] ?? '12345678'; ?>?text=<?php echo $message_whatsapp; ?>" target="_blank" class="btn-whatsapp-produit">
                    📱 Commander sur WhatsApp
                </a>

                <!-- LIEN MODIFIER (si connecté) -->
                <?php if (!empty($_SESSION['id'])): ?>
                    <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" class="btn-modifier-produit">Modifier ce produit</a>
                <?php endif; ?>

                <!-- RETOUR -->
                <a href="index.php" class="btn-retour-produit">← Retour à l'accueil</a>
            </div>
        </div>
    </main>

</body>
</html>