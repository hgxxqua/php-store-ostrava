<?php
        session_start();
        require_once('functions.php');

        if(isset($_POST['username']))       {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email    = $_POST['email'];
            registration($username, $password, $email);
        }
    ?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    
    <title>Login</title>
    <style>

      body {
    margin: 0; 
    background: #0a0a0a; 
    font-family: Verdana; 
    overflow: hidden;
    margin-top: 15px;
    margin-left: 15px;
}

.glowa {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-y: hidden;  
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

.user-box { position: relative; }

.user-box input {
    width: 100%;
    padding: 10px 0;
    color: #fff;
    margin-bottom: 30px;
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
}

a {
    text-decoration: none;
    color: #ccff00;
    font-weight: bold;
}

a:hover {
    color: #fff; 
}

.blok-dla-knopki
{
    width:100%;
    display:flex;
    justify-content:center;  
    text-align:center; 
    color:white;
}


.knopka-dla-rega{ 
    font-size:15px;
    margin-bottom:30px;
    text-align:center;  
    height:30px;  
    width:150px;
    font-family: Verdana; 
    cursor:pointer;
}


    </style>
</head>
<body>
    




    <a href="#"><h2 style="color:white">Back</h2></a>
    <div class="glowa">
        <div class="login-box">
            
        <h2>Rejestracja</h2>
                        
                <?php if(isset($_SESSION["error"])): ?>
                    <p style="color:red; text-align:center"><?= $_SESSION["error"] ?></p>
                    <?php unset($_SESSION["error"]); ?>
                <?php endif; ?>

            <form method='POST' action=''>
                <div class="user-box">
                    <input type="text" name="username" required="">
                    <label>Username</label>
                </div>
                <div class="user-box">
                    <input type="password" name="password" required="">
                    <label>Password</label>
                </div>
                    <div class="user-box">

<input type="email" name="email" required="">
<label>Email</label>
</div>

<div class="blok-dla-knopki">
    <input type="submit" class="knopka-dla-rega"  value="Rejestruj się" required=""></input>
    </div>  
                    
                  <div class="rejestracja">
                        Już masz konto? <a href="login.php">Login</a>
                       </div>
            </form>
        </div>
    </div>
</body>
</html>
