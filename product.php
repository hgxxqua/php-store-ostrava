<?php

session_start();
require_once("functions.php");

// Получаем товар по id из GET-параметра; если не найден — редирект на главную
$product = getProductById($_GET['id'] ?? 0);
if(!$product){ header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> — ShoeShop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="logo"><a href="index.php">ShoeShop</a></div>
    <div class="auth">
        <a href="cart.php">Cart</a>
        <a href="#">|</a>
        <?php if(!empty($_SESSION['login-in'])): ?>
            <a href="cabinet.php"><?= htmlspecialchars($_SESSION['username']) ?></a>
            <a href="#">|</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="index.php?modal=login">Login</a>
        <?php endif; ?>
    </div>
</header>

<main class="product-page">
    <a href="index.php" class="btn-back">← Back</a>

    <div class="product-detail">
        <?php
            $imgSrc = !empty($product['image'])
                ? (strpos($product['image'], '/') === false ? 'uploads/' . $product['image'] : $product['image'])
                : '';
        ?>
        <?php if($imgSrc): ?>
            <img src="<?= htmlspecialchars($imgSrc) ?>" class="product-detail-img" alt="<?= htmlspecialchars($product['name']) ?>">
        <?php endif; ?>
        <div class="product-detail-info">
            <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="price"><?= $product['price'] ?> zl</p>
            <p class="description"><?= htmlspecialchars($product['description']) ?></p>
            <p class="product-meta">Brand: <?= htmlspecialchars($product['brand']) ?></p>
            <p class="product-meta">Size: <?= htmlspecialchars($product['size']) ?></p>
            <p class="product-meta">In stock: <?= $product['stock'] ?></p>
            <a href="cart.php?add=<?= $product['id'] ?>" class="btn-view">Add to cart</a>
        </div>
    </div>
</main>

</body>
</html>