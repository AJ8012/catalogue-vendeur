<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Catalogue - Accueil</title>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3FXBWCRQQR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-3FXBWCRQQR');
    </script>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>





<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require(__DIR__ . '/database.php');

if (empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}
$id_produit = intval($_GET['id']);

// Vérifier que le produit existe et appartient à l'utilisateur
$req = $bdd->prepare('SELECT * FROM produits WHERE id = ? AND id_utilisateur = ?');
$req->execute([$id_produit, $_SESSION['id']]);
if ($req->rowCount() == 0) {
    header('Location: index.php');
    exit();
}
$produit = $req->fetch();

// Récupérer les images
$req_img = $bdd->prepare('SELECT id, image FROM produit_images WHERE produit_id = ? ORDER BY id');
$req_img->execute([$id_produit]);
$images = $req_img->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="formulaire">
        <h2>Modifier le produit</h2>

        <?php if(!empty($_SESSION['erreur'])): ?>
            <p class="message-erreur"><?php echo $_SESSION['erreur']; ?></p>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form action="actions/action_modifier_produit.php?id=<?php echo $id_produit; ?>" method="POST" enctype="multipart/form-data">

            <div class="champ">
                <label>Nom du produit (obligatoire) :</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
            </div>

            <div class="champ">
                <label>Description (facultatif) :</label>
                <textarea name="description"><?php echo htmlspecialchars($produit['description']); ?></textarea>
            </div>

            <div class="champ">
                <label>Prix (UM- facultatif) :</label>
                <input type="number" name="prix" step="0.01" value="<?php echo $produit['prix']; ?>">
            </div>

            <div class="champ">
                <label>Images actuelles :</label>
                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                    <?php foreach ($images as $img): ?>
                        <div style="position:relative; width:100px; height:100px; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
                            <img src="<?php echo htmlspecialchars($img['image']); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <a href="actions/action_supprimer_image.php?id=<?php echo $img['id']; ?>&produit=<?php echo $id_produit; ?>" 
                               style="position:absolute; top:2px; right:2px; background:rgba(0,0,0,0.6); color:#fff; border:none; border-radius:50%; width:24px; height:24px; text-align:center; line-height:24px; text-decoration:none; font-size:16px;"
                               onclick="return confirm('Supprimer cette image ?')">×</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="champ">
                <label>Ajouter de nouvelles photos :</label>
                <input type="file" name="new_images[]" accept="image/*" multiple>
                <div id="preview-new" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;"></div>
            </div>

            <button type="submit">Enregistrer les modifications</button>

        </form>

        <div class="lien-bas">
            <a href="index.php">Annuler et retourner à l'accueil</a>
        </div>
    </div>

    <script>
        const inputNew = document.querySelector('input[name="new_images[]"]');
        const previewNew = document.getElementById('preview-new');

        inputNew.addEventListener('change', function() {
            previewNew.innerHTML = '';
            const files = Array.from(this.files);
            files.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.style.position = 'relative';
                    div.style.width = '100px';
                    div.style.height = '100px';
                    div.style.overflow = 'hidden';
                    div.style.border = '1px solid #ddd';
                    div.style.borderRadius = '4px';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    const btn = document.createElement('button');
                    btn.textContent = '×';
                    btn.style.position = 'absolute';
                    btn.style.top = '2px';
                    btn.style.right = '2px';
                    btn.style.background = 'rgba(0,0,0,0.6)';
                    btn.style.color = '#fff';
                    btn.style.border = 'none';
                    btn.style.borderRadius = '50%';
                    btn.style.width = '24px';
                    btn.style.height = '24px';
                    btn.style.cursor = 'pointer';
                    btn.style.fontSize = '16px';
                    btn.style.lineHeight = '24px';
                    btn.style.textAlign = 'center';
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Retirer du DataTransfer
                        const dt = new DataTransfer();
                        const current = Array.from(inputNew.files);
                        const filtered = current.filter((f, i) => i !== idx);
                        filtered.forEach(f => dt.items.add(f));
                        inputNew.files = dt.files;
                        div.remove();
                    });
                    div.appendChild(img);
                    div.appendChild(btn);
                    previewNew.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

</body>
</html>