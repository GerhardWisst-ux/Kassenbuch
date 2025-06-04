<?php
if (headers_sent()) {
    die("Headers wurden bereits gesendet.");
}
ob_start();
session_start();
?>

<!DOCTYPE html>
<html></html>
<head>
    <title>Kassenbuch Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/jquery.min"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* Allgemeine Einstellungen */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }


        .topnav {
            background-color: #2d3436;
            overflow: hidden;
            display: flex;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .topnav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .topnav a:hover {
            background-color: rgb(161, 172, 169);
            color: #2d3436;
        }

        .topnav .icon {
            display: none;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            padding: 0 10px;
            display: flex;
            background-color: #f4f7f6;
            justify-content: center;
            align-items: center;
        }

        #login-row {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        #login-column {
            max-width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login_form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .login_form h3 {
            font-size: 20px;
            text-align: center;
        }

        /* Google & Apple button styling */
        .login_form .login_option {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .login_form .login_option .option {
            width: calc(100% / 2 - 12px);
        }

        .login_form .login_option .option a {
            height: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            background: #F8F8FB;
            border: 1px solid #DADAF2;
            border-radius: 5px;
            margin: 34px 0 24px 0;
            text-decoration: none;
            color: #171645;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .login_form .login_option .option a:hover {
            background: #ededf5;
            border-color: #626cd6;
        }

        .login_form .login_option .option a img {
            max-width: 25px;
        }

        .login_form p {
            text-align: center;
            font-weight: 500;
        }

        .login_form .separator {
            position: relative;
            margin-bottom: 24px;
        }

        /* Login option separator styling */
        .login_form .separator span {
            background: #fff;
            z-index: 1;
            padding: 0 10px;
            position: relative;
        }

        .login_form .separator::after {
            content: '';
            position: absolute;
            width: 100%;
            top: 50%;
            left: 0;
            height: 1px;
            background: #C2C2C2;
            display: block;
        }

        form .input_box label {
            display: block;
            font-weight: 500;
            margin-bottom: 4px;
        }

        /* Input field styling */
        form .input_box input {
            width: 100%;
            height: 57px;
            border: 1px solid #DADAF2;
            border-radius: 5px;
            outline: none;
            background: #F8F8FB;
            font-size: 17px;
            padding: 0px 20px;
            margin-bottom: 25px;
            transition: 0.2s ease;
        }

        form .input_box input:focus {
            border-color:rgb(42, 42, 44);
        }

        form .input_box .password_title {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        form .input_box {
            position: relative;
        }

        a {
            text-decoration: none;
            color: #626cd6;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Login button styling */
        form button {
            width: 100%;
            height: 56px;
            border-radius: 5px;
            border: none;
            outline: none;
            background-color: rgb(97, 104, 102);
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            text-transform: uppercase;
            cursor: pointer;
            margin-bottom: 28px;
            transition: 0.3s ease;
        }

        form button:hover {
            background-color: rgb(60, 61, 61);
            color: rgb(235, 240, 241);
        }

        label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-top: px;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 16px;
            transition: border 0.3s ease;
        }

        .form-control:focus {
            border: 1px solid #2d3436;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #2d3436;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #f4f7f6;
            color: #2d3436;
        }

        .text-danger {
            color: rgb(110, 21, 11);
            font-size: 14px;
            margin-top: 10px;
        }

        .text-right a {
            font-size: 14px;
            text-decoration: none;
            color: #2d3436;
            transition: color 0.3s ease;
        }

        .text-right a:hover {
            color: rgb(60, 61, 61);
            background-color: rgb(162, 167, 168);
        }

        /* Responsive Design */
        @media screen and (max-width: 600px) {
            .topnav a:not(:first-child) {
                display: none;
            }

            .topnav a.icon {
                display: block;
                font-size: 30px;
            }

            .topnav.responsive {
                position: relative;
            }

            .topnav.responsive .icon {
                position: absolute;
                right: 0;
                top: 0;
            }

            .topnav.responsive a {
                display: block;
                text-align: left;
            }

            #login-column {
                max-width: 90%;
            }
        }
    </style>
</head>

<body>
    <?php
    
    require 'db.php';
    $_SESSION['userid'] = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $passwort = trim($_POST['passwort']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Ungültige E-Mail-Adresse.";
        } else {
            try {
                $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $statement->execute(['email' => $email]);
                $user = $statement->fetch();

                if ($user !== false && password_verify($passwort, $user['passwort'])) {
                    $_SESSION['userid'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    redirect("Index.php");
                    exit();
                } else {
                    $errorMessage = "E-Mail oder Passwort war ungültig.";
                }
            } catch (PDOException $e) {
                $errorMessage = "Datenbankfehler: " . $e->getMessage();
            }
        }       
    }

    function redirect($url) {
        header('Location: '.$url);
        die();
    }
    ?>

    <div class="topnav" id="myTopnav">
        <a class="navbar-brand" href="Index.php"><i class="fa-solid fa-house"></i></a>
        <!-- <a href="Index.php" class="active">Haupseite</a> -->
        <!-- <a href="Buchungsarten.php">Buchungsarten</a>
        <a href="Bestaende.php">Bestände</a>
        <a class="disabled"  href="Impressum.php">Impressum</a>
        <a href="javascript:void(0);" class="icon" onclick="NavBarClick()">
            <i class="fa fa-bars"></i>
        </a> -->
    </div>

    <div id="login">
        <form id="loginform" method="post" action="?login=1" class="login_form needs-validation" novalidate>
            <div id="login">
                <div class="container">
                    <div id="login-row" class="row justify-content-center align-items-center">
                        <div id="login-column" class="col-md-6">
                            <h1>Kassenbuch Login</h1>
                            <div id="login-box" class="col-md-12">

                                <div class="input_box">
                                    <label for="email" class="text-dark">Benutzer:</label><br>
                                    <input type="text" name="email" placeholder="Benutzer eingeben" required id="email"
                                        class="form-control">
                                </div>
                                <div class="input_box">
                                    <label for="password" class="text-dark">Passwort:</label><br>
                                    <input type="password" name="passwort" placeholder="Passwort eingeben" required
                                        id="passwort" class="form-control">
                                </div>
                                <div class="input_box">
                                    <button type="submit" class="btn btn-success" name="login" id="login">
                                        Anmelden
                                    </button>
                                </div>
                                <div id="error-link" class="text-danger">
                                    <?php
                                    if (isset($errorMessage)) {
                                        echo $errorMessage;
                                    } ?>
                                </div>
                                <div id="register-link" class="text-right">
                                    <a href="Register.php" class="text-dark">Registierung</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            function NavBarClick() {
                var x = document.getElementById("myTopnav");
                if (x) {
                    if (x.className === "topnav") {
                        x.className += " responsive";
                    } else {
                        x.className = "topnav";
                    }
                }
            }

        </script>

</body>

</html>