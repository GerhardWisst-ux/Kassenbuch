<?php
// includes/bestaende_berechnen.php

function berechneBestaende(PDO $pdo, int $userid, int $kassennummer, int $jahr): array
{
    $eingefuegt = 0;
    $aktualisiert = 0;
    $gesamtSaldo = 0;

    // Anfangsbestand ermitteln (Saldo bis Ende Vorjahr)
    $erstesDatum = "$jahr-01-01";
    $stmtVor = $pdo->prepare("
        SELECT bestand 
        FROM bestaende
        WHERE userid = :userid 
          AND datum < :erstesDatum
          AND kassennummer = :kassennummer
        ORDER BY datum DESC
        LIMIT 1
    ");
    $stmtVor->execute([
        'userid' => $userid,
        'erstesDatum' => $erstesDatum,
        'kassennummer' => $kassennummer
    ]);
    $anfangsbestand = (float) $stmtVor->fetchColumn() ?: 0;

    $saldoVormonat = $anfangsbestand;

    for ($monat = 1; $monat <= 12; $monat++) {
        // Monats-Summen berechnen
        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN typ='Einlage' THEN betrag ELSE 0 END) AS einlagen,
                SUM(CASE WHEN typ='Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
            FROM buchungen
            WHERE userid = :userid 
              AND kassennummer = :kassennummer 
              AND barkasse = 1
              AND YEAR(datum) = :jahr 
              AND MONTH(datum) = :monat
        ");
        $stmt->execute([
            'userid' => $userid,
            'jahr' => $jahr,
            'monat' => $monat,
            'kassennummer' => $kassennummer
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $einlagen = (float) ($result['einlagen'] ?? 0);
        $ausgaben = (float) ($result['ausgaben'] ?? 0);

        // Neuer Saldo
        $saldo = $saldoVormonat + $einlagen - $ausgaben;
        $datum = date("$jahr-$monat-01");

        // Prüfen ob Eintrag existiert
        $checkStmt = $pdo->prepare("
            SELECT id FROM bestaende
            WHERE userid = :userid 
              AND kassennummer = :kassennummer 
              AND YEAR(datum) = :jahr 
              AND MONTH(datum) = :monat
        ");
        $checkStmt->execute([
            'userid' => $userid,
            'jahr' => $jahr,
            'monat' => $monat,
            'kassennummer' => $kassennummer
        ]);
        $id = $checkStmt->fetchColumn();

        if ($id) {
            // Update
            $stmtUpdate = $pdo->prepare("
                UPDATE bestaende 
                SET ausgaben = ?, einlagen = ?, bestand = ?, monat = ?, kassennummer = ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([$ausgaben, $einlagen, $saldo, $monat, $kassennummer, $id]);
            $aktualisiert++;
        } else {
            // Insert
            $stmtInsert = $pdo->prepare("
                INSERT INTO bestaende (datum, ausgaben, einlagen, bestand, monat, userid, kassennummer)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([$datum, $ausgaben, $einlagen, $saldo, $monat, $userid, $kassennummer]);
            $eingefuegt++;
        }

        $saldoVormonat = $saldo;
        $gesamtSaldo = $saldo;
    }

    return [
        'eingefuegt' => $eingefuegt,
        'aktualisiert' => $aktualisiert,
        'saldo' => $gesamtSaldo
    ];
}
