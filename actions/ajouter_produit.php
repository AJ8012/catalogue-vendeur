<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../database.php');
require(__DIR__ . '/../vendor/autoload.php');

use Cloudinary\Cloudinary;

if (empty($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}

// Le nom du produit est obligatoire
if (empty($_POST['nom'])) {
    $_SESSION['erreur'] = "Le nom du produit est obligatoire.";
    header('Location: ../ajouter_produit.php');
    exit();
}

$nom          = htmlspecialchars($_POST['nom']);
$description  = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : "";
$prix         = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;
$id_utilisateur = $_SESSION['id'];

// 1. Insertion du produit
$insert = $bdd->prepare('INSERT INTO produits(nom, description, prix, id_utilisateur) VALUES(?, ?, ?, ?)');
$insert->execute([$nom, $description, $prix, $id_utilisateur]);
$id_produit = $bdd->lastInsertId();

// ----- Configuration Cloudinary -----
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: 'yme18tjv',
        'api_key'    => getenv('CLOUDINARY_API_KEY') ?: '193269622434582',
        'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: 'FQGu7ePvtNecUV187T5Qt8uuQyU',
    ],
]);

// 2. Upload des images (avec conversion automatique HEIC -> JPEG/WebP côté Cloudinary)
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $files = $_FILES['images'];
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== 0) continue;

        try {
            $upload = $cloudinary->uploadApi()->upload(
                $files['tmp_name'][$i],
                [
                    'folder'       => 'catalogue',
                    'fetch_format' => 'auto',
                    'quality'      => 'auto',
                ]
            );
            $image_url = $upload['secure_url'];

            $insertImg = $bdd->prepare('INSERT INTO produit_images(produit_id, image) VALUES(?, ?)');
            $insertImg->execute([$id_produit, $image_url]);
        } catch (Exception $e) {
            $_SESSION['erreur'] = "Erreur lors de l'upload : " . $e->getMessage();
        }
    }
}

header('Location: ../index.php');
exit();