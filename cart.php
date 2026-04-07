<?php
session_start();
require_once __DIR__ . '/functions.php';

// Obsługa akcji koszyka
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'add') {
        addToCart((int)$_POST['product_id']);
        header("Location: main.php"); exit;
    }
    if (($_POST['action'] ?? '') === 'order') {
        if (placeOrder()) {
            $_SESSION['msg'] = "Zamówienie zostało złożone!";
            header("Location: cabinet.php"); exit;
        }
    }
    if (($_POST['action'] ?? '') === 'clear') {
        unset($_SESSION['cart']);
        header("Location: cart.php"); exit;
    }
}

$db = polacz_z_baza();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk — ShoeShop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #1a1a1a; }
        .cart-table th, .cart-table td { padding: 15px; border: 1px solid #333; text-align: left; }
        .total-box { margin-top: 20px; text-align: right; font-size: 20px; color: #ccff00; }
        .btn-order { background: #ccff00; color: #000; padding: 10px 20px; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo"><a href="main.php">ShoeShop</a></div>
        <div class="auth"><a href="main.php">Powrót do sklepu</a></div>
    </header>

    <div class="main-wrapper" style="display:block;">
        <h1>Twój koszyk</h1>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Koszyk jest pusty.</p>
        <?php else: ?>
            <table class="cart-table">
                <tr><th>Produkt</th><th>Cena</th><th>Ilość</th><th>Suma</th></tr>
                <?php 
                $grandTotal = 0;
                foreach ($_SESSION['cart'] as $id => $qty): 
                    $res = mysqli_query($db, "SELECT * FROM products WHERE id = $id");
                    $p = mysqli_fetch_assoc($res);
                    $sum = $p['price'] * $qty;
                    $grandTotal += $sum;
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $p['price'] ?> zl</td>
                    <td><?= $qty ?></td>
                    <td><?= $sum ?> zl</td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <div class="total-box">Suma całkowita: <?= $grandTotal ?> zl</div>
            
            <div style="display:flex; gap:10px; justify-content: flex-end; margin-top:20px;">
                <form method="POST"><button name="action" value="clear" class="tag-btn">Wyczyść</button></form>
                <form method="POST"><button name="action" value="order" class="btn-order">Złóż zamówienie</button></form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>