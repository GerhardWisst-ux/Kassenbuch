<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=Kassenbuch', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
} catch (PDOException $e) {
    echo "Verbindung fehlgeschlagen: " . $e->getMessage();
}
?>
