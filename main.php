<?php
session_start();
require_once __DIR__ . '/functions.php';

// przez metode GET otrzymujemy filtry 
$current_brand = $_GET['brand'] ?? null;
$current_cat   = $_GET['category'] ?? null;
$search        = $_GET['q'] ?? null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👟 ShoeShop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styl dla aktywnego filtra */

        /* 
         to tak powiem zmienne ktore mozemy wypisac na poczatku i 
         za tym wykorzystac w css 
        */
        :root {
      --bg:        #0f0f11;
      --surface:   #18181c;
      --surface2:  #222228;
      --border:    rgba(255,255,255,0.07);
      --text:      #e8e8ea;
      --muted:     #7a7a85;
      --accent:    #c8f04a;
      --accent2:   #ff6b6b;
      --blue:      #4a9eff;
      --radius:    14px;
    }




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
        
/* KONTENER DLA GUZIKA 
To jest tylko pudelko, zeby guzik mial troche miejsca 
dookola i nie przyklejal sie do innych rzeczy 
*/
.cart-bembem {
    display: inline-block;
    padding: 10px;
}

/* 
Tutaj robimy caly styl:
- robimy flex, zeby tekst i koszyk byly w linii
- dajemy ciemny kolor (#1a1a1a), zeby pasowal do reszty sklepu
- border-radius na 12px, bo takie zaokraglone rogi wygladaja nowoczesnie
- overflow: visible jest MEGA WAZNE, bo inaczej nasz badge (licznik) 
  zniknie, bo on wystaje poza guzik
*/
.cart-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    background: #1a1a1a; 
    color: #ffffff;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    border-radius: 12px;
    border: 1px solid #333;
    transition: all 0.3s ease; /* zeby zmiany koloru i ruchu byly plynne, a nie skakaly */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

/* 
Gdy user najedzie myszka:
- zmieniamy ramke na ten nasz jaskrawy kolor (#ccff00)
- guzik lekko idzie do gory (transform translateY), 
  zeby user widzial, ze to dziala (taki vibe "kliknij mnie")
*/
.cart-btn:hover {
    background: #252525;
    border-color: #ccff00; 
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(204, 255, 0, 0.15);
    color: #ccff00;
}

/* 
To jest to male kolko z numerkiem:
- position: absolute pozwala nam go wyrzucic na gora/prawa strona guzika
- uzywamy tego jaskrawego koloru, zeby od razu walilo po oczach ile mamy rzeczy
- border 2px czarny robi taka "odcinke", zeby sie nie zlewal z guzikiem
*/
.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ccff00; 
    color: #000;
    font-size: 12px;
    font-weight: 800;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 50%; /* robi idealne kolo */
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 10px rgba(204, 255, 0, 0.5);
    border: 2px solid #1a1a1a;
    /* transition z cubic-bezier robi taki fajny efekt "odbicia" jump przy animacji */
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/*
Jak najedziesz na guzik, to to male kolko troche sie powieksza 
i lekko obraca (scale i rotate). Wyglada to pro i zywo.
*/
.cart-btn:hover .cart-badge {
    transform: scale(1.2) rotate(10deg);
}

/* 
Jesli badge nie jest pusty (:not(:empty)), to caly czas plynnie pulsuje.
To taki trik, zeby przypomniec klientowi: "ej, masz cos w koszyku, kup to!"
*/
.cart-badge:not(:empty) {
    animation: badge-pulse 2s infinite;
}

