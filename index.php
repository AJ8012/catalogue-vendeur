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
    <title>Mon Catalogue - Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="header">
        <h1>Catalogue Vendeur</h1>
        
        <?php if (!empty($_SESSION['id'])): ?>
            <p class="bienvenue">👋 Mode Admin : Bonjour <?php echo htmlspecialchars($_SESSION['nom']); ?></p>
            <a href="ajouter_produit.php" class="btn btn-ajout">➕ Ajouter un produit</a>
            <a href="actions/action_logout.php" class="btn btn-deconnexion">Se déconnecter</a>
        <?php else: ?>
            <p class="bienvenue">✨ Bienvenue sur notre catalogue en ligne !</p>
            <a href="login.php" class="btn btn-connexion">Connexion Vendeur</a>
        <?php endif; ?>
    </div>

    <div class="grille-produits">
        <?php
        $compteur = 0;
        while ($produit = $recup_produits->fetch()) {
            $compteur++;
            $message_whatsapp = "Bonjour, je suis intéressé par le produit : " . urlencode($produit['nom']);
            ?>
            <!-- Carte produit : clic = va sur la page détail du produit -->
            <div class="carte-produit">

                <a href="produit.php?id=<?php echo $produit['id']; ?>" class="lien-carte">
                    <img src="uploads/<?php echo htmlspecialchars($produit['image'] ?? 'placeholder.png'); ?>" 
                         alt="<?php echo htmlspecialchars($produit['nom']); ?>">

                    <h3><?php echo htmlspecialchars($produit['nom']); ?></h3>

                    <?php if (!empty($produit['description'])): ?>
                        <p class="description"><?php echo nl2br(htmlspecialchars($produit['description'])); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($produit['prix']) && $produit['prix'] > 0): ?>
                        <p class="prix"><?php echo number_format($produit['prix'], 2); ?> UM</p>
                    <?php endif; ?>
                </a>

                <!-- Bouton WhatsApp direct -->
             
<a href="https://wa.me/<?php echo $produit['vendeur_telephone']; ?>?text=<?php echo $message_whatsapp; ?>" 
   class="btn-whatsapp" target="_blank">
   Commander sur WhatsApp
</a>














                <?php if (!empty($_SESSION['id'])): ?>
                    <a href="modifier_produit.php?id=<?php echo $produit['id']; ?>" 
                       class="btn-whatsapp">
                       Modifier
                    </a>
                <?php endif; ?>
            </div>
            <?php
        }

        if ($compteur == 0) {
            echo '<p class="aucun-produit">Aucun produit en ligne pour le moment.</p>';
        }
        ?>
    </div>


</body>
</html>