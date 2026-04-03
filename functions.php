<?php
require_once __DIR__ . '/db.php';


// Авторизует пользователя по имени и паролю; запускает сессию при успехе
function login($username, $password){
    $db = polacz_z_baza();
    $result = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM users WHERE name = '$username' AND password = '$password' LIMIT 1"));
    if($result){
        $_SESSION["username"] = $result["name"];
        $_SESSION["email"]    = $result["email"];
        $_SESSION["role"]     = $result["role"];
        $_SESSION["login-in"] = "true";
        header("Location: index.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center'>Nieprawidłowy login lub hasło</p>";
    }
}

// Регистрирует нового пользователя и создаёт сессию; выводит сообщение если логин занят
function registration($usernamedb, $passworddb, $emaildb){
    $db = polacz_z_baza();

    // Защита от спецсимволов
    $u = mysqli_real_escape_string($db, $usernamedb);
    $p = mysqli_real_escape_string($db, $passworddb);
    $e = mysqli_real_escape_string($db, $emaildb);

    $is_free = checklogin($u, $db);

    if($is_free){
        // ВАЖНО: Добавил created_at и CURDATE(), иначе база отклоняет запрос
        $sql = "INSERT INTO users (name, email, password, role, created_at) 
                VALUES ('$u', '$e', '$p', 'user', CURDATE())";
        
        $res = mysqli_query($db, $sql);

        if($res){
            $_SESSION["username"] = $usernamedb;
            $_SESSION["email"] = $emaildb;
            $_SESSION["role"] = "user";
            $_SESSION["login-in"] = "true";
            header("Location: index.php");
            exit();    
        } else {
            // Если снова не сработает, эта строка покажет реальную причину
            die("Ошибка MySQL: " . mysqli_error($db));
        }
    } else {
        $_SESSION["error"] = "Ten login jest już zajęty";
        header("Location: register.php");
        exit();
    }
}

// Проверяет является ли текущий пользователь сессии администратором
// Возвращает true если role === 'admin', иначе false
function isAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Проверяет существует ли хотя бы один администратор в базе данных
// Используется чтобы показать кнопку создания первого админа если его ещё нет
function adminExists(){
    $db = polacz_z_baza();
    $result = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS count FROM users WHERE role = 'admin'"));



    return $result['count'] > 0;
}

// Проверяет свободен ли логин: возвращает true если не занят, false если уже существует
function checklogin($username, $db){
    $u = mysqli_real_escape_string($db, $username);
    $res = mysqli_query($db, "SELECT id FROM users WHERE name = '$u' LIMIT 1");
    
    if(mysqli_num_rows($res) > 0){
        return false; // Занят
    }
    return true; // Свободен
}


function loginstatus(){
if($_SESSION["login-in"] == true){
    return true;
}else{
    return false;
}
}

?>