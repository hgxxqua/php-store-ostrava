<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    
    <title>Login</title>
    <style>

      body {
    margin: 0; 
    background: #0a0a0a; /* Очень темный фон как на сайте */
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
    background: #1a1a1a; /* Цвет фона карточки товара (темно-серый) */
    box-shadow: 0 15px 25px rgba(0,0,0,.8); /* Более глубокая тень */
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
    border-bottom: 1px solid #444; /* Сделал линию чуть темнее в покое */
    background: transparent;
    outline: none;
}

.user-box label {
    position: absolute;
    top: 0; left: 0;
    padding: 10px 0;
    color: #888; /* Серый цвет для неактивного состояния */
    pointer-events: none; 
    transition: .5s; 
}

/* АНИМАЦИЯ: Когда фокус на поле ИЛИ когда оно не пустое */
.user-box input:focus ~ label,
.user-box input:valid ~ label {
    top: -17px;
    font-size: 15px;
    color: #ccff00; /* ТОТ САМЫЙ ЛАЙМОВЫЙ цвет из CS2/Магазина */
}

/* Линия при фокусе тоже становится лаймовой */
.user-box input:focus {
    border-bottom: 1px solid #ccff00;
}

.rejestracja {
    text-align: center;
    color: #888; /* Текст "Нет аккаунта" серым */
}

a {
    text-decoration: none;
    color: #ccff00; /* Ссылка "Регистрация" лаймовым */
    font-weight: bold;
}

a:hover {
    color: #fff; /* При наведении на ссылку — белым */
}
    </style>
</head>
<body>
    <?php
    require_once('functions.php')
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    ?>




    <a href="#"><h2 style="color:white">Back</h2></a>
    <a href="login.php"><h2 style="color:white">Back</h2></a>
    <div class="glowa">
        <div class="login-box">
            <h2>Rejestracja</h2>
            <form>
                <div class="user-box">
                    <input type="text" name="Login" required="">
                    <label>Username</label>
                </div>
                <div class="user-box">
                    <input type="password" name="Hasło" required="">
                    <label>Password</label>

                    
                    <div class="rejestracja">
                        Już masz konto? <a href="login.php">Login</a>
                       </div>
            </form>
        </div>
    </div>
</body>
</html>
