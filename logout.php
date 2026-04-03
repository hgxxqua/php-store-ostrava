<?php
session_start();
$_SESSION = [];
if(ini_get('session.use_cookies')){
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']
    );
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
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
            text-align: center;
        }
        .login-box h2 { color: white; margin-bottom: 20px; }
        .login-box p { color: #888; margin-bottom: 30px; }
        a { text-decoration: none; color: #ccff00; font-weight: bold; }
        a:hover { color: #fff; }
        .blok-dla-knopki {
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 10px;
        }
        .knopka-dla-rega {
            background: #f0f0f0;
            border: 1px solid #ccc;
            text-align: center;
            height: 35px;
            width: 160px;
            font-family: Verdana;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .knopka-dla-rega:hover {
            background: #ccff00;
            border-color: #ccff00;
            color: #000;
        }
    </style>
</head>
<body>

    <a href="index.php"><h2 style="color:white; margin:0;">Back</h2></a>

    <div class="glowa">
        <div class="login-box">
            <h2>You have been logged out</h2>
            <p>See you next time!</p>
            <div class="blok-dla-knopki">
                <a href="login.php" class="knopka-dla-rega">Login</a>
                <a href="index.php" class="knopka-dla-rega">Home</a>
            </div>
        </div>
    </div>

</body>
</html>