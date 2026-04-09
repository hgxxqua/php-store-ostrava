<?php
session_start();
require_once 'functions.php';

/*
*/

if(!isAdmin()){
    header("Location: main.php");
    exit;
}

if(!is_dir(__DIR__ . '/uploads')){
    mkdir(__DIR__ . '/uploads', 0755, true);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    
    // Obsluga przyciskow z formy
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
    if($action === 'update_status'){
        updateOrderStatus($_POST['order_id'] ?? 0, $_POST['status'] ?? '');
        header("Location: admin.php?page=orders");
        exit;
    }
}

/*
*/
$page        = $_GET['page'] ?? '';
$allProducts = ($page === 'list')   ? getProducts()    : null;
$allOrders   = ($page === 'orders') ? getAdminOrders() : null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — 👟 ShoeShop</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">
            <span>ShoeShop 👟</span>
            <small>Admin · <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></small>
        </div>
        <nav class="admin-nav">
            <a href="admin.php?page=add"    class="<?= $page === 'add'    ? 'active' : '' ?>">＋ Dodaj produkt</a>
            <a href="admin.php?page=list"   class="<?= $page === 'list'   ? 'active' : '' ?>">☰ Lista produktów</a>
            <a href="cabinet.php" class="<?= $page === 'orders' ? 'active' : '' ?>">📦 Zamówienia</a>
            <hr class="nav-divider">
            <a href="main.php">← Wróć do sklepu</a>
            <a href="logout.php" class="nav-danger">⏻ Wyloguj się</a>
        </nav>
    </aside>

    <main class="admin-main <?= $page ? 'has-content' : '' ?>">

        <div class="admin-banner">
            <div class="banner-line"></div>
            <h1>👟 SHOESHOP</h1>
            <p>Wybierz sekcję z lewego menu</p>
            <div class="banner-line"></div>
        </div>

        <div class="admin-content-area">

            <?php if($page === 'add'): ?>
            <div class="section-title">＋ Dodaj produkt</div>
            <form class="add-form" method="POST" action="admin.php" style="max-width: 600px;">
                <input type="hidden" name="action" value="add">

                <div class="field-group">
                    <label>Nazwa</label>
                    <input type="text" name="name" placeholder="Triple S" required>
                </div>
                <div class="field-group">
                    <label>Opis</label>
                    <textarea name="desc" placeholder="Krótki opis..."></textarea>
                </div>
                <div class="field-group">
                    <label>Cena (zł)</label>
                    <input type="number" name="price" placeholder="990" required>
                </div>
                <div class="field-group">
                    <label>Rozmiar</label>
                    <input type="text" name="size" placeholder="42">
                </div>
                <div class="field-group">
                    <label>Marka</label>
                    <input type="text" name="brand" placeholder="Balenciaga">
                </div>
                <div class="field-group">
                    <label>Kategoria</label>
                    <select name="category">
                        <option value="Casual">Casual</option>
                        <option value="Sport">Sport</option>
                        <option value="Formal">Formal</option>
                        <option value="Outdoor">Outdoor</option>
                        <option value="Party">Party</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Stan</label>
                    <input type="number" name="stock" placeholder="10">
                </div>
                <div class="field-group">
                    <label>Ścieżka do zdjęcia</label>
                    <input type="text" name="image_path" placeholder="shoe.jpg">
                </div>

                <button type="submit" class="btn-submit">Dodaj produkt</button>
            </form>

            <?php elseif($page === 'list'): ?>
            <div class="section-title">☰ Lista produktów</div>

            <?php while($p = mysqli_fetch_assoc($allProducts)): ?>
            <div class="product-row">
                <div class="product-row-img">
                    <?php
                        // Logika obrazka: jesli w bazie jest tylko nazwa (np. but.jpg)
                        // to dodajemy folder /uploads/ ========== Jesli pelna sciezka - zostawiamy
                        $imgSrc = !empty($p['image_path'])
                            ? (strpos($p['image_path'], '/') === false ? 'uploads/' . $p['image_path'] : $p['image_path'])
                            : '';
                    ?>
                    <?php if($imgSrc): ?>
                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="">
                    <?php else: ?>
                        Brak zdjęcia
                    <?php endif; ?>
                </div>
                <div class="product-row-info">
                    <div class="product-row-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-row-meta"><?= htmlspecialchars($p['category']) ?> · Rozmiar <?= htmlspecialchars($p['size']) ?> · Stan: <?= $p['stock'] ?></div>
                </div>
                <div class="product-row-price"><?= $p['price'] ?> zł</div>

                <button class="btn-edit" onclick="openEdit(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">Edytuj</button>

                <form method="POST" onsubmit="return confirm('Usunąć «<?= htmlspecialchars($p['name']) ?>»?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn-delete">Usuń</button>
                </form>
            </div>
            <?php endwhile; ?>

            <?php elseif($page === 'orders'): ?>
            <div class="section-title">📦 Zamówienia</div>
            <?php
            // kolory statusow - robimy mape zeby nie pisac 100 razy IF
            // ale ich jeszcze nie mamy w BD
            $statusLabels = [
                'new'        => ['label' => 'Nowe',          'color' => '#4a9eff'],
                'processing' => ['label' => 'W realizacji',  'color' => '#ffaa00'],
                'shipped'    => ['label' => 'Wysłane',        'color' => '#cc88ff'],
                'delivered'  => ['label' => 'Dostarczone',   'color' => '#ccff00'],
                'cancelled'  => ['label' => 'Anulowane',     'color' => '#ff4444'],
            ];

            // IF / ELSE z uzyciem mysqli_num_rows - sprawdzamy czy w ogole ktos cos kupil
            if(mysqli_num_rows($allOrders) === 0):
            ?>
                <p style="color:#666;">Brak zamówień.</p>
            <?php else: ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Klient</th>
                        <th>Produkty</th>
                        <th>Suma</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Zmień status</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($o = mysqli_fetch_assoc($allOrders)): ?>
                    <?php $sl = $statusLabels[$o['status']] ?? ['label' => $o['status'], 'color' => '#888']; ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($o['username']) ?></strong><br>
                            <small style="color:#666"><?= htmlspecialchars($o['email']) ?></small>
                        </td>
                        <td style="color:#aaa;font-size:13px"><?= htmlspecialchars($o['products']) ?></td>
                        <td style="color:#ccff00;font-weight:bold"><?= number_format($o['total'], 0, '.', ' ') ?> zł</td>
                        <td style="color:#666;font-size:13px"><?= htmlspecialchars($o['created_at']) ?></td>
                        <td>
                            <span class="order-status-badge" style="background:<?= $sl['color'] ?>22;color:<?= $sl['color'] ?>;border:1px solid <?= $sl['color'] ?>55">
                                <?= $sl['label'] ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:flex;gap:6px;align-items:center">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <select name="status" class="status-select">
                                    <?php foreach($statusLabels as $val => $info): ?>
                                        <option value="<?= $val ?>" <?= $o['status'] === $val ? 'selected' : '' ?>>
                                            <?= $info['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn-status-save">Zapisz</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; // KONIEC IF (orders) ?>
            <?php endif; // KONIEC IF (pages) ?>

        </div>
    </main>
</div>

<div class="modal-overlay" id="edit-modal">
    <div class="modal-box">
        <div class="modal-title">
            Edytuj produkt
            <button class="modal-close" onclick="closeEdit()">✕</button>
        </div>
        <form class="add-form" method="POST" action="admin.php?page=list">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" id="edit-id">

            <div class="field-group">
                <label>Nazwa</label>
                <input type="text" name="name" id="edit-name" required>
            </div>
            <div class="field-group">
                <label>Opis</label>
                <textarea name="desc" id="edit-desc"></textarea>
            </div>
            <div class="field-group">
                <label>Cena (zł)</label>
                <input type="number" name="price" id="edit-price" required>
            </div>
            <div class="field-group">
                <label>Rozmiar</label>
                <input type="text" name="size" id="edit-size">
            </div>
            <div class="field-group">
                <label>Marka</label>
                <input type="text" name="brand" id="edit-brand">
            </div>
            <div class="field-group">
                <label>Kategoria</label>
                <select name="category" id="edit-cat">
                    <option value="Casual">Casual</option>
                    <option value="Sport">Sport</option>
                    <option value="Formal">Formal</option>
                    <option value="Outdoor">Outdoor</option>
                    <option value="Party">Party</option>
                </select>
            </div>
            <div class="field-group">
                <label>Stan</label>
                <input type="number" name="stock" id="edit-stock">
            </div>
            <div class="field-group">
                <label>Ścieżka do zdjęcia</label>
                <input type="text" name="image_path" id="edit-image">
            </div>

            <button type="submit" class="btn-submit">Zapisz zmiany</button>
        </form>
    </div>
</div>

<script>
/*
*/
function openEdit(p){
    document.getElementById('edit-id').value    = p.id;
    document.getElementById('edit-name').value  = p.name;
    document.getElementById('edit-desc').value  = p.description;
    document.getElementById('edit-price').value = p.price;
    document.getElementById('edit-size').value  = p.size;
    document.getElementById('edit-brand').value = p.brand;
    document.getElementById('edit-stock').value = p.stock;
    document.getElementById('edit-cat').value   = p.category;
    document.getElementById('edit-image').value = p.image_path || '';

    document.getElementById('edit-modal').classList.add('open');
}

function closeEdit(){
    document.getElementById('edit-modal').classList.remove('open');
}

// Zamykanie modala jak klikniemy gdzies obok (w tlo)
document.getElementById('edit-modal').addEventListener('click', function(e){
    if(e.target === this) closeEdit();
});
</script>

</body>
</html>