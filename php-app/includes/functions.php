<?php
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && strtolower($d->format($format)) === strtolower($date);
}

function checkGermanDate($date) {
    $tempDate = explode('.', $date);
    if (count($tempDate) !== 3) {
        return false; // nicht im Format d.m.Y
    }
    return checkdate((int)$tempDate[1], (int)$tempDate[0], (int)$tempDate[2]);
}

function checkGermanDate2($date) {
    $d = DateTime::createFromFormat('d.m.Y', $date);
    return $d && $d->format('d.m.Y') === $date;
}

function Redirect()
{
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: Index.php'); // Fallback, falls kein Referrer vorhanden
    }    
}

?>