<?php
ob_start();
session_start();

$host = 'db';
$dbname = 'kassenbuch';
$user = 'kassenbuch';
$pass = 'n';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}

// Users-Tabelle erstellen
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
");

// Testuser einfügen, falls nicht vorhanden
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email=:email");
$stmt->execute([':email'=>'test@example.com']);
if ($stmt->fetchColumn() == 0) {
    $pdo->prepare("INSERT INTO users (email,password) VALUES (:email,:pass)")
        ->execute([
            ':email'=>'test@example.com',
            ':pass'=>md5('geheim')
        ]);
}

echo "Migration abgeschlossen!\n";
