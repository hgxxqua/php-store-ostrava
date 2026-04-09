<?php

// no tutaj mamy poprosty easy logout nie wiem czemu nie zrobliem w functions.php
session_start();    
session_unset();      
session_destroy();    

header("Location: main.php"); 
exit();
?>