/* 
Poprostu rozszerzamy cien (box-shadow) i go wygaszamy.
*/
@keyframes badge-pulse {
    0% { box-shadow: 0 0 0 0 rgba(204, 255, 0, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(204, 255, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(204, 255, 0, 0); }
}

    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <a href="index.php">👟 ShoeShop</a>
    </div>
    
    <div class="auth">
<!-- 
    
tutaj skrypt do navigation panel ze jezeli masz role admin to masz inne niz zwykly user

-->
        <?php 
        if(isAdmin()){
           echo '<a href="admin.php">Panel administratora</a>';
           echo '<a href="#">|</a>';
        }                                 
        if(isset($_SESSION["login-in"]) && $_SESSION["login-in"] == true){
            echo '<a href="cabinet.php">Moje konto</a>';
            echo '<a href="#">|</a>';
            echo '<a href="logout.php">Wyloguj się</a>';
        } else {
            echo '<a href="login.php">Zaloguj się</a>';
            echo '<a href="#">|</a>';
        } 
        ?>
    </div>
</header>

<div class="filters-wrapper">
    <div class="filters">
        <span class="filter-label">Filtruj według marki:</span>
        
        <a href="?brand=Balenciaga" class="tag-btn <?= ($current_brand == 'Balenciaga') ? 'active' : '' ?>">Balenciaga</a>
        <a href="?brand=Dior"       class="tag-btn <?= ($current_brand == 'Dior') ? 'active' : '' ?>">Dior</a>
        <a href="?brand=Gucci"      class="tag-btn <?= ($current_brand == 'Gucci') ? 'active' : '' ?>">Gucci</a>
        <a href="?brand=Prada"      class="tag-btn <?= ($current_brand == 'Prada') ? 'active' : '' ?>">Prada</a>
        <a href="?brand=Versace"    class="tag-btn <?= ($current_brand == 'Versace') ? 'active' : '' ?>">Versace</a>

        <?php if ($current_brand || $current_cat || $search): ?>
        <!--
            Tutaj mamy oczyscienie filtra 
        -->
            <a href="main.php" class="reset-link">✕ Wyczyść filtry</a>

        <?php endif; ?>
    </div>
    
    <div class="search">
        <form action="main.php" method="GET">
            <input type="search" name="q" placeholder="Szukaj..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit">Szukaj</button>
        </form>
    </div>

    <!--

    NAJPROSTSZY SKRYPT Z CALEGO PROJEKTU
    jezeli nie mamy w sesji cart nic ti nie mamy znacznika obok koszyka 
    jezeli mamy no przez count liczymy i piszemy 

        -->
    <div class="cart-bembem">
    <a class="cart-btn" href="cart.php">
        🛒 Koszyk 
        <?php 
        $count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; 
        if($count > 0): 
        ?>
            <span class="cart-badge"><?= $count ?></span>
        <?php endif; ?>
    </a>
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
        // tutaj otrzymujemy produkty z filtrem albo bez 
        $products_res = getProducts($current_cat, $current_brand, $search);
        
        if (mysqli_num_rows($products_res) > 0):
            // rozdzielamy cala odpowiedz bazy na array za pomocy fetch_assoc 
            // za tym za pomocy perli while wykonujemy vizualizacje tej odpowiedzi 
            while ($p = mysqli_fetch_assoc($products_res)): ?>
                <div class="product-card">
                    <?php
                        // logika obrazka: jesli w bazie jest tylko nazwa bez tego foldera
                        // to dodajemy folder /uploads/ ======== Jesli pelna sciezka - zostawiamy
                        $imgSrc = !empty($p['image_path'])
                            ? (strpos($p['image_path'], '/') === false ? 'uploads/' . $p['image_path'] : $p['image_path'])
                            : 'default.png';
                    ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>" class="image-product" alt="<?= htmlspecialchars($p['name']) ?>">
                    
                    <span class="product-category"><?= htmlspecialchars($p['category']) ?> | <?= htmlspecialchars($p['brand']) ?></span>
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <p class="price"><?= number_format($p['price'], 0, '.', ' ') ?> zl</p>
                    <p class="description"><?= htmlspecialchars($p['description']) ?></p>
                    <p class="product-meta">Rozmiar: <?= htmlspecialchars($p['size']) ?> | Sztuk: <?= $p['stock'] ?></p>
                    
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn-view">Dodaj do koszyka</button>
                    </form>
                </div>
            <?php endwhile;
        else: ?>
            <p class="no-results">Nie znaleziono produktów.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2026 👟 ShoeShop Ostrava</p>
</footer>

</body>
</html>