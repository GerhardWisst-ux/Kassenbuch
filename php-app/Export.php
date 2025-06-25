
<?php

require 'db.php';
session_start();

 if (!isset($_SESSION['userid'])) 
 {
    header('Location: Login.php'); // zum Loginformular
 }

// Abfrage der E-Mail vom Login
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];

// Sicherstellen, dass keine Ausgabe erfolgt
ob_clean(); // Bereinigt den Ausgabepuffer

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Kassenbuch Buchungen ' . $email . '.csv"');
// Öffne einen temporären Speicher für die CSV-Datei
$output = fopen('php://output', 'w');

// Schreibe die Kopfzeile der CSV-Datei
fputcsv($output, ['Datum', 'VonAn', 'Beschreibung', 'Betrag', 'Typ','UserId']);

// Hole die Daten aus der Tabelle
$sql = "SELECT Datum, VonAn, Beschreibung, Betrag, Typ, UserId FROM buchungen WHERE userid = :userid and barkasse =1 ORDER BY datum DESC";      
$stmt = $pdo->prepare($sql);
$stmt->execute(['userid' => $userid]); 

// Schreibe jede Zeile in die CSV-Datei
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

// Schließe den CSV-Speicher
fclose($output);

// Beende das Skript, um weitere Ausgaben zu verhindern
exit();
?>
