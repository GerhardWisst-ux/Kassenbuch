<?php
http_response_code(404); // HTTP-Status 404 setzen
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seite nicht gefunden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .error-container {
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #102dd3ff;
        }
        .error-text {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .btn-custom {
            background-color: #102dd3ff;
            color: #fff;
            margin-top: 1rem;
        }
        .btn-custom:hover {
            background-color: #081e9cff;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-text">Oops! Diese Seite gibt es nicht.</div>
        <p class="mb-4">Die angeforderte Seite wurde nicht gefunden oder existiert nicht mehr.</p>

        <!-- Startseiten-Link mit absolutem Pfad -->
        <a href="/Cash/index.php" class="btn btn-custom btn-lg">
            <i class="bi bi-house"></i> Zur Startseite
        </a>
                
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
