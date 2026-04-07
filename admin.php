<?php
session_start();
require_once 'functions.php';

// Не-админов отправляем на главную
if(!isAdmin()){
    header("Location: index.php");
    exit;
}

// Создаём папку uploads если не существует
if(!is_dir(__DIR__ . '/uploads')){
    mkdir(__DIR__ . '/uploads', 0755, true);
}

// Обработка POST действий
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    if($action === 'add'){
        addProduct();
        header("Location: admin.php?page=list");
        exit;
    }
    if($action === 'delete'){
        deleteProduct($_POST['product_id'] ?? 0);
        header("Location: admin.php?page=list");
        exit;
    }
    if($action === 'update'){
        updateProduct($_POST['product_id'] ?? 0);
        header("Location: admin.php?page=list");
        exit;
    }
}

$page        = $_GET['page'] ?? '';
$allProducts = ($page === 'list') ? getAllProducts() : null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — ShoeShop</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="admin-wrapper">

    <!-- ========== SIDEBAR ========== -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">
            <span>ShoeShop</span>
            <small>Admin · <?= htmlspecialchars($_SESSION['username']) ?></small>
        </div>
        <nav class="admin-nav">
            <a href="admin.php?page=add"  class="<?= $page === 'add'  ? 'active' : '' ?>">＋ Add Product</a>
            <a href="admin.php?page=list" class="<?= $page === 'list' ? 'active' : '' ?>">☰ Products List</a>
            <hr class="nav-divider">
            <a href="index.php">← Back to Shop</a>
            <a href="logout.php" class="nav-danger">⏻ Logout</a>
        </nav>
    </aside>

    <!-- ========== MAIN ========== -->
    <main class="admin-main <?= $page ? 'has-content' : '' ?>">

        <!-- Welcome banner (затемняется когда выбран раздел) -->
        <div class="admin-banner">
            <div class="banner-line"></div>
            <h1>SHOESHOP</h1>
            <p>Select a section from the left menu</p>
            <div class="banner-line"></div>
        </div>

        <!-- Рабочая область -->
        <div class="admin-content-area">

            <?php if($page === 'add'): ?>
            <!-- ===== ДОБАВЛЕНИЕ ТОВАРА ===== -->
            <div class="section-title">＋ Add Product</div>
            <div class="add-layout">

                <form class="add-form" method="POST" action="admin.php">
                    <input type="hidden" name="action" value="add">

                    <div class="field-group">
                        <label>Name</label>
                        <input type="text" name="name" id="prev-name" placeholder="Triple S" required oninput="updatePreview()">
                    </div>
                    <div class="field-group">
                        <label>Description</label>
                        <textarea name="desc" placeholder="Short description..."></textarea>
                    </div>
                    <div class="field-group">
                        <label>Price (zl)</label>
                        <input type="number" name="price" id="prev-price" placeholder="990" required oninput="updatePreview()">
                    </div>
                    <div class="field-group">
                        <label>Size</label>
                        <input type="text" name="size" id="prev-size" placeholder="42" oninput="updatePreview()">
                    </div>
                    <div class="field-group">
                        <label>Brand</label>
                        <input type="text" name="brand" placeholder="Balenciaga">
                    </div>
                    <div class="field-group">
                        <label>Category</label>
                        <select name="category" id="prev-cat" onchange="updatePreview()">
                            <option value="Casual">Casual</option>
                            <option value="Sport">Sport</option>
                            <option value="Formal">Formal</option>
                            <option value="Outdoor">Outdoor</option>
                            <option value="Party">Party</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="prev-stock" placeholder="10" oninput="updatePreview()">
                    </div>

                    <div class="field-group">
                        <label>Image path</label>
                        <input type="text" name="image" id="prev-image" placeholder="shoe.jpg" oninput="updatePreviewImage('card-img','card-img-ph',this.value)">
                    </div>

                    <button type="submit" class="btn-submit">Add Product</button>
                </form>

                <!-- Предпросмотр карточки товара -->
                <div class="preview-wrap">
                    <div class="preview-label">Preview</div>
                    <div class="preview-card">
                        <div class="preview-img">
                            <img id="card-img" alt="">
                            <span id="card-img-ph">No photo</span>
                        </div>
                        <div class="preview-body">
                            <div class="preview-cat"  id="card-cat">Casual</div>
                            <div class="preview-name" id="card-name">Product name</div>
                            <div class="preview-price" id="card-price">— zl</div>
                            <div class="preview-meta"  id="card-meta">Size: — | Stock: —</div>
                        </div>
                    </div>
                </div>

            </div>

            <?php elseif($page === 'list'): ?>
            <!-- ===== СПИСОК ТОВАРОВ ===== -->
            <div class="section-title">☰ Products List</div>

            <?php while($p = mysqli_fetch_assoc($allProducts)): ?>
            <div class="product-row">
                <div class="product-row-img">
                    <?php
                        $imgSrc = !empty($p['image'])
                            ? (strpos($p['image'], '/') === false ? 'uploads/' . $p['image'] : $p['image'])
                            : '';
                    ?>
                    <?php if($imgSrc): ?>
                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="">
                    <?php else: ?>
                        No img
                    <?php endif; ?>
                </div>
                <div class="product-row-info">
                    <div class="product-row-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-row-meta"><?= htmlspecialchars($p['category']) ?> · Size <?= htmlspecialchars($p['size']) ?> · Stock <?= $p['stock'] ?></div>
                </div>
                <div class="product-row-price"><?= $p['price'] ?> zl</div>

                <!-- Кнопка редактирования — открывает модал с заполненными полями -->
                <button class="btn-edit" onclick="openEdit(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">Edit</button>

                <!-- Кнопка удаления — требует подтверждения -->
                <form method="POST" onsubmit="return confirm('Delete «<?= htmlspecialchars($p['name']) ?>»? This cannot be undone.')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn-delete">Delete</button>
                </form>
            </div>
            <?php endwhile; ?>

            <?php endif; ?>

        </div><!-- /admin-content-area -->

    </main>
