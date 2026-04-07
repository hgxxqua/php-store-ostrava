<?php
session_start();
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION["login-in"])) {
    $_SESSION["username"] = "";
    $_SESSION["email"]    = "";
    $_SESSION["role"]     = "user";
    $_SESSION["login-in"] = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swag Store Ostrava</title>
    <?= get_landing_styles() ?>
</head>
<body>
    <?= render_landing_hero() ?>
    <?= render_landing_flow() ?>
    <?= render_landing_cta() ?>
    <?= render_landing_crack_overlay() ?>
    <?= get_landing_scripts() ?>
</body>
</html>