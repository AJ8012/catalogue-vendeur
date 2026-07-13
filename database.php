<?php
// Active l'affichage des erreurs (utile en développement)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Détection automatique : local ou production
$is_local = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');

if ($is_local) {
    // -------- CONNEXION LOCALE (XAMPP) --------
    $host = 'localhost';
    $port = 3306;
    $dbname = 'catalogue-bd';
    $username = 'root';
    $password = '';
    $ssl_options = [];
} else {
    // -------- CONNEXION AIVEN (production) --------
    $host = getenv('DB_HOST') ?: 'service-sql-catalogue-catalogue111.c.aivencloud.com';
    $port = getenv('DB_PORT') ?: 15682;
    $dbname = getenv('DB_NAME') ?: 'defaultdb';
    $username = getenv('DB_USER') ?: 'avnadmin';
    $password = getenv('DB_PASS') ?: '';  // ← Variable d'environnement, PAS de mot de passe en dur
    $ssl_options = [
        PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/ca.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
}

try {
    $bdd = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password,
        $ssl_options
    );
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>