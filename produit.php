<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require(__DIR__ . '/database.php');

if (empty($_GET['id'])) { header('Location: index.php'); exit(); }
$id_produit = intval($_GET['id']);

$req = $bdd->prepare('SELECT p.*, u.telephone AS vendeur_telephone FROM produits p JOIN utilisateurs u ON p.id_utilisateur = u.id WHERE p.id = ?');
$req->execute([$id_produit]);
if ($req->rowCount() == 0) { header('Location: index.php'); exit(); }
$produit = $req->fetch();

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
            <a href="index.php" class="nav-btn">Accueil</a>
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

<main class="page-produit-porter">
    <div class="image-section">
        <!-- Carrousel -->
        <div class="carousel-container">
            <?php if (!empty($images)): ?>
                <div class="carousel-slides">
                    <?php foreach ($images as $idx => $img): ?>
                        <div class="carousel-slide <?php echo ($idx === 0) ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($img['image'] . '?f_auto=1'); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-btn prev-btn" onclick="changeSlide(-1)">&#10094;</button>
                <button class="carousel-btn next-btn" onclick="changeSlide(1)">&#10095;</button>
                <div class="carousel-indicators">
                    <?php foreach ($images as $idx => $img): ?>
                        <span class="dot <?php echo ($idx === 0) ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $idx; ?>)"></span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="carousel-slides">
                    <div class="carousel-slide active">
                        <img src="uploads/placeholder.png?f_auto=1" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="infos-section">
        <p class="marque"><?php echo htmlspecialchars($produit['nom']); ?></p>
        <p class="nom-produit"><?php echo htmlspecialchars($produit['nom']); ?></p>
        <?php if (!empty($produit['description'])): ?>
            <p class="description"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
        <?php endif; ?>
        <p class="prix"><?php echo (!empty($produit['prix']) && $produit['prix'] > 0) ? number_format($produit['prix'], 0, ',', ' ') . ' UM' : 'Prix sur demande'; ?></p>
        <div class="actions">
            <a href="https://wa.me/<?php echo $produit['vendeur_telephone'] ?? '+222'; ?>?text=<?php echo $message_whatsapp; ?>" target="_blank" class="btn-whatsapp-porter">📱 Commander sur WhatsApp</a>
            <?php if (!empty($_SESSION['id'])): ?>
                <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" class="btn-modifier-porter">Modifier</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.dot');

    function goToSlide(index) {
        if (slides.length === 0) return;
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        currentSlide = index;
    }

    function changeSlide(direction) {
        goToSlide(currentSlide + direction);
    }

    // Initialisation
    goToSlide(0);
</script>

</body>
</html>