<?php
require '../db.php';
session_start();
$userid = $_SESSION['userid'];
$kassennummer = $_POST['kassennummer'] ?? 0;
$monat = $_POST['monat'] ?? '';

$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

// Basisquery
$where = "WHERE userid = :userid AND kassennummer = :kassennummer AND barkasse = 1";
$params = ['userid' => $userid, 'kassennummer' => $kassennummer];

// Monat filtern, falls gesetzt
if (!empty($monat)) {
    $where .= " AND DATE_FORMAT(datum, '%Y-%m') = :monat";
    $params['monat'] = $monat;
}

// Total Records ohne Paging
$stmt = $pdo->prepare("SELECT COUNT(*) FROM buchungen $where");
$stmt->execute($params);
$recordsTotal = (int)$stmt->fetchColumn();

// Daten holen mit LIMIT für Paging
$stmt = $pdo->prepare("SELECT * FROM buchungen $where ORDER BY datum DESC LIMIT :start, :length");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':length', $length, PDO::PARAM_INT);
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value);
}
$stmt->execute();
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        date('d.m.Y', strtotime($row['datum'])),
        $row['typ'],
        $row['belegnr'],
        number_format($row['betrag'], 2, ',', '.') . " €",
        '', // MwSt placeholder
        '', // erm placeholder
        $row['vonan'],
        $row['beschreibung'],
        "<a href='EditBuchung.php?id={$row['id']}' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a>
         <a href='DeleteBuchung.php?id={$row['id']}' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>"
    ];
}

$response = [
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsTotal,
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
