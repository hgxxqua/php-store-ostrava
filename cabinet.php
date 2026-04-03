<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

require_once __DIR__ . '/db.php';
$conn = polacz_z_baza();

// Берём пользователя из БД
$id   = (int)$_SESSION['user_id'];
$res  = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($res);

if (!$user) {
    session_destroy();
    header('Location: login.php'); exit;
}

// Берём заказы пользователя с товарами
$res_orders = mysqli_query($conn, "
    SELECT
        o.id,
        o.total,
        o.created_at,
        GROUP_CONCAT(p.name SEPARATOR ', ') AS products
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN products p     ON p.id = oi.product_id
    WHERE o.user_id = $id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");

// Инициалы для аватара
$words    = explode(' ', $user['name']);
$initials = mb_strtoupper(
    mb_substr($words[0], 0, 1) .
    (isset($words[1]) ? mb_substr($words[1], 0, 1) : '')
);

// Форматирование даты на русском
function formatDateRu(string $date): string {
    $months = ['','января','февраля','марта','апреля','мая','июня',
               'июля','августа','сентября','октября','ноября','декабря'];
    $d = date_create($date);
    return date_format($d, 'j') . ' ' . $months[(int)date_format($d, 'n')] . ' ' . date_format($d, 'Y');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Личный кабинет — ShoeShop</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;600;700&family=Onest:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

    body {
      font-family: 'Onest', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    nav {
      display: flex;
      align-items: center;
      padding: 0 32px;
      height: 60px;
      border-bottom: 1px solid var(--border);
      background: rgba(15,15,17,0.9);
      backdrop-filter: blur(10px);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .logo {
      font-family: 'Unbounded', sans-serif;
      font-size: 17px;
      font-weight: 700;
      color: var(--accent);
      letter-spacing: -0.5px;
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }
    .logo span { color: var(--text); font-weight: 400; }

    .nav-links { display: flex; gap: 6px; margin-left: 32px; }
    .nav-links a {
      color: var(--muted);
      text-decoration: none;
      font-size: 14px;
      padding: 6px 12px;
      border-radius: 8px;
      transition: all .2s;
    }
    .nav-links a:hover { color: var(--text); background: var(--surface2); }

    .nav-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }

    .cart-btn {
      display: flex;
      align-items: center;
      gap: 7px;
      color: var(--muted);
      font-size: 14px;
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 8px;
      transition: all .2s;
    }
    .cart-btn:hover { color: var(--text); background: var(--surface2); }

    .cart-badge {
      background: var(--accent2);
      color: #fff;
      font-size: 11px;
      font-weight: 600;
      width: 20px; height: 20px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
    }

    .greeting { color: var(--accent); font-size: 14px; font-weight: 500; }

    .btn-logout {
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--muted);
      font-family: 'Onest', sans-serif;
      font-size: 13px;
      padding: 6px 14px;
      border-radius: 8px;
      cursor: pointer;
      transition: all .2s;
      text-decoration: none;
    }
    .btn-logout:hover { color: var(--text); border-color: rgba(255,255,255,0.15); }

    .page {
      max-width: 1060px;
      margin: 0 auto;
      padding: 36px 24px;
      display: grid;
      grid-template-columns: 220px 1fr;
      gap: 20px;
      align-items: start;
    }

    .sidebar {
      display: flex;
      flex-direction: column;
      gap: 8px;
      position: sticky;
      top: 80px;
    }

    .profile-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 24px 16px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      margin-bottom: 4px;
    }

    .avatar {
      width: 68px; height: 68px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1e3a5f, #2d6a4f);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Unbounded', sans-serif;
      font-size: 20px;
      font-weight: 600;
      color: #7ecfb3;
      border: 2px solid rgba(126,207,179,0.25);
    }

    .profile-name { font-size: 16px; font-weight: 600; margin-top: 4px; }
    .profile-email { font-size: 13px; color: var(--muted); }

    .role-badge {
      background: rgba(200,240,74,0.12);
      color: var(--accent);
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 20px;
      border: 1px solid rgba(200,240,74,0.2);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .sidebar-menu { display: flex; flex-direction: column; gap: 2px; }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      border-radius: 10px;
      font-size: 14px;
      color: var(--muted);
      text-decoration: none;
      transition: all .2s;
      border: 1px solid transparent;
    }
    .menu-item:hover { color: var(--text); background: var(--surface2); }
    .menu-item.active {
      background: var(--surface2);
      color: var(--text);
      border-color: var(--border);
    }
    .menu-item .icon { font-size: 16px; width: 20px; text-align: center; }

    .content { display: flex; flex-direction: column; gap: 12px; }

    .section-title {
      font-family: 'Unbounded', sans-serif;
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 4px;
      color: var(--text);
    }

    .order-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px 24px;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 6px 16px;
      transition: border-color .2s, transform .15s;
    }
    .order-card:hover {
      border-color: rgba(255,255,255,0.13);
      transform: translateY(-1px);
    }

    .order-header {
      display: flex;
      align-items: center;
      gap: 12px;
      grid-column: 1;
    }

    .order-id { font-weight: 600; font-size: 15px; }
    .order-date { font-size: 13px; color: var(--muted); margin-left: auto; }
    .order-product { font-size: 14px; color: var(--muted); grid-column: 1; }
    .order-total { font-weight: 600; font-size: 15px; grid-column: 1; }

    .order-status {
      grid-column: 2;
      grid-row: 1 / 4;
      display: flex;
      align-items: center;
    }

    .status-badge {
      font-size: 12px;
      font-weight: 500;
      padding: 5px 12px;
      border-radius: 20px;
      white-space: nowrap;
    }
    .status-badge.delivered {
      background: rgba(200,240,74,0.1);
      color: var(--accent);
      border: 1px solid rgba(200,240,74,0.2);
    }

    .empty-orders {
      background: var(--surface);
      border: 1px dashed var(--border);
      border-radius: var(--radius);
      padding: 48px;
      text-align: center;
      color: var(--muted);
      font-size: 15px;
    }

    @media (max-width: 680px) {
      .page { grid-template-columns: 1fr; }
      .sidebar { position: static; }
      nav { padding: 0 16px; }
    }
  </style>
