<?php
session_start();
require_once __DIR__ . '/functions.php';

// Obsługa koszyka
// ogolne sprawdzenie czy user z metoda POST
// funkcja addToCart z functions.php
// odwolanie przez metode POST




// odwolanie do metody z buttons 
// kazda ma swoje i inna reakcje 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'add') {
        addToCart((int)$_POST['product_id']);
        header("Location: main.php"); exit;
    }
    if (($_POST['action'] ?? '') === 'order') {
        if (placeOrder()) {
            $_SESSION['msg'] = "Zamówienie zostało złożone!";
            // no tutaj mamy zapis do sesji ze udalo sie wszystko 
            echo $_SESSION['msg'];
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
    <title>Koszyk — 👟 ShoeShop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-table {
             width: 100%; 
             border-collapse: collapse; 
             margin-top: 20px; 
             background: #1a1a1a; 
            }






        .cart-table th, .cart-table td { 
            padding: 15px; 
            border: 1px solid #333; 
            text-align: left; 
        }





        .total-box { 
            margin-top: 20px; 
            text-align: right; 
            font-size: 20px;
            color: #ccff00; 
        }
          
        .btn-order {
            background: #ccff00;
            color: #000;
            padding: 10px 20px;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <header class="header">
        <div class="logo"><a href="main.php">👟 ShoeShop</a></div>
        <div class="auth"><a href="main.php">Powrót do sklepu</a></div>
    </header>

    <div class="main-wrapper" style="display:block;">
        <h1>Twój koszyk</h1>
        <?php
        echo "<pre>";

        echo "_SESSION 'cart'";
        print_r($_SESSION["cart"]);
        echo "</pre>";
        ?>

<!-- 
dzialanie skryptu php tego dziala tak ze
w sesji my mamy - 'cart' a w cart mamy juz
znaczenia dodanych rzeczy
t.z. ze przy dodaniu zapisujemy w taki sposub

id(art. z b.d.) => ilosc

-----------------------
_SESSION 'cart'Array
(
    [20] => 1
    [12] => 1
)
------------------------

przez pentle foreach wykonujemy taka logike skryptu
$qty to prawa czesc t.z. ilosc ktora mamy w sesji
$id to lewa czesc tablicy cart w sesji (id z Bazy danych)

UWAGA 
dzialanie $id albo $qty w jakejkolwiek tablicy to zawsze 
jak zapisano w php bogiem jezyka 
$id to klucz (index) - lewa czesc
$qty to jest to co mamy w indeksie


$res - pytanie do BD
$p - rozpakujemy tablice do zmiennej 
$sum - przez to ze mamy ilosc i cene w roznych swiatach to robimy tak ze cena z tablicy mnozymy przez ilosc z sesji 

dla debug ta vizualizacji 
wywoalem przez 
echo <pre>
   ta przez no ten print r (to z php mamy ze fajnie nam po wierszach wypisuje cala tablice) 

po co uzylem endif ta endforeach 
pracujemy w zespolu widzialem uzycie takiego na githabie
przez uzycie tego endif lub endforeach nic jak rozumiem sie nie zmienia 
poprostu dokladnie moge pokazac TEAM ze mamy tam koniec if a tam petli foreach
ale mamy tez roznice bo musimy wypisywac kazdy sbosub tam if else w rozmych tak powiem sktyprach php 


wyglada to tak ze 
++++++++++++++++++++++++++++++++++++
if (czy mamy pusty)
  /else 
       /foreach
       /endforeach
/endif
++++++++++++++++++++++++++++++++++++

-->
        <!-- tutaj mamy takie uzycie jak mowilem w roznych skryptach -->
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
                    echo "<pre>";
                    print_r($p);
                    echo "</pre>";
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