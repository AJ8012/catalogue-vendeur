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
    <!-- Section image avec carrousel -->
    <div class="produit-image-section">
        <?php if (!empty($images)): ?>
            <div class="carousel-container">
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($images as $img): ?>
                        <div class="carousel-slide">
                            <img src="<?php echo htmlspecialchars($img['image'] . '?f_auto=1'); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <button class="carousel-btn carousel-btn-left" id="prevBtn">‹</button>
                    <button class="carousel-btn carousel-btn-right" id="nextBtn">›</button>
                    <div class="carousel-indicator" id="carouselIndicator">1 / <?php echo count($images); ?></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="carousel-container">
                <div class="carousel-track">
                    <div class="carousel-slide">
                        <img src="uploads/placeholder.png?f_auto=1" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section infos produit -->
    <div class="produit-infos-section">
        <p class="produit-marque"><?php echo htmlspecialchars($produit['nom']); ?></p>
        <p class="produit-nom"><?php echo htmlspecialchars($produit['nom']); ?></p>
        <?php if (!empty($produit['description'])): ?>
            <p class="produit-description"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
        <?php endif; ?>
        <?php if (!empty($produit['prix']) && $produit['prix'] > 0): ?>
            <p class="produit-prix"><?php echo number_format($produit['prix'], 0, ',', ' '); ?> UM</p>
        <?php else: ?>
            <p class="produit-prix">Prix sur demande</p>
        <?php endif; ?>

        <div class="produit-actions">
            <a href="https://wa.me/<?php echo $produit['vendeur_telephone'] ?? '+222'; ?>?text=<?php echo $message_whatsapp; ?>" target="_blank" class="btn-whatsapp-porter">📱 Commander sur WhatsApp</a>
            <?php if (!empty($_SESSION['id'])): ?>
                <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" class="btn-modifier-porter">Modifier</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('carouselTrack');
        if (!track) return;
        const slides = track.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;
        if (totalSlides <= 1) return;

        let currentIndex = 0;
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const indicator = document.getElementById('carouselIndicator');

        function updateCarousel() {
            const offset = -currentIndex * 100;
            track.style.transform = 'translateX(' + offset + '%)';
            if (indicator) {
                indicator.textContent = (currentIndex + 1) + ' / ' + totalSlides;
            }
        }

        prevBtn.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateCarousel();
        });

        nextBtn.addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateCarousel();
        });

        // Clavier : flèches gauche/droite
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevBtn.click();
            } else if (e.key === 'ArrowRight') {
                nextBtn.click();
            }
        });

        // Option : swipe sur mobile (simple)
        let startX = 0;
        track.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        });
        track.addEventListener('touchend', function(e) {
            if (!startX) return;
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            if (Math.abs(diff) > 30) {
                if (diff > 0) {
                    nextBtn.click();
                } else {
                    prevBtn.click();
                }
            }
            startX = 0;
        });
    });
</script>

</body>
</html>