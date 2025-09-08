<?php
try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=kassenbuch', 'kassenbuch', 'n');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
} catch (PDOException $e) {
    echo "Verbindung fehlgeschlagen: " . $e->getMessage();
}
?>
