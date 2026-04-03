<?php 
session_start();
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>new index</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Trispace:wght@100..800&display=swap" rel="stylesheet">
    <style>
       *{
margin: 0;
padding: 0;


  font-family: "Trispace", sans-serif;
  font-optical-sizing: auto;
  font-weight: <weight>;
  font-style: normal;
  font-variation-settings:
    "wdth" 100;

       } 
       header{
        display: flex;
        width: 200vh;
        height:100vh;
        justify-content: center;
        align-items :center;
        background-color: black;
       }
       a{
        padding: 50px;
        border: 2px solid #ccff00;
        text-decoration: none ;
        color: #ccff00;
       }
       a:hover{
        background-color: #ccff00;
        color: black; 
       }




    </style>
</head>
<body>
    <header>
        <a href ="main.php">Cl1ck here if y0u n9ed swag</a>
    </header>
</body>
</html>