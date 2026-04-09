<?php 
session_start();
require_once __DIR__ . '/functions.php';
if (!isset($_SESSION["login-in"])) {
    $_SESSION["username"] = "";
    $_SESSION["email"]    = "";
    $_SESSION["role"]     = "user";
    $_SESSION["login-in"] = false; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>new index</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Trispace:wght@100..800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Trispace", sans-serif;
        }

        body, html {
            height: 100%;
            background-color: black;
            overflow: hidden; 
        }

        header {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .golowa {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1; 
        }

        .golowa img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
            filter: brightness(0.6) drop-shadow(0 0 50px rgba(204, 255, 0, 0.2));
            animation: pulse 5s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.02); opacity: 1; }
        }

        a {
            position: relative;
            z-index: 2;
            padding: 40px 60px;
            border: 3px solid #ccff00;
            text-decoration: none ;
            color: #ccff00;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 1.5rem;
            background-color: rgba(0, 0, 0, 0.5); 
            backdrop-filter: blur(5px); 
            transition: all 0.3s ease;
        }

        a:hover {
            background-color: #ccff00;
            color: black; 
            box-shadow: 0 0 50px #ccff00;
            transform: scale(1.1);
        }

    </style>
</head>
<body>

    <header>
        <div class="golowa">
            <img src="krosovki.png" alt="Full Screen Background">
        </div>

        <a href="main.php">Cl1ck here if y0u n9ed swag</a>



        <!--
                JEZELI MAMY ROLE ADMIN W SESJI TO ZROBILEM DEBUG TAKI 
                ARRAY CALEJ SESJI DO VIZUALIZACJI
-->
    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
    <div style="color: white; background: #040404; padding: 20px; margin-left: 20px; border: 1px dashed #ccff00; opacity: 100%; animation: none; z-index: 1; " >
        <h3>Debug Session:</h3>
        <?php
            echo '<pre>';
            // caly array Sesji do vizualizacji 
            print_r($_SESSION); 
            echo '</pre>';
            
        ?>
    </div>
    <?php endif; ?>


</header>
</body>
</html>