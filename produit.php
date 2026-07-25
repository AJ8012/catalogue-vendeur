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
$total_images = count($images);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
                <span class="bienvenue-minimal"> <?php echo htmlspecialchars($_SESSION['nom']); ?></span>
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
        <div class="carousel-container" id="carousel">
            <div class="carousel-track" id="carouselTrack">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $img): ?>
                        <div class="carousel-slide">
                            <img src="<?php echo htmlspecialchars($img['image'] . '?f_auto=1'); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>" draggable="false">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-slide">
                        <img src="uploads/placeholder.png?f_auto=1" alt="<?php echo htmlspecialchars($produit['nom']); ?>" draggable="false">
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($total_images > 1): ?>
                <!-- Flèches -->
                <button class="carousel-btn carousel-btn-left" id="prevBtn">&#10094;</button>
                <button class="carousel-btn carousel-btn-right" id="nextBtn">&#10095;</button>
                <!-- Compteur -->
                <div class="carousel-counter" id="counter">1 / <?php echo $total_images; ?></div>
                <!-- Indicateur de progression (bande noire) -->
                <div class="carousel-progress">
                    <div class="carousel-progress-bar" id="progressBar"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Infos produit -->
    <div class="produit-infos-section">
        <p class="produit-marque"><?php echo htmlspecialchars($produit['nom']); ?></p>
        <?php if (!empty($produit['description'])): ?>
            <p class="produit-description"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
        <?php endif; ?>
        <p class="produit-prix"><?php echo (!empty($produit['prix']) && $produit['prix'] > 0) ? number_format($produit['prix'], 0, ',', ' ') . ' UM' : 'Prix sur demande'; ?></p>
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
        const slides = track.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;
        if (totalSlides <= 1) return;

        let currentIndex = 0;
        let startX = 0;
        let isDragging = false;

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const counter = document.getElementById('counter');
        const progressBar = document.getElementById('progressBar');

        function updateCarousel(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;
            track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
            counter.textContent = (currentIndex + 1) + ' / ' + totalSlides;
            progressBar.style.width = ((currentIndex + 1) / totalSlides * 100) + '%';
        }

        // Événements tactiles (swipe)
        track.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            isDragging = true;
        }, { passive: true });

        track.addEventListener('touchend', function(e) {
            if (!isDragging) return;
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    updateCarousel(currentIndex + 1);
                } else {
                    updateCarousel(currentIndex - 1);
                }
            }
            isDragging = false;
        }, { passive: true });

        // Flèches
        prevBtn.addEventListener('click', function() {
            updateCarousel(currentIndex - 1);
        });
        nextBtn.addEventListener('click', function() {
            updateCarousel(currentIndex + 1);
        });

        // Clavier (flèches gauche/droite)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') updateCarousel(currentIndex - 1);
            if (e.key === 'ArrowRight') updateCarousel(currentIndex + 1);
        });

        // Initialisation
        updateCarousel(0);
    });
</script>

</body>
</html>