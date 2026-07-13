<?php

//demarrer la session 

session_start();



//la vider 
$_SESSION = array();

//la detruire
session_destroy();


//rediriger vers la page de connexion


header('Location:../login.php');