</head>
<body>

<nav>
  <a href="index.php" class="logo">👟 <span>Shoe</span>Shop</a>
  <div class="nav-links">
    <a href="catalog.php">Каталог</a>
    <?php if ($user['role'] === 'admin'): ?>
      <a href="admin.php" style="color:#ff2d78;font-weight:600;">⚡ Админ</a>
    <?php endif; ?>
  </div>
  <div class="nav-right">
    <a href="cart.php" class="cart-btn">
      Корзина <span class="cart-badge">0</span>
    </a>
    <span class="greeting">Привет, <?= htmlspecialchars($user['name']) ?></span>
    <a href="logout.php" class="btn-logout">Выйти</a>
  </div>
</nav>

<div class="page">
  <aside class="sidebar">
    <div class="profile-card">
      <div class="avatar"><?= $initials ?></div>
      <div class="profile-name"><?= htmlspecialchars($user['name']) ?></div>
      <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
      <span class="role-badge"><?= htmlspecialchars($user['role']) ?></span>
    </div>
    <nav class="sidebar-menu">
      <a href="cabinet.php" class="menu-item active">
        <span class="icon">📦</span> Мои заказы
      </a>
      <a href="settings.php" class="menu-item">
        <span class="icon">⚙️</span> Настройки
      </a>
      <a href="logout.php" class="menu-item">
        <span class="icon">🚪</span> Выйти
      </a>
    </nav>
  </aside>

  <main class="content">
    <div class="section-title">Мои заказы</div>

    <?php if (mysqli_num_rows($res_orders) === 0): ?>
      <div class="empty-orders">У вас пока нет заказов</div>
    <?php else: ?>
      <?php while ($order = mysqli_fetch_assoc($res_orders)): ?>
        <div class="order-card">
          <div class="order-header">
            <span class="order-id">Заказ #<?= $order['id'] ?></span>
            <span class="order-date"><?= formatDateRu($order['created_at']) ?></span>
          </div>
          <div class="order-product"><?= htmlspecialchars($order['products']) ?></div>
          <div class="order-total">
            Итого: <?= number_format($order['total'], 0, '.', ' ') ?> ₽
          </div>
          <div class="order-status">
            <span class="status-badge delivered">Доставлен</span>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </main>
</div>

<?php mysqli_close($conn); ?>
</body>
</html>