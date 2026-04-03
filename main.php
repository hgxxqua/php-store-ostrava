<?php
session_start();
require_once __DIR__ . '/functions.php';
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
    
    <div class="auth">
        <a href="#">Contact</a>
        <a href="#">|</a>
        <?php 
        if(isAdmin()){
           echo '<a href="admin.php">Admin Panel</a>';
           echo '<a href="#">|</a>';
        }                                 
        if($_SESSION["login-in"] == true){
            echo '<a href="cabinet.php">Cabinet</a>';
            echo '<a href="#">|</a>';
            echo '<a href="logout.php">Logout</a>';
            echo '<a href="#">|</a>';
        } else if($_SESSION["login-in"] == false){
            echo '<a href="login.php">Login</a>';
            echo '<a href="#">|</a>';
        } 
        ?>
    </div>
</header>

<div class="filters-wrapper">
    
    <div class="filters">
        <span class="filter-label">Filter by brand:</span>
        <a href="?brand=Balenciaga" class="tag-btn">Balenciaga</a>
        <a href="?brand=Dior"       class="tag-btn">Dior</a>
        <a href="?brand=Gucci"      class="tag-btn">Gucci</a>
        <a href="?brand=Prada"      class="tag-btn">Prada</a>
        <a href="?brand=Versace"    class="tag-btn">Versace</a>
    </div>
    <div class="search">
        <form action="index.php" method="GET">
            <input type="search" name="q" placeholder="Search...">
            <button type="submit">Find</button>
        </form>
    </div>
</div>

<main class="main-wrapper">

    <div class="products">
        <div class="product-card">
            <span class="product-category"></span>
            <h3></h3>
            <p class="price"> zl</p>
            <p class="description"></p>
            <p class="product-meta">Size:  | Stock: </p>
            <a href="product.php?id=" class="btn-view">View</a>
        </div>
    </div>

    <aside class="categories">
        <h4>Categories</h4>
        <a href="index.php"           class="category-btn active">All</a>
        <a href="?category=Casual"    class="category-btn">Casual</a>
        <a href="?category=Sport"     class="category-btn">Sport</a>
        <a href="?category=Formal"    class="category-btn">Formal</a>
        <a href="?category=Outdoor"   class="category-btn">Outdoor</a>
        <a href="?category=Party"     class="category-btn">Party</a>
    </aside>

</main>

<footer>
    <p>&copy; 2026 ShoeShop Ostrava</p>
</footer>

</body>
</html>
