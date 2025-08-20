<?php
// Gibt Monatsdaten (Einlagen/Ausgaben) für ein Jahr als JSON zurück.
session_start();
if (empty($_SESSION['userid'])) {
    http_response_code(403);
    echo json_encode(['error' => 'not authorized']);
    exit;
}

require 'db.php';

$userid = (int)$_SESSION['userid'];
$jahr   = isset($_POST['jahr']) ? (int)$_POST['jahr'] : (int)date('Y');

$monatMapping = [
    1 => "Januar", 2 => "Februar", 3 => "März", 4 => "April",
    5 => "Mai", 6 => "Juni", 7 => "Juli", 8 => "August",
    9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember"
];

$einnahmen = array_fill(1, 12, 0.0);
$ausgaben  = array_fill(1, 12, 0.0);

// Robust: aggregieren nach MONTH(datum) und filtern nach userid + jahr
$sql = "SELECT MONTH(datum) AS m,
               SUM(einlagen) AS sum_einlagen,
               SUM(ausgaben) AS sum_ausgaben
        FROM bestaende
        WHERE userid = :uid AND YEAR(datum) = :jahr
        GROUP BY MONTH(datum)
        ORDER BY MONTH(datum)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $userid, 'jahr' => $jahr]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $m = (int)$row['m'];
    $einnahmen[$m] = (float)$row['sum_einlagen'];
    $ausgaben[$m]  = (float)$row['sum_ausgaben'];
}

// Arrays in der Reihenfolge Jan..Dez
$labels         = array_values($monatMapping);
$einlagenWerte  = [];
$ausgabenWerte  = [];
for ($i = 1; $i <= 12; $i++) {
    $einlagenWerte[] = $einnahmen[$i];
    $ausgabenWerte[] = $ausgaben[$i];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'monate'   => $labels,
    'einlagen' => $einlagenWerte,
    'ausgaben' => $ausgabenWerte
], JSON_UNESCAPED_UNICODE);
