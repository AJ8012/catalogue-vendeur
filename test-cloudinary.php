<?php
require __DIR__ . '/vendor/autoload.php';

use Cloudinary\Cloudinary;

// Configuration avec tes identifiants réels
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'yme18tjv',
        'api_key'    => '193269622434582',
        'api_secret' => 'FQGu7ePvtNecUV187T5Qt8uuQyU',
    ],
]);

// 1. Uploader une image depuis l'URL de démo Cloudinary
$image_url = 'https://res.cloudinary.com/demo/image/upload/sample.jpg';
echo "📤 Upload de l'image depuis : $image_url\n";

$upload_result = $cloudinary->uploadApi()->upload($image_url, [
    'public_id' => 'test_upload'
]);

$public_id = $upload_result['public_id'];
$secure_url = $upload_result['secure_url'];
echo "✅ Image uploadée !\n";
echo "   URL sécurisée : $secure_url\n";
echo "   Public ID     : $public_id\n\n";

// 2. Récupérer les métadonnées
$metadata = $cloudinary->adminApi()->asset($public_id);
$width = $metadata['width'];
$height = $metadata['height'];
$format = $metadata['format'];
$bytes = $metadata['bytes'];

echo "📊 Métadonnées de l'image :\n";
echo "   Dimensions : {$width}x{$height} px\n";
echo "   Format     : $format\n";
echo "   Taille     : " . number_format($bytes) . " octets\n\n";

// 3. Générer une version transformée avec f_auto et q_auto
$transformed_url = $cloudinary->image($public_id)->delivery([
    'f_auto' => null,  // Sélectionne automatiquement le meilleur format (WebP, AVIF, etc.)
    'q_auto' => null,  // Ajuste automatiquement la qualité pour un bon compromis taille/qualité
])->toUrl();

echo "🔁 Version optimisée générée :\n";
echo "   $transformed_url\n\n";
echo "✅ Fait ! Clique sur le lien ci-dessus pour voir la version optimisée.\n";
echo "   Compare la taille et le format avec l'original.\n";