<?php
include "product.php";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeShop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="logo">
        <a href="index.php">ShoeShop</a>
    </div>

    <nav class="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php">Products</a></li>
            
        </ul>
    </nav>

    <div class="cart">
        <a href="cart.php">Cart</a>
    </div>

    <div class="auth">
        <a href="#" >Contact</a>
        <a href="#" >|</a>
        <a href="login.php">Login</a>
    </div>
</header>

<div class="filters-wrapper">
    <div class="search">
        <form action="index.php" method="GET">


            <input type="search" name="q" placeholder="Search...">
            <button type="submit">Find</button>
        </form>
    </div>

    <div class="filters">
        <span class="filter-label">Filter:</span>

    </div>
</div>

<main class="main-wrapper">

    <div class="products">
    </div>

    <aside class="categories">
        <h4>Categories</h4>

        <a href="index.php" class="category-btn <?php if ($activeCategory == '') echo 'active'; ?>">
            All
        </a>
    </aside>

</main>

<footer>
    <p>&copy; 2026 ShoeShop Ostrava</p>
</footer>

</body>
</html>