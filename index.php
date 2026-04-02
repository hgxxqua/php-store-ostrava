<?php
include "product.php";

$activeCategory = '';
$activeTag = '';

if (isset($_GET['category'])) {
    $activeCategory = trim($_GET['category']);
}

if (isset($_GET['tag'])) {
    $activeTag = trim($_GET['tag']);
}

// собираем категории и теги
$categories = [];
$allTags = [];

foreach ($producttst as $product) {

    // категории
    if (!in_array($product['category'], $categories)) {
        $categories[] = $product['category'];
    }

    // теги
    foreach ($product['tags'] as $tag) {
        if (!in_array($tag, $allTags)) {
            $allTags[] = $tag;
        }
    }
}

// фильтрация товаров
$visibleProducts = [];

foreach ($producttst as $id => $product) {

    $show = true;

    if ($activeCategory != '') {
        if ($product['category'] != $activeCategory) {
            $show = false;
        }
    }

    if ($activeTag != '') {
        if (!in_array($activeTag, $product['tags'])) {
            $show = false;
        }
    }

    if ($show) {
        $visibleProducts[$id] = $product;
    }
}
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
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>

    <div class="cart">
        <a href="cart.php">Cart</a>
    </div>

    <div class="auth">
        <a href="login.php">Login</a>
    </div>
</header>

<div class="filters-wrapper">
    <div class="search">
        <form action="index.php" method="GET">

            <?php if ($activeCategory != '') { ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($activeCategory); ?>">
            <?php } ?>

            <input type="search" name="q" placeholder="Search...">
            <button type="submit">Find</button>
        </form>
    </div>

    <div class="filters">
        <span class="filter-label">Filter:</span>

        <?php
        $visibleTags = [];

        if ($activeCategory != '') {

            foreach ($producttst as $p) {
                if ($p['category'] == $activeCategory) {
                    foreach ($p['tags'] as $t) {
                        if (!in_array($t, $visibleTags)) {
                            $visibleTags[] = $t;
                        }
                    }
                }
            }

            $resetHref = '?category=' . urlencode($activeCategory);

        } else {

            $visibleTags = $allTags;
            $resetHref = 'index.php';
        }
        ?>

        <a href="<?php echo $resetHref; ?>" class="tag-btn <?php if ($activeTag == '') echo 'active'; ?>">
            All
        </a>

        <?php foreach ($visibleTags as $tag) { ?>

            <?php
            if ($activeCategory != '') {
                $href = '?category=' . urlencode($activeCategory) . '&tag=' . urlencode($tag);
            } else {
                $href = '?tag=' . urlencode($tag);
            }
            ?>

            <a href="<?php echo $href; ?>" class="tag-btn <?php if ($activeTag == $tag) echo 'active'; ?>">
                <?php echo htmlspecialchars($tag); ?>
            </a>

        <?php } ?>
    </div>
</div>

<main class="main-wrapper">

    <div class="products">

        <?php foreach ($visibleProducts as $id => $product) { ?>

            <div class="product-card">
                <span class="product-category">
                    <?php echo htmlspecialchars($product['category']); ?>
                </span>

                <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                <p class="price">
                    <?php echo htmlspecialchars($product['price']); ?> z&#322;
                </p>

                <p class="description">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <div class="product-tags">
                    <?php foreach ($product['tags'] as $tag) { ?>

                        <a href="?category=<?php echo urlencode($product['category']); ?>&tag=<?php echo urlencode($tag); ?>" class="tag-btn">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>

                    <?php } ?>
                </div>

                <a href="product.php?id=<?php echo $id; ?>" class="btn-view">View</a>
            </div>

        <?php } ?>

        <?php if (count($visibleProducts) == 0) { ?>
            <p class="no-results">No products found.</p>
        <?php } ?>

    </div>

    <aside class="categories">
        <h4>Categories</h4>

        <a href="index.php" class="category-btn <?php if ($activeCategory == '') echo 'active'; ?>">
            All
        </a>

        <?php foreach ($categories as $cat) { ?>

            <a href="?category=<?php echo urlencode($cat); ?>" class="category-btn <?php if ($activeCategory == $cat) echo 'active'; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>

        <?php } ?>
    </aside>

</main>

<footer>
    <p>&copy; 2026 ShoeShop Ostrava</p>
</footer>

</body>
</html>