<?php
session_start();
require(__DIR__ . '/../database.php');

if (empty($_SESSION['id']) || empty($_GET['id']) || empty($_GET['produit'])) {
    header('Location: ../index.php');
    exit();
}

$id_img = intval($_GET['id']);
$id_produit = intval($_GET['produit']);

// Vérifier que l'image appartient au produit de l'utilisateur
$check = $bdd->prepare('
    SELECT pi.image 
    FROM produit_images pi 
    JOIN produits p ON p.id = pi.produit_id 
    WHERE pi.id = ? AND p.id_utilisateur = ?
');
$check->execute([$id_img, $_SESSION['id']]);
$img = $check->fetch();
if (!$img) {
    header('Location: ../modifier_produit.php?id=' . $id_produit);
    exit();
}

// Supprimer le fichier physique
$file_path = __DIR__ . '/../uploads/' . $img['image'];
if (file_exists($file_path)) {
    unlink($file_path);
}

// Supprimer la ligne en base
$delete = $bdd->prepare('DELETE FROM produit_images WHERE id = ?');
$delete->execute([$id_img]);

header('Location: ../modifier_produit.php?id=' . $id_produit);
exit();