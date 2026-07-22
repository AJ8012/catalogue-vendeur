<?php
session_start();
require(__DIR__ . '/../database.php');
require(__DIR__ . '/../vendor/autoload.php');

use Cloudinary\Cloudinary;

// Vérifier que l'utilisateur est connecté
if (empty($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}

// Vérifier qu'un ID produit est passé
if (empty($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$id_produit = intval($_GET['id']);
$id_vendeur = $_SESSION['id'];

// Vérifier que le produit appartient bien à l'utilisateur
$check = $bdd->prepare('SELECT id FROM produits WHERE id = ? AND id_utilisateur = ?');
$check->execute([$id_produit, $id_vendeur]);
if ($check->rowCount() == 0) {
    $_SESSION['erreur'] = "Vous n'avez pas le droit de supprimer ce produit.";
    header('Location: ../index.php');
    exit();
}

// 1. Récupérer les images du produit
$req_img = $bdd->prepare('SELECT image FROM produit_images WHERE produit_id = ?');
$req_img->execute([$id_produit]);
$images = $req_img->fetchAll();

// 2. Supprimer les images de Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: 'yme18tjv',
        'api_key'    => getenv('CLOUDINARY_API_KEY') ?: '193269622434582',
        'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: 'FQGu7ePvtNecUV187T5Qt8uuQyU',
    ],
]);

foreach ($images as $img) {
    // Extraire le public_id de l'URL Cloudinary
    $url = $img['image'];
    // Ex: https://res.cloudinary.com/yme18tjv/image/upload/v1234567890/catalogue/nom_image.jpg
    $parts = explode('/', $url);
    $filename_with_ext = end($parts);
    // On enlève l'extension pour avoir le public_id
    $public_id = pathinfo($filename_with_ext, PATHINFO_FILENAME);
    // Si l'image est dans un dossier "catalogue/", il faut reconstituer le public_id complet
    // On vérifie si le chemin contient "catalogue/"
    if (strpos($url, '/catalogue/') !== false) {
        $public_id = 'catalogue/' . $public_id;
    }
    try {
        $cloudinary->uploadApi()->destroy($public_id);
    } catch (Exception $e) {
        // Ignorer les erreurs si l'image n'existe plus sur Cloudinary
    }
}

// 3. Supprimer les lignes de la table produit_images
$delete_img = $bdd->prepare('DELETE FROM produit_images WHERE produit_id = ?');
$delete_img->execute([$id_produit]);

// 4. Supprimer le produit
$delete_prod = $bdd->prepare('DELETE FROM produits WHERE id = ?');
$delete_prod->execute([$id_produit]);

// 5. Redirection avec message de succès
$_SESSION['success'] = "Produit supprimé avec succès !";
header('Location: ../index.php');
exit();