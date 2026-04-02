<?php
require_once("db.php");

function login($username, $password){
$db = polacz_z_baza();
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
    $_SESSION["error"] = "Ten login jest już zajęty";
    header("Location: register.php");
    exit();
}

}

// Проверяет свободен ли логин: возвращает true если не занят, false если уже существует
function checklogin($username, $db){

$check = mysqli_fetch_assoc(mysqli_query($db,"SELECT COUNT(*) AS count FROM users WHERE name = '$username'"));
if($check['count']>0){
    return false;
}else if($check['count']== 0){
    return true;
    header("Location: index.php");
}
}
?>