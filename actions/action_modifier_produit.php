<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../database.php');

if (empty($_SESSION['id']) || empty($_GET['id'])) {
    header('Location: ../login.php');
    exit();
}
$id_produit = intval($_GET['id']);

// Vérifier que le produit appartient à l'utilisateur
$check = $bdd->prepare('SELECT id FROM produits WHERE id = ? AND id_utilisateur = ?');
$check->execute([$id_produit, $_SESSION['id']]);
if ($check->rowCount() == 0) {
    header('Location: ../index.php');
    exit();
}

// Mise à jour des champs texte
if (empty($_POST['nom'])) {
    $_SESSION['erreur'] = "Le nom est obligatoire.";
    header('Location: ../modifier_produit.php?id=' . $id_produit);
    exit();
}
$nom = htmlspecialchars($_POST['nom']);
$description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : "";
$prix = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;

$update = $bdd->prepare('UPDATE produits SET nom = ?, description = ?, prix = ? WHERE id = ?');
$update->execute([$nom, $description, $prix, $id_produit]);

// Gestion des nouvelles images
// Gestion des nouvelles images avec Cloudinary
if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
    $files = $_FILES['new_images'];
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== 0) continue;

        try {
            $upload = $cloudinary->uploadApi()->upload(
                $files['tmp_name'][$i],
                ['folder' => 'catalogue']
            );
            $image_url = $upload['secure_url'];

            $insert = $bdd->prepare('INSERT INTO produit_images(produit_id, image) VALUES(?, ?)');
            $insert->execute([$id_produit, $image_url]);
        } catch (Exception $e) {
            $_SESSION['erreur'] = "Erreur lors de l'upload : " . $e->getMessage();
        }
    }
    
}