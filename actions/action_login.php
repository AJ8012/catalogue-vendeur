<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../database.php'); 

if(!empty($_POST['telephone']) && !empty($_POST['mdp'])){

    $tel_recup = htmlspecialchars($_POST['telephone']);
    $mdp_recup = $_POST['mdp'];

    // Recherche via la colonne telephone
    $check = $bdd->prepare('SELECT id, nom, mdp FROM utilisateurs WHERE telephone = ?');
    $check->execute(array($tel_recup));

    if($check->rowCount() == 1){
        $userInfos = $check->fetch();

        if(password_verify($mdp_recup, $userInfos['mdp'])){
            $_SESSION['id'] = $userInfos['id'];
            $_SESSION['nom'] = $userInfos['nom'];

            header('Location: ../index.php');
            exit();
        } else {
            $_SESSION['erreur'] = "Identifiants incorrects";
            header('Location: ../login.php');
            exit();
        }
    } else {
        $_SESSION['erreur'] = "Identifiants incorrects";
        header('Location: ../login.php');
        exit();
    }

} else {
    $_SESSION['erreur'] = "Veuillez remplir tous les champs.";
    header('Location: ../login.php');
    exit();
}