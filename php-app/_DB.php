<?php
try {
    $pdo = new PDO('mysql:host=db;dbname=cash', 'root', 'geheim');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}
?>
