<?php
// Créer la base de données MySQL
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS laravel_soap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de données 'laravel_soap' créée avec succès!" . PHP_EOL;
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
