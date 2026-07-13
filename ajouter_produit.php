<?php
session_start();

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="formulaire">
        <h2>Ajouter un produit</h2>

        <?php if(!empty($_SESSION['erreur'])): ?>
            <p class="message-erreur"><?php echo $_SESSION['erreur']; ?></p>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form action="actions/ajouter_produit.php" method="POST" enctype="multipart/form-data">

            <div class="champ">
                <label>Nom du produit (obligatoire) :</label>
                <input type="text" name="nom" placeholder="Ex: T-shirt blanc" required>
            </div>

            <div class="champ">
                <label>Description (facultatif) :</label>
                <textarea name="description" placeholder="Décrivez votre produit..."></textarea>
            </div>

            <div class="champ">
                <label>Prix (€ - facultatif) :</label>
                <input type="number" name="prix" step="0.01" placeholder="Ex: 25.00">
            </div>

            <div class="champ">
                <label>Photos du produit (plusieurs possibles) :</label>
                <input type="file" name="images[]" id="images" accept="image/*" multiple>
                <div id="preview-images" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;"></div>
            </div>

            <button type="submit">Mettre en ligne</button>

        </form>

        <div class="lien-bas">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>

    <script>
        const input = document.getElementById('images');
        const preview = document.getElementById('preview-images');

        input.addEventListener('change', function(e) {
            preview.innerHTML = '';
            const files = Array.from(this.files);
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.style.position = 'relative';
                    div.style.display = 'inline-block';
                    div.style.width = '100px';
                    div.style.height = '100px';
                    div.style.overflow = 'hidden';
                    div.style.border = '1px solid #ddd';
                    div.style.borderRadius = '4px';

                    const img = document.createElement('img');
                    img.src = ev.target.result;
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
                        // Retirer le fichier de l'input
                        const dt = new DataTransfer();
                        const currentFiles = Array.from(input.files);
                        const newFiles = currentFiles.filter((f, i) => i !== index);
                        newFiles.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                        // Supprimer l'élément du DOM
                        div.remove();
                        // Re-indexer les boutons (pour garder la cohérence)
                        const allPreviews = preview.querySelectorAll('div');
                        allPreviews.forEach((div, idx) => {
                            const btn = div.querySelector('button');
                            // On ne change pas l'index, on recrée l'événement ou on rafraîchit
                        });
                    });

                    div.appendChild(img);
                    div.appendChild(btn);
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

</body>
</html>