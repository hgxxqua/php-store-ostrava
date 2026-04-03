<?php
require_once __DIR__ . '/db.php';


// TODO: авторизация пользователя
function login($username, $password){
    // stub
}

// TODO: выход пользователя из сессии
function logout(){
    // stub
}

// Регистрирует нового пользователя и создаёт сессию; выводит сообщение если логин занят
function registration($usernamedb, $passworddb, $emaildb){
    $db = polacz_z_baza();

    // Защита от спецсимволов
    $u = mysqli_real_escape_string($db, $usernamedb);
    $p = mysqli_real_escape_string($db, $passworddb);
    $e = mysqli_real_escape_string($db, $emaildb);

    $is_free = checklogin($u, $db);

    if($is_free){
        // ВАЖНО: Добавил created_at и CURDATE(), иначе база отклоняет запрос
        $sql = "INSERT INTO users (name, email, password, role, created_at) 
                VALUES ('$u', '$e', '$p', 'user', CURDATE())";
        
        $res = mysqli_query($db, $sql);

        if($res){
            $_SESSION["username"] = $usernamedb;
            $_SESSION["email"] = $emaildb;
            $_SESSION["role"] = "user";
            $_SESSION["login-in"] = "true";
            header("Location: index.php");
            exit();    
        } else {
            // Если снова не сработает, эта строка покажет реальную причину
            die("Ошибка MySQL: " . mysqli_error($db));
        }
    } else {
        $_SESSION["error"] = "Ten login jest już zajęty";
        header("Location: register.php");
        exit();
    }
}

// Проверяет является ли текущий пользователь сессии администратором
// Возвращает true если role === 'admin', иначе false
function isAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Проверяет существует ли хотя бы один администратор в базе данных
// Используется чтобы показать кнопку создания первого админа если его ещё нет
function adminExists(){
    $db = polacz_z_baza();
    $result = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS count FROM users WHERE role = 'admin'"));



    return $result['count'] > 0;
}

// Возвращает товары с фильтром по brand, category или search (берёт из $_GET)
function getFilteredProducts(){
    $db = polacz_z_baza();
    $sql = "SELECT * FROM products";

    $where = [];

    if(!empty($_GET['brand'])){
        $v = mysqli_real_escape_string($db, $_GET['brand']);
        $where[] = "brand = '$v'";
    }
    if(!empty($_GET['category'])){
        $v = mysqli_real_escape_string($db, $_GET['category']);
        $where[] = "category = '$v'";
    }
    if(!empty($_GET['q'])){
        $v = mysqli_real_escape_string($db, $_GET['q']);
        $where[] = "name LIKE '%$v%'";
    }

    if($where) $sql .= " WHERE " . implode(" AND ", $where);

    $sql .= " ORDER BY id DESC";
    return mysqli_query($db, $sql);
}

// Возвращает один товар по id; если не найден — возвращает null
function getProductById($id){
    $db = polacz_z_baza();
    $id = (int)$id;
    return mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM products WHERE id = $id LIMIT 1"));
}

// =============================================
// ФУНКЦИИ АДМИН ПАНЕЛИ
// =============================================

// Добавляет новый товар в БД; сохраняет фото в uploads/ если загружено (только JPG/PNG)
// Требует колонку: ALTER TABLE products ADD COLUMN image varchar(255) DEFAULT '';
function addProduct(){
    $db       = polacz_z_baza();
    $name     = $_POST['name'];
    $desc     = $_POST['desc'];
    $price    = (int)$_POST['price'];
    $size     = $_POST['size'];
    $brand    = $_POST['brand'];
    $category = $_POST['category'];
    $stock    = (int)$_POST['stock'];
    if(!$name || $price <= 0) return;
    $name = mysqli_real_escape_string($db, $name);
    $desc = mysqli_real_escape_string($db, $desc);
    $size = mysqli_real_escape_string($db, $size);
    $brand = mysqli_real_escape_string($db, $brand);
    $category = mysqli_real_escape_string($db, $category);
    $image = '';
    if(!empty($_FILES['image']['name'])){
        $allowed = ['image/jpeg','image/png'];
        $mime    = mime_content_type($_FILES['image']['tmp_name']);
        if(in_array($mime, $allowed)){
            $ext   = $mime === 'image/png' ? 'png' : 'jpg';
            $image = uniqid('prod_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $image);
        }
    }
    $image = mysqli_real_escape_string($db, $image);
    mysqli_query($db, "INSERT INTO products (name,description,price,size,brand,category,stock,image)
        VALUES ('$name','$desc',$price,'$size','$brand','$category',$stock,'$image')");
}

// Обновляет данные товара по id; заменяет фото если загружено новое
function updateProduct($id){
    $db       = polacz_z_baza();
    $id       = (int)$id;
    $name     = $_POST['name'];
    $desc     = $_POST['desc'];
    $price    = (int)$_POST['price'];
    $size     = $_POST['size'];
    $brand    = $_POST['brand'];
    $category = $_POST['category'];
    $stock    = (int)$_POST['stock'];
    $name = mysqli_real_escape_string($db, $name);
    $desc = mysqli_real_escape_string($db, $desc);
    $size = mysqli_real_escape_string($db, $size);
    $brand = mysqli_real_escape_string($db, $brand);
    $category = mysqli_real_escape_string($db, $category);
    $imageSQL = '';
    if(!empty($_FILES['image']['name'])){
        $allowed = ['image/jpeg','image/png'];
        $mime    = mime_content_type($_FILES['image']['tmp_name']);
        if(in_array($mime, $allowed)){
            $ext      = $mime === 'image/png' ? 'png' : 'jpg';
            $filename = uniqid('prod_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $filename);
            $filename = mysqli_real_escape_string($db, $filename);
            $imageSQL = ", image='$filename'";
        }
    }
    mysqli_query($db, "UPDATE products SET
        name='$name', description='$desc', price=$price,
        size='$size', brand='$brand', category='$category', stock=$stock $imageSQL
        WHERE id=$id");
}

// Удаляет товар из БД по id
function deleteProduct($id){
    $db = polacz_z_baza();
    $id = (int)$id;
    mysqli_query($db, "DELETE FROM products WHERE id = $id");
}

// Возвращает все товары из БД для списка в админке
function getAllProducts(){
    $db = polacz_z_baza();
    return mysqli_query($db, "SELECT * FROM products ORDER BY id DESC");
}

// Проверяет свободен ли логин: возвращает true если не занят, false если уже существует
function checklogin($username, $db){
    $u = mysqli_real_escape_string($db, $username);
    $res = mysqli_query($db, "SELECT id FROM users WHERE name = '$u' LIMIT 1");
    
    if(mysqli_num_rows($res) > 0){
        return false; // Занят
    }
    return true; // Свободен
}


function logout(){
    $_SESSION = array(); // Очищаем массив сессии
    session_destroy();   // Удаляем файл сессии на сервере
    return "Wylogowano pomyslnie";
}
?>