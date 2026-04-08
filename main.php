<?php
session_start();
require_once __DIR__ . '/functions.php';

// Pobieramy aktualne filtry z adresu URL
$current_brand = $_GET['brand'] ?? null;
$current_cat   = $_GET['category'] ?? null;
$search        = $_GET['q'] ?? null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeShop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styl dla aktywnego filtra */
        .tag-btn.active {
            background-color: #ccff00;
            color: #000;
            border-color: #ccff00;
            font-weight: bold;
        }
        .reset-link {
            color: #ff4444;
            text-decoration: none;
            font-size: 13px;
            margin-left: 10px;
            font-weight: bold;
        }
        .reset-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <a href="main.php">ShoeShop</a>
    </div>
    
    <div class="auth">
        <a href="#">Contact</a>
        <a href="#">|</a>
        <?php 
        if(isAdmin()){
           echo '<a href="admin.php">Admin Panel</a>';
           echo '<a href="#">|</a>';
        }                                 
        if(isset($_SESSION["login-in"]) && $_SESSION["login-in"] == true){
            echo '<a href="cabinet.php">Cabinet</a>';
            echo '<a href="#">|</a>';
            echo '<a href="logout.php">Logout</a>';
            echo '<a href="#">|</a>';
        } else {
            echo '<a href="login.php">Login</a>';
            echo '<a href="#">|</a>';
        } 
        ?>
    </div>
</header>

<div class="filters-wrapper">
    <div class="filters">
        <span class="filter-label">Filter by brand:</span>
        
        <a href="?brand=Balenciaga" class="tag-btn <?= ($current_brand == 'Balenciaga') ? 'active' : '' ?>">Balenciaga</a>
        <a href="?brand=Dior"       class="tag-btn <?= ($current_brand == 'Dior') ? 'active' : '' ?>">Dior</a>
        <a href="?brand=Gucci"      class="tag-btn <?= ($current_brand == 'Gucci') ? 'active' : '' ?>">Gucci</a>
        <a href="?brand=Prada"      class="tag-btn <?= ($current_brand == 'Prada') ? 'active' : '' ?>">Prada</a>
        <a href="?brand=Versace"    class="tag-btn <?= ($current_brand == 'Versace') ? 'active' : '' ?>">Versace</a>

        <?php if ($current_brand || $current_cat || $search): ?>
            <a href="main.php" class="reset-link">✕ Wyczyść filtry</a>
        <?php endif; ?>
    </div>
    
    <div class="search">
        <form action="main.php" method="GET">
            <input type="search" name="q" placeholder="Search..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit">Find</button>
        </form>
    </div>
</div>

<main class="main-wrapper">
    <aside class="categories" style="width: 200px;">
        <h4>Kategorie</h4>
        <a href="main.php" class="category-btn <?= (!$current_cat) ? 'active' : '' ?>">Wszystkie</a>
        <a href="?category=Casual" class="category-btn <?= ($current_cat == 'Casual') ? 'active' : '' ?>">Casual</a>
        <a href="?category=Sport" class="category-btn <?= ($current_cat == 'Sport') ? 'active' : '' ?>">Sport</a>
        <a href="?category=Formal" class="category-btn <?= ($current_cat == 'Formal') ? 'active' : '' ?>">Formal</a>
        <a href="?category=Outdoor" class="category-btn <?= ($current_cat == 'Outdoor') ? 'active' : '' ?>">Outdoor</a>
    </aside>

    <div class="products">
        <?php
        // Вызов функции получения продуктов с текущими фильтрами
        $products_res = getProducts($current_cat, $current_brand, $search);
        
        if (mysqli_num_rows($products_res) > 0):
            while ($p = mysqli_fetch_assoc($products_res)): ?>
                <div class="product-card">
                    <span class="product-category"><?= htmlspecialchars($p['category']) ?> | <?= htmlspecialchars($p['brand']) ?></span>
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <p class="price"><?= number_format($p['price'], 0, '.', ' ') ?> zl</p>
                    <p class="description"><?= htmlspecialchars($p['description']) ?></p>
                    <p class="product-meta">Rozmiar: <?= htmlspecialchars($p['size']) ?> | Sztuk: <?= $p['stock'] ?></p>
                    
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn-view" style="border:none; cursor:pointer; width:100%;">Dodaj do koszyka</button>
                    </form>
                </div>
            <?php endwhile;
        else: ?>
            <p class="no-results">Nie znaleziono produktów.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2026 ShoeShop Ostrava</p>
</footer>

</body>
</html>