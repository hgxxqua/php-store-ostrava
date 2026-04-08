<?php
session_start();
require_once __DIR__ . '/functions.php';

// Проверяем, пришла ли форма методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Login"])) {
    // Вызываем твою функцию из functions.php
    // Убедись, что в HTML name="Login" и name="Haslo"
    login($_POST["Login"], $_POST["Haslo"]);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie — ShoeShop</title>
    <style>
        body {
            margin: 0; 
            background: #0a0a0a; 
            font-family: Verdana; 
            overflow: hidden;
            padding-top: 15px;
            padding-left: 15px;
        }

        .glowa {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 400px;
            padding: 40px;
            background: #1a1a1a; 
            box-shadow: 0 15px 25px rgba(0,0,0,.8); 
            border-radius: 10px;
        }

        .login-box h2 { 
            color: white; 
            text-align: center; 
            margin-bottom: 30px; 
        }

        .user-box { position: relative; margin-bottom: 30px; }

        .user-box input {
            width: 100%;
            padding: 10px 0;
            color: #fff;
            border: none;
            border-bottom: 1px solid #444; 
            background: transparent;
            outline: none;
        }

        .user-box label {
            position: absolute;
            top: 0; left: 0;
            padding: 10px 0;
            color: #888; 
            pointer-events: none; 
            transition: .5s; 
        }

        .user-box input:focus ~ label,
        .user-box input:valid ~ label {
            top: -17px;
            font-size: 15px;
            color: #ccff00; 
        }

        .user-box input:focus {
            border-bottom: 1px solid #ccff00;
        }

        .rejestracja {
            text-align: center;
            color: #888; 
            margin-top: 20px;
        }

        a {
            text-decoration: none;
            color: #ccff00; 
            font-weight: bold;
        }

        a:hover {
            color: #fff; 
        }

        .blok-dla-knopki {
            width: 100%;
            display: flex;
            justify-content: center;  
            margin-top: 10px;
        }

        .knopka-dla-rega { 
            background: #f0f0f0;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            text-align: center;  
            height: 35px;  
            width: 160px;
            font-family: Verdana;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .knopka-dla-rega:hover {
            background: #ccff00;
            border-color: #ccff00;
        }
        
        .error-msg {
            color: #ff4444;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <a href="main.php"><h2 style="color:white; margin:0;">Wróć</h2></a>
    
    <div class="glowa">
        <div class="login-box">
            <h2>Logowanie</h2>

            <?php if(isset($_SESSION["error"])): ?>
                <div class="error-msg"><?= $_SESSION["error"] ?></div>
                <?php unset($_SESSION["error"]); ?>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="user-box">
                    <input type="text" name="Login" required="">
                    <label>Nazwa użytkownika</label>
                </div>
                
                <div class="user-box">
                    <input type="password" name="Haslo" required="">
                    <label>Hasło</label>
                </div>

                <div class="blok-dla-knopki">
                    <input type="submit" class="knopka-dla-rega" value="Zaloguj się"/>
                </div> 

                <div class="rejestracja">
                    Nie masz konta? <a href="register.php">Załóż konto</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>