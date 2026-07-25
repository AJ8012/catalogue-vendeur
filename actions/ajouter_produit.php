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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3FXBWCRQQR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-3FXBWCRQQR');
    </script>
    <link rel="stylesheet" href="style.css">
    <!-- Conversion HEIC/HEIF -> JPEG pour l'aperçu (les iPhone envoient des photos en HEIC,
         un format que les navigateurs (hors Safari) ne savent pas afficher directement) -->
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('images');
            const preview = document.getElementById('preview-images');

            if (!input || !preview) return;

            // Message par défaut si aucun fichier
            if (input.files.length === 0) {
                preview.innerHTML = '<p style="color:#6b5f49; font-size:13px;">Aucune image sélectionnée</p>';
            }

            input.addEventListener('change', function(e) {
                preview.innerHTML = '';
                const files = Array.from(this.files);
                if (files.length === 0) {
                    preview.innerHTML = '<p style="color:#6b5f49; font-size:13px;">Aucune image sélectionnée</p>';
                    return;
                }

                // Un fichier est-il au format HEIC/HEIF (photos iPhone) ?
                function isHeic(file) {
                    const name = (file.name || '').toLowerCase();
                    return file.type === 'image/heic' || file.type === 'image/heif' ||
                           name.endsWith('.heic') || name.endsWith('.heif');
                }

                files.forEach((file, index) => {
                    const div = document.createElement('div');
                    div.style.position = 'relative';
                    div.style.display = 'inline-block';
                    div.style.width = '100px';
                    div.style.height = '100px';
                    div.style.margin = '4px';
                    div.style.overflow = 'hidden';
                    div.style.border = '2px solid #d9c69c';
                    div.style.borderRadius = '8px';
                    div.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                    div.style.background = '#f0ece0';

                    const img = document.createElement('img');
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    div.appendChild(img);

                    // Petit indicateur "conversion en cours" pendant la génération de l'aperçu HEIC
                    const loading = document.createElement('div');
                    loading.textContent = 'HEIC…';
                    loading.style.position = 'absolute';
                    loading.style.inset = '0';
                    loading.style.display = 'flex';
                    loading.style.alignItems = 'center';
                    loading.style.justifyContent = 'center';
                    loading.style.fontSize = '12px';
                    loading.style.color = '#6b5f49';

                    // Bouton de suppression (centré via flexbox, pas via line-height)
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = '×';
                    btn.style.position = 'absolute';
                    btn.style.top = '4px';
                    btn.style.right = '4px';
                    btn.style.width = '22px';
                    btn.style.height = '22px';
                    btn.style.padding = '0';
                    btn.style.margin = '0';
                    btn.style.boxSizing = 'border-box';
                    btn.style.display = 'flex';
                    btn.style.alignItems = 'center';
                    btn.style.justifyContent = 'center';
                    btn.style.background = 'rgba(0,0,0,0.7)';
                    btn.style.color = '#fff';
                    btn.style.border = 'none';
                    btn.style.borderRadius = '50%';
                    btn.style.cursor = 'pointer';
                    btn.style.fontSize = '16px';
                    btn.style.lineHeight = '1';
                    btn.style.transition = 'background 0.2s';
                    btn.onmouseover = () => btn.style.background = 'rgba(200,0,0,0.8)';
                    btn.onmouseout = () => btn.style.background = 'rgba(0,0,0,0.7)';
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Retirer le fichier du DataTransfer
                        const dt = new DataTransfer();
                        const currentFiles = Array.from(input.files);
                        const newFiles = currentFiles.filter((f, i) => i !== index);
                        newFiles.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                        // Supprimer l'élément du DOM
                        div.remove();
                        // Si plus de fichiers, afficher message
                        if (input.files.length === 0) {
                            preview.innerHTML = '<p style="color:#6b5f49; font-size:13px;">Aucune image sélectionnée</p>';
                        }
                    });
                    div.appendChild(btn);
                    preview.appendChild(div);

                    if (isHeic(file)) {
                        div.appendChild(loading);
                        heic2any({ blob: file, toType: 'image/jpeg', quality: 0.7 })
                            .then(function(convertedBlob) {
                                img.src = URL.createObjectURL(convertedBlob);
                                loading.remove();
                            })
                            .catch(function() {
                                // Si la conversion échoue, on garde un aperçu simplifié
                                loading.textContent = '📷 HEIC';
                            });
                    } else {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            img.src = ev.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });
    </script>
</body>
</html>