</div>

<!-- ========== EDIT MODAL ========== -->
<div class="modal-overlay" id="edit-modal">
    <div class="modal-box">
        <div class="modal-title">
            Edit Product
            <button class="modal-close" onclick="closeEdit()">✕</button>
        </div>
        <div class="add-layout">

            <form class="add-form" method="POST" action="admin.php?page=list">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" id="edit-id">

                <div class="field-group">
                    <label>Name</label>
                    <input type="text" name="name" id="edit-name" required oninput="updateEditPreview()">
                </div>
                <div class="field-group">
                    <label>Description</label>
                    <textarea name="desc" id="edit-desc"></textarea>
                </div>
                <div class="field-group">
                    <label>Price (zl)</label>
                    <input type="number" name="price" id="edit-price" required oninput="updateEditPreview()">
                </div>
                <div class="field-group">
                    <label>Size</label>
                    <input type="text" name="size" id="edit-size" oninput="updateEditPreview()">
                </div>
                <div class="field-group">
                    <label>Brand</label>
                    <input type="text" name="brand" id="edit-brand">
                </div>
                <div class="field-group">
                    <label>Category</label>
                    <select name="category" id="edit-cat" onchange="updateEditPreview()">
                        <option value="Casual">Casual</option>
                        <option value="Sport">Sport</option>
                        <option value="Formal">Formal</option>
                        <option value="Outdoor">Outdoor</option>
                        <option value="Party">Party</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Stock</label>
                    <input type="number" name="stock" id="edit-stock" oninput="updateEditPreview()">
                </div>

                <div class="field-group">
                    <label>Image path</label>
                    <input type="text" name="image" id="edit-image" placeholder="shoe.jpg" oninput="updatePreviewImage('edit-card-img','edit-card-img-ph',this.value)">
                </div>

                <button type="submit" class="btn-submit">Save Changes</button>
            </form>

            <!-- Предпросмотр редактируемого товара -->
            <div class="preview-wrap">
                <div class="preview-label">Preview</div>
                <div class="preview-card">
                    <div class="preview-img">
                        <img id="edit-card-img" alt="">
                        <span id="edit-card-img-ph">No photo</span>
                    </div>
                    <div class="preview-body">
                        <div class="preview-cat"   id="edit-card-cat">Casual</div>
                        <div class="preview-name"  id="edit-card-name">Product name</div>
                        <div class="preview-price" id="edit-card-price">— zl</div>
                        <div class="preview-meta"  id="edit-card-meta">Size: — | Stock: —</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// === Обновляет предпросмотр карточки в форме добавления ===
function updatePreview(){
    document.getElementById('card-name').textContent  = document.getElementById('prev-name').value  || 'Product name';
    document.getElementById('card-price').textContent = (document.getElementById('prev-price').value || '—') + ' zl';
    document.getElementById('card-cat').textContent   = document.getElementById('prev-cat').value;
    document.getElementById('card-meta').textContent  = 'Size: ' + (document.getElementById('prev-size').value  || '—')
                                                      + ' | Stock: ' + (document.getElementById('prev-stock').value || '—');
}

// === Обновляет фото предпросмотра по введённому пути ===
function updatePreviewImage(cardImgId, phId, path){
    const cardImg = document.getElementById(cardImgId);
    const ph      = document.getElementById(phId);
    if(path){
        const src = path.includes('/') ? path : 'uploads/' + path;
        cardImg.src = src;
        cardImg.style.display = 'block';
        if(ph) ph.style.display = 'none';
    } else {
        cardImg.src = '';
        cardImg.style.display = 'none';
        if(ph) ph.style.display = '';
    }
}

// === Открывает модал редактирования и заполняет поля данными товара ===
function openEdit(p){
    document.getElementById('edit-id').value    = p.id;
    document.getElementById('edit-name').value  = p.name;
    document.getElementById('edit-desc').value  = p.description;
    document.getElementById('edit-price').value = p.price;
    document.getElementById('edit-size').value  = p.size;
    document.getElementById('edit-brand').value = p.brand;
    document.getElementById('edit-stock').value = p.stock;
    document.getElementById('edit-cat').value   = p.category;

    document.getElementById('edit-image').value = p.image || '';

    // Показываем текущее фото товара если есть
    updatePreviewImage('edit-card-img','edit-card-img-ph', p.image || '');

    updateEditPreview();
    document.getElementById('edit-modal').classList.add('open');
}

function closeEdit(){
    document.getElementById('edit-modal').classList.remove('open');
}

// === Обновляет предпросмотр карточки в модале редактирования ===
function updateEditPreview(){
    document.getElementById('edit-card-name').textContent  = document.getElementById('edit-name').value  || 'Product name';
    document.getElementById('edit-card-price').textContent = (document.getElementById('edit-price').value || '—') + ' zl';
    document.getElementById('edit-card-cat').textContent   = document.getElementById('edit-cat').value;
    document.getElementById('edit-card-meta').textContent  = 'Size: ' + (document.getElementById('edit-size').value  || '—')
                                                           + ' | Stock: ' + (document.getElementById('edit-stock').value || '—');
}

// Закрытие модала кликом на фон
document.getElementById('edit-modal').addEventListener('click', function(e){
    if(e.target === this) closeEdit();
});
</script>

</body>
</html>
