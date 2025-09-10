<?php
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
$sql = "
    SELECT datum, belegnr, vonan, beschreibung, betrag 
    FROM buchungen 
    WHERE userid = :userid 
      AND kassennummer = :kassennummer
      AND buchungsart = :buchungsart
";

$params = [
    'userid' => $userid,
    'kassennummer' => $kassennummer,
    'buchungsart' => $buchungsartId,
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
    exit("<div>Keine Details vorhanden.</div>");
}

// Tabelle ausgeben
echo "<table id='TableDetail' class='table table-sm table-striped'>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Beleg-Nr</th>
                <th>Buchungsart</th>
                <th>Beschreibung</th>                
                <th class='text-end'>Betrag</th>
            </tr>
        </thead>
        <tbody>";

$summe = 0;

foreach ($rows as $r) {
    $summe += $r['betrag'];

    echo "<tr>
            <td>" . htmlspecialchars(date('d.m.Y', strtotime($r['datum']))) . "</td>
            <td>" . htmlspecialchars($r['belegnr']) . "</td>
            <td>" . htmlspecialchars($r['vonan']) . "</td>
            <td>" . htmlspecialchars($r['beschreibung']) . "</td>            
            <td class='text-end'>" . number_format($r['betrag'], 2, ',', '.') . " €</td>
          </tr>";
}

// Summenzeile
echo "<tr class='table-secondary fw-bold'>
        <td colspan='4' class='text-end'>Summe:</td>
        <td class='text-end'>" . number_format($summe, 2, ',', '.') . " €</td>
      </tr>";

echo "</tbody></table>";
?>

<script>
   

    // Automatisch Berechnung auslösen, wenn das Jahr geändert wird
    document.getElementById('jahr')?.addEventListener('change', function () {
        const form = document.getElementById('bestaendeForm');
        if (!form) return;

        const hiddenButton = document.createElement('input');
        hiddenButton.type = 'hidden';
        hiddenButton.name = 'berechne_bestaende';
        hiddenButton.value = '1';

        form.appendChild(hiddenButton);
        form.submit();
    });
</script>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    .table-secondary {
        background-color: #e9ecef;
    }
</style>