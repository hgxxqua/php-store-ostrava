<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    
    <style>
        body {margin: 0; background: #eef0f5; font-family: Verdana; 
        overflow:hidden;
        margin-top:15px;
        margin-left:15px;}

        .glowa {
            
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-y:hidden;  
        }

        .login-box {
            width: 400px;
            padding: 40px;
            background: rgba(0,0,0,.5);
            box-shadow: 0 15px 25px rgba(0,0,0,.6);
            border-radius: 10px;
        }

        .login-box h2 { color: white; text-align: center; margin-bottom: 30px; }

        .user-box { position: relative; }


        .user-box input {
            width: 100%;
            padding: 10px 0;
            color: #fff;
            margin-bottom: 30px;
            border: none;
            border-bottom: 1px solid #fff;
            background: transparent;
            outline: none;
        }

        
        .user-box label {
            position: absolute;
            top: 0; left: 0;
            padding: 10px 0;
            color: #fff;
            pointer-events: none; /* Пропускает клик сквозь себя на input */
            transition: .5s; /* Плавность движения */
        }

        /* АНИМАЦИЯ: Когда фокус на поле ИЛИ когда оно не пустое (:valid) */
        .user-box input:focus ~ label,
        .user-box input:valid ~ label {
            top: -17px;
            font-size: 15px;
            color: #03e9f4;
        }

        .forgot-password{text-align:center;}


        .rejestracja{text-align:center; padding-top:10px;}


       

        a{text-decoration:none}

    </style>
</head>
<body>
    
    <div class="glowa">
        <div class="login-box">
            <h2>Login</h2>
            <form>
                <div class="user-box">
                    <input type="text" name="Login" required="">
                    <label>Username</label>
                </div>
                <div class="user-box">
                    <input type="password" name="Hasło" required="">
                    <label>Password</label>

                    <div class="forgot-password">
                        <a href="footer.php">Zapomniałeś hasło?</a>
</div>
                    
                    <div class="rejestracja">

                        Nie masz konta? <a href="header.php">Rejestruj się</a>
                       </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>