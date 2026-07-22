<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../database.php');

// On vérifie que tous les champs sont remplis
if (!empty($_POST['nom']) && !empty($_POST['telephone']) && !empty($_POST['mdp'])) {

    $user_nom = htmlspecialchars($_POST['nom']);
    $user_tel = htmlspecialchars($_POST['telephone']);

    // Vérification du téléphone : 8 chiffres
    if (!preg_match('/^[0-9]{8}$/', $user_tel)) {
        $_SESSION['erreur'] = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
        header('Location: ../signup.php');
        exit();
    }

    // Vérification du mot de passe : 4 chiffres exactement (côté serveur)
    if (!preg_match('/^[0-9]{4}$/', $_POST['mdp'])) {
        $_SESSION['erreur'] = "Le mot de passe doit être composé de 4 chiffres (ex: 1234).";
        header('Location: ../signup.php');
        exit();
    }

    $user_mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    // Vérifier si le numéro de téléphone existe déjà
    $check = $bdd->prepare('SELECT id FROM utilisateurs WHERE telephone = ?');
    $check->execute(array($user_tel));

    if ($check->rowCount() == 0) {
        // Insertion du nouvel utilisateur
        $insert = $bdd->prepare('INSERT INTO utilisateurs(nom, telephone, mdp) VALUES(?, ?, ?)');
        $insert->execute(array($user_nom, $user_tel, $user_mdp));

        // Récupérer les infos pour connecter immédiatement l'utilisateur
        $getInfo = $bdd->prepare('SELECT id, nom FROM utilisateurs WHERE telephone = ?');
        $getInfo->execute(array($user_tel));
        $userInfos = $getInfo->fetch();

        $_SESSION['id'] = $userInfos['id'];
        $_SESSION['nom'] = $userInfos['nom'];

        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['erreur'] = "Ce numéro de téléphone est déjà utilisé.";
        header('Location: ../signup.php');
        exit();
    }
} else {
    $_SESSION['erreur'] = "Veuillez remplir tous les champs.";
    header('Location: ../signup.php');
    exit();
}