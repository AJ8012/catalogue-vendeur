<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/database.php');
require(__DIR__ . '/vendor/autoload.php');

use Cloudinary\Cloudinary;

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

if (empty($_POST['nom']) || empty($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    $_SESSION['erreur'] = "Le nom et au moins une photo sont obligatoires.";
    header('Location: ajouter_produit.php');
    exit();
}

// Configuration Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'yme18tjv',
        'api_key'    => '193269622434582',
        'api_secret' => 'FQGu7ePvtNecUV187T5Qt8uuQyU',
    ],
]);

$nom = htmlspecialchars($_POST['nom']);
$description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : "";
$prix = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;
$id_vendeur = $_SESSION['id'];

// Insérer le produit
$insert = $bdd->prepare('INSERT INTO produits(nom, description, prix, id_utilisateur) VALUES(?, ?, ?, ?)');
$insert->execute([$nom, $description, $prix, $id_vendeur]);
$produit_id = $bdd->lastInsertId();

// Upload des images vers Cloudinary
$images = $_FILES['images'];
$success = true;

for ($i = 0; $i < count($images['name']); $i++) {
    if ($images['error'][$i] !== 0) continue;

    try {
        $upload = $cloudinary->uploadApi()->upload(
            $images['tmp_name'][$i],
            ['folder' => 'catalogue']
        );
        $image_url = $upload['secure_url'];

        $insert_img = $bdd->prepare('INSERT INTO produit_images(produit_id, image) VALUES(?, ?)');
        $insert_img->execute([$produit_id, $image_url]);
    } catch (Exception $e) {
        $success = false;
        $_SESSION['erreur'] = "Erreur lors de l'upload d'une image : " . $e->getMessage();
    }
}

if ($success) {
    header('Location: index.php');
} else {
    header('Location: ajouter_produit.php');
}
exit();