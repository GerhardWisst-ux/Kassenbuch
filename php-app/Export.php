<?php
ob_start();
session_start();
if (empty($_SESSION['userid'])) {
    http_response_code(403);
    echo json_encode(['error' => 'not authorized']);
    header('Location: Login.php'); // zum Loginformular
    exit;
}

$userid = $_SESSION['userid'];
$email = $_SESSION['email'];

// Parameter auslesen
$type = $_GET['type'] ?? '';
$monat = $_GET['monat'] ?? date('m'); // Standard: aktueller Monat
$jahr = $_GET['jahr'] ?? date('Y');  // Standard: aktuelles Jahr

// Sicherstellen, dass keine Ausgabe erfolgt
ob_clean();

header('Content-Type: text/csv');
if ($type === 'monat') {
    header('Content-Disposition: attachment; filename="Kassenbuch_Buchungen_' . $monat . '-' . $jahr . ' '  . $email . '.csv"');
}
else {
    header('Content-Disposition: attachment; filename="Kassenbuch_Buchungen_' . $jahr . ' ' . $email . '.csv"');
}    

$output = fopen('php://output', 'w');

// Kopfzeile
fputcsv($output, ['Datum', 'VonAn', 'Beschreibung', 'Betrag', 'Typ', 'UserId']);

// Grund-Query
$sql = "SELECT Datum, VonAn, Beschreibung, Betrag, Typ, UserId 
        FROM buchungen 
        WHERE userid = :userid AND barkasse = 1";

// Filter je nach Auswahl
$params = ['userid' => $userid];

if ($type === 'monat') {
    $sql .= " AND MONTH(Datum) = :monat AND YEAR(Datum) = :jahr";
    $params['monat'] = $monat;
    $params['jahr'] = $jahr;
} elseif ($type === 'jahr') {
    $sql .= " AND YEAR(Datum) = :jahr";
    $params['jahr'] = $jahr;
}

$sql .= " ORDER BY datum DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// CSV schreiben
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
