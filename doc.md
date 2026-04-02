# ShoeShop — Документация проекта

## Структура проекта

```
php-store-ostrava/
├── index.php         — Главная страница: каталог товаров, фильтры, поиск
├── product.php       — Страница отдельного товара
├── login.php         — Страница входа
├── register.php      — Страница регистрации
├── logout.php        — Выход из аккаунта
├── cart.php          — Корзина
├── cabinet.php       — Личный кабинет
├── admin.php         — Панель администратора
├── functions.php     — Все функции проекта
├── db.php            — Подключение к базе данных
├── style.css         — Стили главного сайта
├── admin.css         — Стили панели администратора
├── DB.sql            — Дамп базы данных
└── uploads/          — Папка для загруженных фото товаров
```

---

## База данных

**Название БД:** `php-store`

### Таблицы

**users**
| Поле | Тип | Описание |
|------|-----|----------|
| id | int AUTO_INCREMENT | |
| name | varchar(15) | Логин |
| email | varchar(35) | |
| password | varchar(255) | |
| role | varchar(6) | `user` или `admin` |
| created_at | date | |

**products**
| Поле | Тип | Описание |
|------|-----|----------|
| id | int AUTO_INCREMENT | |
| name | varchar(15) | |
| description | text | |
| price | decimal(15,0) | |
| size | varchar(15) | |
| brand | varchar(25) | |
| category | varchar(25) | Casual, Sport, Formal, Outdoor, Party |
| stock | int | |
| image | varchar(255) | Имя файла в uploads/ |

**orders** — id, user_id, total, created_at

**order_items** — id, order_id, product_id, quantity, price

---

## functions.php — Все функции

### Авторизация и регистрация

**`login($username, $password)`**
Ищет пользователя в БД по имени и паролю. При совпадении создаёт сессию и делает редирект на `index.php`. Если не найден — выводит сообщение об ошибке.

**`registration($usernamedb, $passworddb, $emaildb)`**
Проверяет свободен ли логин через `checklogin()`. Если свободен — делает INSERT в таблицу `users` с role=`user`, создаёт сессию, редирект на главную. Если занят — редирект на register.php с сообщением об ошибке в сессии.

**`checklogin($username, $db)`**
Делает `SELECT COUNT(*)` из `users` по имени. Возвращает `true` если логин свободен, `false` если занят.

### Проверка ролей

**`isAdmin()`**
Возвращает `true` если в сессии `$_SESSION['role'] === 'admin'`, иначе `false`. Используется для показа кнопки Admin Panel и защиты admin.php.

**`adminExists()`**
Делает `SELECT COUNT(*)` из `users WHERE role='admin'`. Возвращает `true` если хотя бы один администратор существует.

### Каталог товаров

**`getFilteredProducts()`**
Читает фильтры из `$_GET` (`brand`, `category`, `q`) и строит SQL с `WHERE ... AND ...`. Все фильтры работают совместно. Возвращает результат запроса.

**`getProductById($id)`**
Делает `SELECT * FROM products WHERE id = $id LIMIT 1`. Возвращает массив с данными товара или `null`.

### Функции админ панели

**`addProduct()`**
Читает данные из `$_POST`, экранирует через `mysqli_real_escape_string`. Если загружено фото (JPG/PNG) — сохраняет в `uploads/` с уникальным именем. Делает INSERT в `products`.

**`updateProduct($id)`**
Аналогично `addProduct()` но делает UPDATE по id. Если новое фото загружено — заменяет старое.

**`deleteProduct($id)`**
Делает `DELETE FROM products WHERE id = $id`.

**`getAllProducts()`**
Возвращает все товары из БД (`SELECT * FROM products ORDER BY id DESC`) для списка в админке.

---

## Страницы

### index.php
- Подключает `session_start()` и `functions.php`
- Показывает фильтры по бренду (All + 5 брендов)
- Поиск по названию товара
- Кнопка сброса фильтров (появляется только при активном фильтре)
- Вызывает `getFilteredProducts()` и выводит карточки товаров с фото
- Категории в sidebar через `buildUrl()` — сохраняют активные параметры
- В хедере: для не залогиненных — Login, для user — username + Logout, для admin — Admin Panel + username + Logout

