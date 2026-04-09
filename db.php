<?php
// tutaj nie ma co kommentowac 
// zwylke podlaczenie bd

define('DB_HOST' , 'localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','php-store');

function polacz_z_baza(){

$connectionDB = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if(!$connectionDB){
    die('Nie udalo sie'. "<br>" . mysqli_connect_error() . "<br>" .  "Baza dannych musi nazywac sie: php-store" . "<br>");
}

return $connectionDB;
}


?>