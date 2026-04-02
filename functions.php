<?php
// hello hi 
// here is a main functions for all shop
require_once("db.php");


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

$checklogin = checklogin($usernamedb,$db);
if($checklogin == true){

    mysqli_query($db,"INSERT INTO users (name , email, password , role) VALUES ('$usernamedb', '$emaildb', '$passworddb', 'user')");

    $_SESSION["username"] = $usernamedb;
    $_SESSION["email"] = $emaildb;
    $_SESSION["role"] = "user";
    $_SESSION["login-in"] = "true";
    


}else if(!$checklogin){
    echo"Ten login jest juz zajety";
}  

}

// Проверяет свободен ли логин: возвращает true если не занят, false если уже существует
function checklogin($username, $db){

$check = mysqli_fetch_assoc(mysqli_query($db,"SELECT COUNT(*) AS count FROM users WHERE name = '$username'"));
if($check['count']>0){
    return false;
}else if($check['count']== 0){
    return true;
}
}
?>