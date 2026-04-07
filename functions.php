<?php
require_once __DIR__ . '/db.php';

// Autoryzuje użytkownika i uruchamia sesję
function login($username, $password){
    $db = polacz_z_baza();
    $u = mysqli_real_escape_string($db, $username);
    $p = mysqli_real_escape_string($db, $password);
    
    $result = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM users WHERE name = '$u' AND password = '$p' LIMIT 1"));
    if($result){
        $_SESSION["username"] = $result["name"];
        $_SESSION["email"]    = $result["email"];
        $_SESSION["role"]     = $result["role"];
        $_SESSION["login-in"] = true;
        header("Location: main.php");
        exit;
    } else {
        echo "<p style='color:red;text-align:center'>Nieprawidłowy login lub hasło</p>";
    }
}

// Rejestruje nowego użytkownika
function registration($usernamedb, $passworddb, $emaildb){
    $db = polacz_z_baza();
    $u = mysqli_real_escape_string($db, $usernamedb);
    $p = mysqli_real_escape_string($db, $passworddb);
    $e = mysqli_real_escape_string($db, $emaildb);

    if(checklogin($u, $db)){
        $sql = "INSERT INTO users (name, email, password, role, created_at) 
                VALUES ('$u', '$e', '$p', 'user', CURDATE())";
        if(mysqli_query($db, $sql)){
            login($usernamedb, $passworddb);
        } else {
            die("Błąd MySQL: " . mysqli_error($db));
        }
    } else {
        $_SESSION["error"] = "Ten login jest już zajęty";
        header("Location: register.php");
        exit();
    }
}

// Pobiera produkty z filtrowaniem
function getProducts($category = null, $brand = null, $search = null) {
    $db = polacz_z_baza();
    $sql = "SELECT * FROM products WHERE stock > 0";
    
    if ($category) $sql .= " AND category = '" . mysqli_real_escape_string($db, $category) . "'";
    if ($brand)    $sql .= " AND brand = '" . mysqli_real_escape_string($db, $brand) . "'";
    if ($search)   $sql .= " AND (name LIKE '%" . mysqli_real_escape_string($db, $search) . "%' OR description LIKE '%" . mysqli_real_escape_string($db, $search) . "%')";
    
    $sql .= " ORDER BY id DESC";
    return mysqli_query($db, $sql);
}

// Zarządzanie koszykiem (dodawanie)
function addToCart($productId) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
}

// Składanie zamówienia
function placeOrder() {
    if (empty($_SESSION['cart'])) return false;
    
    $db = polacz_z_baza();
    $username = mysqli_real_escape_string($db, $_SESSION['username']);
    $user_res = mysqli_query($db, "SELECT id FROM users WHERE name = '$username'");
    $user = mysqli_fetch_assoc($user_res);
    $userId = $user['id'];
    
    $total = 0;
    $items = [];
    
    foreach ($_SESSION['cart'] as $id => $qty) {
        $res = mysqli_query($db, "SELECT price, stock FROM products WHERE id = $id");
        $p = mysqli_fetch_assoc($res);
        if ($p && $p['stock'] >= $qty) {
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            $items[] = ['id' => $id, 'qty' => $qty, 'price' => $p['price']];
        }
    }
    
    if ($total > 0) {
        mysqli_query($db, "INSERT INTO orders (user_id, total, created_at) VALUES ($userId, $total, CURDATE())");
        $orderId = mysqli_insert_id($db);
        
        foreach ($items as $item) {
            $pid = $item['id'];
            $pqty = $item['qty'];
            $pprice = $item['price'];
            mysqli_query($db, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($orderId, $pid, $pqty, $pprice)");
            mysqli_query($db, "UPDATE products SET stock = stock - $pqty WHERE id = $pid");
        }
        
        unset($_SESSION['cart']);
        return true;
    }
    return false;
}

function isAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function checklogin($username, $db){
    $res = mysqli_query($db, "SELECT id FROM users WHERE name = '$username' LIMIT 1");
    return mysqli_num_rows($res) == 0;
}
?>