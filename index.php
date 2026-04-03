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
    <div class="cart">
        <a href="cart.php">Cart</a>
    </div>
    <div class="auth">
        <a href="#">Contact</a>
        <a href="#">|</a>
        <?php if(isAdmin()): ?>
            <a href="admin.php">Admin Panel</a>
            <a href="#">|</a>
        <?php endif; ?>
        <?php if(!empty($_SESSION['login-in'])): ?>
            <span style="color:#ccc"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="#">|</a>
            <a href="index.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</header>

<?php
// Строит URL сохраняя текущие GET параметры, но заменяя указанный
function buildUrl($key, $value){
    $params = $_GET;
    if($value === '') unset($params[$key]);
    else $params[$key] = $value;
    return 'index.php' . ($params ? '?' . http_build_query($params) : '');
}
?>
<div class="filters-wrapper">
    
    <div class="filters">
        <span class="filter-label">Filter by brand:</span>
        <a href="<?= buildUrl('brand','') ?>"             class="tag-btn">All</a>
        <a href="<?= buildUrl('brand','Balenciaga') ?>"   class="tag-btn">Balenciaga</a>
        <a href="<?= buildUrl('brand','Dior') ?>"         class="tag-btn">Dior</a>
        <a href="<?= buildUrl('brand','Gucci') ?>"        class="tag-btn">Gucci</a>
        <a href="<?= buildUrl('brand','Prada') ?>"        class="tag-btn">Prada</a>
        <a href="<?= buildUrl('brand','Versace') ?>"      class="tag-btn">Versace</a>
    </div>
    <div class="search">
        <form action="index.php" method="GET">
            <?php if(!empty($_GET['brand'])): ?>
                <input type="hidden" name="brand" value="<?= htmlspecialchars($_GET['brand']) ?>">
            <?php endif; ?>
            <?php if(!empty($_GET['category'])): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($_GET['category']) ?>">
            <?php endif; ?>
            <input type="search" name="q" placeholder="Search...">
            <button type="submit">Find</button>
        </form>
    </div>
    <!-- Кнопка сброса всех фильтров — показывается только если есть активный фильтр -->
    <?php if(!empty($_GET['brand']) || !empty($_GET['category']) || !empty($_GET['q'])): ?>
        <a href="index.php" class="tag-btn" style="color:red;">✕ Reset</a>
    <?php endif; ?>
</div>

<main class="main-wrapper">

    <div class="products">
        <?php $products = getFilteredProducts(); while($p = mysqli_fetch_assoc($products)): ?>
        <a href="product.php?id=<?= $p['id'] ?>" class="product-card">
            <?php if(!empty($p['image']) && file_exists(__DIR__.'/uploads/'.$p['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="product-card-img" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php endif; ?>
            <span class="product-category"><?= htmlspecialchars($p['category']) ?></span>
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p class="price"><?= $p['price'] ?> zl</p>
            <p class="description"><?= htmlspecialchars($p['description']) ?></p>
            <p class="product-meta">Size: <?= htmlspecialchars($p['size']) ?> | Stock: <?= $p['stock'] ?></p>
        </a>
        <?php endwhile; ?>
    </div>

    <aside class="categories">
        <h4>Categories</h4>
        <a href="<?= buildUrl('category','') ?>"            class="category-btn">All</a>
        <a href="<?= buildUrl('category','Casual') ?>"      class="category-btn">Casual</a>
        <a href="<?= buildUrl('category','Sport') ?>"       class="category-btn">Sport</a>
        <a href="<?= buildUrl('category','Formal') ?>"      class="category-btn">Formal</a>
        <a href="<?= buildUrl('category','Outdoor') ?>"     class="category-btn">Outdoor</a>
        <a href="<?= buildUrl('category','Party') ?>"       class="category-btn">Party</a>
    </aside>

</main>

<footer>
    <p>&copy; 2026 ShoeShop Ostrava</p>
</footer>

</body>
</html>
