<?php
ob_start();
session_start();

require 'db.php';

$userid = $_SESSION['userid'] ?? null;
$buchungsartId = $_POST['id'] ?? null;
$monat = $_POST['monat'] ?? null; // optionaler Monatsfilter
$kassennummer = $_SESSION['kassennummer'] ?? 1;

if (!$userid || !$buchungsartId) {
    exit('<div>Keine Daten verfügbar.</div>');
}

// SQL mit optionalem Monatsfilter
$sql = "SELECT datum, vonan, beschreibung, betrag 
        FROM buchungen 
        WHERE userid = :userid 
          AND kassennummer = :kassennummer
          AND buchungsart = :buchungsart";

$params = [
    'userid' => $userid,
    'kassennummer' => $kassennummer,
    'buchungsart' => $buchungsartId
];

if ($monat) {
    $sql .= " AND DATE_FORMAT(datum, '%Y-%m') = :monat";
    $params['monat'] = $monat;
}

$sql .= " ORDER BY datum DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo "<div>Keine Details vorhanden.</div>";
    exit;
}

echo "<table class='table table-sm table-striped'>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Beschreibung</th>
                <th style='text-align:right;'>Betrag</th>
            </tr>
        </thead>
        <tbody>";

foreach ($rows as $r) {
    echo "<tr>
            <td>" . htmlspecialchars(date('d.m.Y', strtotime($r['datum']))) . "</td>
            <td>" . htmlspecialchars($r['beschreibung']) . "</td>
            <td style='text-align:right;'>" . number_format($r['betrag'], 2, ',', '.') . " €</td>
          </tr>";
}

echo "</tbody></table>";
?>