### product.php
- Получает `?id=` из URL, вызывает `getProductById()`
- Если товар не найден — редирект на главную
- Показывает фото слева (360×360), данные товара справа
- Кнопка "Add to cart" и "← Back"

### login.php
- Форма с полями Login и Password
- POST отправляет на `login.php`, вызывает `login()`

### admin.php
- Защита: `isAdmin()` — не-админы редиректятся на главную
- Создаёт папку `uploads/` если не существует
- Sidebar (30% экрана): навигация Add Product / Products List / Back / Logout
- Баннер с `baner.png` — затемняется при выборе раздела
- **Add Product**: форма добавления + живой предпросмотр карточки + загрузка фото
- **Products List**: список с фото, кнопка Edit (открывает модал), кнопка Delete (требует confirm)
- Модал редактирования с предпросмотром и заменой фото

---

## Стили

### style.css
Общие стили сайта. Цветовая схема: фон `#0a0a0a`, акцент `#ccff00`.

### admin.css
Стили только для admin.php. Тот же цветовой стиль.

---

---

# История изменений (Changelog)

---

## Сессия 1 — Авторизация и регистрация

**functions.php**
- Добавлены комментарии к каждой функции
- `login()` — написана логика: SELECT по имени и паролю, создание сессии, редирект
- `registration()` — уже существовала, добавлен редирект после регистрации
- `checklogin()` — уже существовала

**login.php**
- Добавлен `session_start()` и `require_once("functions.php")`
- Форма подключена к `login()` через POST
- Исправлено имя поля `Hasło` → `Haslo` (убрана польская буква из name атрибута)

---

## Сессия 2 — Роли и Admin Panel

**functions.php**
- Добавлена `isAdmin()`
- Добавлена `adminExists()`

**index.php**
- Добавлен `session_start()` + `require_once("functions.php")`
- Кнопка Admin Panel показывается только для `isAdmin()`
- Показ username + Logout для залогиненных, Login для остальных

**admin.php / cabinet.php**
- Исправлена ссылка лого с `/index.php` на `index.php` (убран ведущий слэш, из-за которого вело на корень XAMPP)

---

## Сессия 3 — Каталог товаров и фильтры

**functions.php**
- Добавлена `getFilteredProducts()` — фильтрация по brand, category, search через AND
- Добавлена `getProductById($id)`

**index.php**
- Карточки товаров выводятся из БД через `getFilteredProducts()`
- Добавлена функция `buildUrl()` — сохраняет активные GET параметры при переключении фильтров
- Фильтры по бренду и категории работают совместно (не перезаписывают друг друга)
- Добавлена кнопка сброса всех фильтров (только при активном фильтре)
- Поиск сохраняет активные brand и category через hidden inputs

**product.php**
- Полностью переписан: динамически загружает товар из БД по `?id=`
- При несуществующем id — редирект на главную

---

## Сессия 4 — Админ панель и фото товаров

**functions.php** — новый блок "ФУНКЦИИ АДМИН ПАНЕЛИ":
- `addProduct()` — INSERT с загрузкой фото JPG/PNG в uploads/
- `updateProduct($id)` — UPDATE с заменой фото
- `deleteProduct($id)` — DELETE
- `getAllProducts()` — SELECT для списка в админке

**admin.css** — создан с нуля:
- Sidebar layout, баннер с baner.png
- Формы добавления/редактирования с предпросмотром карточки
- Список товаров, модал редактирования

**admin.php** — полностью переписан:
- Новый layout: sidebar 30% + main 70%
- Баннер затемняется при выборе раздела
- Add Product: живой предпросмотр карточки, загрузка фото
- Products List: Edit (модал) + Delete (confirm)
- Удалён старый дублирующий код с `$conn` и `mysqli_close()`

**style.css**
- `.product-card-img` — фото в карточке на главной
- `.product-detail` / `.product-detail-img` — фото на странице товара
- `.btn-back`, `.btn-view` — кнопки навигации

**index.php**
- Карточки показывают фото из `uploads/` если оно есть

**product.php**
- Показывает фото товара слева от информации
