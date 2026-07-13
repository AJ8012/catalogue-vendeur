<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../database.php');

if (empty($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}

if (empty($_POST['nom']) || empty($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    $_SESSION['erreur'] = "Le nom et au moins une photo sont obligatoires.";
    header('Location: ../ajouter_produit.php');
    exit();
}

$nom = htmlspecialchars($_POST['nom']);
$description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : "";
$prix = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;
$id_vendeur = $_SESSION['id'];

// Insérer le produit
$insert = $bdd->prepare('INSERT INTO produits(nom, description, prix, id_utilisateur) VALUES(?, ?, ?, ?)');
$insert->execute([$nom, $description, $prix, $id_vendeur]);
$produit_id = $bdd->lastInsertId();

// Traiter chaque image
$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$images = $_FILES['images'];
$success = true;
for ($i = 0; $i < count($images['name']); $i++) {
    if ($images['error'][$i] !== 0) continue;
    $ext = pathinfo($images['name'][$i], PATHINFO_EXTENSION);
    $nom_fichier = time() . '_' . uniqid() . '.' . $ext;
    $destination = $upload_dir . $nom_fichier;
    if (move_uploaded_file($images['tmp_name'][$i], $destination)) {
        $insert_img = $bdd->prepare('INSERT INTO produit_images(produit_id, image) VALUES(?, ?)');
        $insert_img->execute([$produit_id, $nom_fichier]);
    } else {
        $success = false;
        $_SESSION['erreur'] = "Erreur lors de l'upload de certaines images.";
    }
}

if ($success) {
    header('Location: ../index.php');
} else {
    header('Location: ../ajouter_produit.php');
}
exit();