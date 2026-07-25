<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/database.php');

$recup_produits = $bdd->prepare('
    SELECT p.*,
           (SELECT pi.image FROM produit_images pi WHERE pi.produit_id = p.id ORDER BY pi.id ASC LIMIT 1) AS image,
           u.telephone AS vendeur_telephone
    FROM produits p
    JOIN utilisateurs u ON p.id_utilisateur = u.id
    ORDER BY p.id DESC
');
$recup_produits->execute();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Catalogue - Accueil</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3FXBWCRQQR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-3FXBWCRQQR');
    </script>

    <!-- Google Fonts (élégantes) -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ======== HEADER MINIMALISTE ======== -->
    <header class="header-minimal">
        <div class="header-inner">
            <div class="logo">
                <a href="index.php">Catalogue Vendeur</a>
            </div>
            <nav class="nav-links">
                <?php if (!empty($_SESSION['id'])): ?>
                    <span class="bienvenue-minimal"><?php echo htmlspecialchars($_SESSION['nom']); ?></span>
                    <a href="ajouter_produit.php" class="nav-btn">Ajouter</a>
                    <a href="actions/action_logout.php" class="nav-btn">Se déconnecter</a>
                <?php else: ?>
                    <a href="login.php" class="nav-btn">Connexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- ======== GRILLE PRODUITS ======== -->
    <main class="grille-produits-portier">
        <?php
        $compteur = 0;
        while ($produit = $recup_produits->fetch()) {
            $compteur++;
            // On récupère l'image (si elle existe) ou placeholder
            $image = htmlspecialchars($produit['image'] ?? 'placeholder.png');
            // On ajoute ?f_auto=1 pour que Cloudinary optimise
            $image_url = $image . '?f_auto=1';
            ?>
            <div class="carte-produit-portier">
                <a href="produit.php?id=<?php echo $produit['id']; ?>" class="lien-carte-portier">
                    <div class="carte-image-portier">
                        <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                    </div>
                    <div class="carte-infos-portier">
                        <p class="carte-marque"><?php echo htmlspecialchars($produit['nom']); ?></p>
                        <?php if (!empty($produit['description'])): ?>
                            <p class="carte-nom"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($produit['prix']) && $produit['prix'] > 0): ?>
                            <p class="carte-prix"><?php echo number_format($produit['prix'], 0, ',', ' '); ?> UM</p>
                        <?php else: ?>
                            <p class="carte-prix">Prix sur demande</p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php
        }

        if ($compteur == 0) {
            echo '<p class="aucun-produit-portier">Aucun produit en ligne pour le moment.</p>';
        }
        ?>
    </main>

</body>
</html>