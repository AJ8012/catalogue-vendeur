<?php
session_start();
require(__DIR__ . '/../database.php');
require(__DIR__ . '/../vendor/autoload.php');

use Cloudinary\Cloudinary;

if (empty($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}

if (empty($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$id_produit = intval($_GET['id']);
$id_vendeur = $_SESSION['id'];

$check = $bdd->prepare('SELECT id FROM produits WHERE id = ? AND id_utilisateur = ?');
$check->execute([$id_produit, $id_vendeur]);
if ($check->rowCount() == 0) {
    $_SESSION['erreur'] = "Vous n'avez pas le droit de supprimer ce produit.";
    header('Location: ../index.php');
    exit();
}

// Récupérer les images
$req_img = $bdd->prepare('SELECT image FROM produit_images WHERE produit_id = ?');
$req_img->execute([$id_produit]);
$images = $req_img->fetchAll();

// Supprimer les images de Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: 'yme18tjv',
        'api_key'    => getenv('CLOUDINARY_API_KEY') ?: '193269622434582',
        'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: 'FQGu7ePvtNecUV187T5Qt8uuQyU',
    ],
]);

foreach ($images as $img) {
    $url = $img['image'];
    // Extraire le public_id
    $parsed = parse_url($url);
    $path = $parsed['path']; // ex: /v123456/catalogue/nom.jpg
    $path_parts = explode('/', $path);
    $filename = end($path_parts);
    $public_id = pathinfo($filename, PATHINFO_FILENAME);
    // Si le dossier est "catalogue", on le reconstruit
    if (strpos($path, '/catalogue/') !== false) {
        $public_id = 'catalogue/' . $public_id;
    }
    try {
        $cloudinary->uploadApi()->destroy($public_id);
    } catch (Exception $e) {
        // Ignorer
    }
}

// Supprimer les enregistrements
$delete_img = $bdd->prepare('DELETE FROM produit_images WHERE produit_id = ?');
$delete_img->execute([$id_produit]);

$delete_prod = $bdd->prepare('DELETE FROM produits WHERE id = ?');
$delete_prod->execute([$id_produit]);

$_SESSION['success'] = "Produit supprimé avec succès !";
header('Location: ../index.php');
exit();