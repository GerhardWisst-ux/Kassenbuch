<?php
$baseUrl = '/Webshop2/'; // z.B. Root-URL der Webseite, oder '/meinprojekt/' falls in Unterordner
?>

<style>
    footer.site-footer {
        background: linear-gradient(90deg, #a8d0ff 0%, #d6e4ff 100%);
        color: #333;
        font-size: 0.9rem;
        border-top: 1px solid #bbb;
        box-shadow: 0 0 0 0 #a8d0ff 0% rgba(40, 167, 69, 0.25);
    }

    footer.site-footer a {
        color: #0645ad;
        text-decoration: none;
        
    }

    footer.site-footer a:hover,
    footer.site-footer a:focus {
        text-decoration: underline;
        color: #042a6a;
    }
</style>

<footer class="site-footer py-3 mt-5">
    <div class="container text-center">
        <small>
            &copy; <?= date('Y') ?> CashControl – Alle Rechte vorbehalten |
            <a href="<?= $baseUrl ?>impressum.php" target="_blank" rel="noopener noreferrer">Impressum</a>                
            <!-- <a href="<?= $baseUrl ?>agb.php" target="_blank" rel="noopener noreferrer">AGB's</a>
            <a href="<?= $baseUrl ?>datenschutzerklaerung.php" target="_blank" rel="noopener noreferrer">Datenschutzerklaerung</a>
            <a href="<?= $baseUrl ?>widerrufsbelehrung.php" target="_blank" rel="noopener noreferrer">Widerrufsbelehrung</a>             -->
        </small>
        <small class="d-block mt-2">
            Diese Seite verwendet Cookies, um die Benutzererfahrung zu verbessern. Durch die Nutzung dieser Seite stimmen Sie der Verwendung von Cookies zu.
        </small>
    </div>
</footer>