<?php
session_start();
require_once __DIR__ . '/db.php';

$conn = polacz_z_baza();

/* --- Проверка администратора --- */
function isAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

if(!isAdmin()){
    header("Location: index.php");
    exit;
}

/* --- Добавление товара --- */
function addProduct($conn){

    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['desc'] ?? '');
    $price = (int)($_POST['price'] ?? 0);
    $size = trim($_POST['size'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);

    if(!$name || $price <= 0){
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO products (name,description,price,size,brand,category,stock)
        VALUES (?,?,?,?,?,?,?)
    ");

    $stmt->bind_param("ssisssi",$name,$desc,$price,$size,$brand,$category,$stock);
    $stmt->execute();
}

/* --- Удаление товара --- */
function deleteProduct($conn){

    $id = (int)($_POST['product_id'] ?? 0);

    if($id <= 0) return;

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

/* --- POST обработка --- */
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    if(($_POST['action'] ?? '') === 'add'){
        addProduct($conn);
        header("Location: admin.php");
        exit;
    }

    if(($_POST['action'] ?? '') === 'delete'){
        deleteProduct($conn);
        header("Location: admin.php");
        exit;
    }
}

/* --- Получение товаров --- */
$res_products = mysqli_query($conn,"SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel — ShoeShop</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;600;700&family=Onest:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
--bg:#0f0f11;
--surface:#18181c;
--surface2:#222228;
--border:rgba(255,255,255,0.07);
--text:#e8e8ea;
--muted:#7a7a85;
--pink:#ff2d78;
--pink-dim:rgba(255,45,120,0.12);
--accent:#c8f04a;
--radius:14px;
}

body{
font-family:'Onest',sans-serif;
background:var(--bg);
color:var(--text);
min-height:100vh;
}

nav{
display:flex;
align-items:center;
padding:0 28px;
height:58px;
border-bottom:1px solid var(--border);
background:rgba(15,15,17,0.95);
backdrop-filter:blur(10px);
gap:24px;
}

.logo{
font-family:'Unbounded',sans-serif;
font-size:16px;
font-weight:700;
color:var(--accent);
text-decoration:none;
display:flex;
align-items:center;
gap:7px;
}

.logo span{color:var(--text);font-weight:400}

.page{
max-width:1060px;
margin:28px auto;
padding:0 24px;
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
}

.card{
background:var(--surface);
border:1px solid var(--border);
border-radius:var(--radius);
padding:24px;
}

.card-title{
font-size:15px;
font-weight:600;
margin-bottom:20px;
color:var(--pink);
}

input,select,textarea{
background:var(--surface2);
border:1px solid var(--border);
color:var(--text);
font-family:'Onest';
font-size:14px;
padding:10px 13px;
border-radius:9px;
width:100%;
margin-bottom:12px;
}

.btn-add{
width:100%;
padding:13px;
background:var(--pink);
color:#fff;
font-family:'Unbounded';
font-size:13px;
font-weight:600;
border:none;
border-radius:10px;
cursor:pointer;
}

.product-row{
display:flex;
align-items:center;
gap:10px;
padding:13px 0;
border-bottom:1px solid var(--border);
}

.product-name{flex:1}

.product-price{
font-weight:600;
color:var(--accent);
}

.btn-delete{
background:transparent;
border:1px solid rgba(255,45,120,0.35);
color:var(--pink);
padding:5px 12px;
border-radius:7px;
cursor:pointer;
}
</style>
</head>

<body>

<nav>
<a href="index.php" class="logo">👟 <span>Shoe</span>Shop</a>
</nav>

<div class="page">

<div class="card">

<div class="card-title">+ Добавить товар</div>

<form method="POST">

<input type="hidden" name="action" value="add">

<input type="text" name="name" placeholder="Название" required>

<textarea name="desc" placeholder="Описание"></textarea>

<input type="number" name="price" placeholder="Цена" required>

<input type="text" name="size" placeholder="Размер">

<input type="text" name="brand" placeholder="Бренд">

<select name="category">
<option value="Casual">Casual</option>
<option value="Sport">Sport</option>
<option value="Formal">Formal</option>
<option value="Outdoor">Outdoor</option>
</select>

<input type="number" name="stock" placeholder="Остаток">

<button class="btn-add">Добавить товар</button>

</form>

</div>

<div class="card">

<div class="card-title">Все товары</div>

<?php while($p = mysqli_fetch_assoc($res_products)): ?>

<div class="product-row">

<span class="product-name"><?=htmlspecialchars($p['name'])?></span>

<span class="product-price"><?=$p['price']?> ₽</span>

<form method="POST">

<input type="hidden" name="action" value="delete">
<input type="hidden" name="product_id" value="<?=$p['id']?>">

<button class="btn-delete">удалить</button>

</form>

</div>

<?php endwhile; ?>

</div>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>