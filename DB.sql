-- ============================================================
-- php-store  |  Luxury Shoe Store
-- Full dump: CREATE TABLE + INSERT DATA
-- Import into any empty database
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table structure
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `email` varchar(35) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(6) NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(15,0) NOT NULL,
  `size` varchar(15) NOT NULL,
  `brand` varchar(25) NOT NULL,
  `category` varchar(25) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total` decimal(15,0) NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_items` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `order_id` int(15) NOT NULL,
  `product_id` int(15) NOT NULL,
  `quantity` int(15) NOT NULL,
  `price` decimal(15,0) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Users
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1,  'admin',        'admin@store.com',   '$2y$10$abcdefghijklmnopqrstuA', 'admin', '2025-01-01'),
(2,  'Egor Utyasho', 'egor@example.com',  '$2y$10$abcdefghijklmnopqrstuB', 'user',  '2025-01-12'),
(3,  'Erik Evona',   'erik@example.com',  '$2y$10$abcdefghijklmnopqrstuC', 'user',  '2025-01-20'),
(4,  'Denis Klyuki', 'denis@example.com', '$2y$10$abcdefghijklmnopqrstuD', 'user',  '2025-02-05'),
(5,  'Renat Minin',  'renat@example.com', '$2y$10$abcdefghijklmnopqrstuE', 'user',  '2025-02-18'),
(6,  'Alice',        'alice@example.com', '$2y$10$abcdefghijklmnopqrstuF', 'user',  '2025-03-01'),
(7,  'Bob',          'bob@example.com',   '$2y$10$abcdefghijklmnopqrstuG', 'user',  '2025-03-15'),
(8,  'Carol',        'carol@example.com', '$2y$10$abcdefghijklmnopqrstuH', 'user',  '2025-04-02'),
(9,  'David',        'david@example.com', '$2y$10$abcdefghijklmnopqrstuI', 'user',  '2025-04-20'),
(10, 'Eva',          'eva@example.com',   '$2y$10$abcdefghijklmnopqrstuJ', 'user',  '2025-05-10');

-- --------------------------------------------------------
-- Products (Casual | Sport | Formal | Outdoor | Party)
-- --------------------------------------------------------

INSERT INTO `products` (`id`, `name`, `description`, `price`, `size`, `brand`, `category`, `stock`) VALUES
(1,  'Triple S',     'Iconic chunky sneaker with multi-layer sole, perfect for everyday wear.',          995,  '42', 'Balenciaga',   'Casual',   25),
(2,  'B23 Oblique',  'High-top sneaker with Dior Oblique canvas, great casual statement piece.',        1100,  '42', 'Dior',         'Casual',   18),
(3,  'Rhyton',       'Vintage-inspired sneaker with Gucci logo print, easygoing everyday style.',        890,  '41', 'Gucci',        'Casual',   28),
(4,  'SL61 Low',     'Minimal low-top leather sneaker, clean look for daily outfits.',                   750,  '40', 'SaintLaurent', 'Casual',   40),
(5,  'Horsebit',     'Classic horsebit loafer in smooth calfskin, ultimate smart-casual shoe.',          890,  '41', 'Gucci',        'Casual',   22),
(6,  'Speed Train',  'Sock-like knit sneaker built for fast movement and all-day comfort.',              895,  '41', 'Balenciaga',   'Sport',    30),
(7,  'Track Sneak',  'Multi-panel running-inspired sneaker with lightweight cushioned sole.',           1050,  '43', 'Balenciaga',   'Sport',    20),
(8,  'Cloudbust',    'Technical knit sneaker with breathable upper and Prada triangle badge.',           920,  '43', 'Prada',        'Sport',    18),
(9,  'Run Away',     'Bold runner sneaker with LV circle logo and energy-return foam sole.',            1090,  '42', 'LouisVuitton', 'Sport',    17),
(10, 'Chain React',  'Platform sports sneaker with reflective mesh and Versace chain print.',            875,  '42', 'Versace',      'Sport',    30),
(11, 'Monolith',     'Platform Derby with lug sole and Prada triangle logo, sharp formal look.',        980,  '42', 'Prada',        'Formal',   14),
(12, 'Derby Shoe',   'Hand-patinated calfskin derby by Berluti, refined and timeless.',                1200,  '42', 'Berluti',      'Formal',   10),
(13, 'B27 Low',      'Low-top sneaker blending leather with Dior Oblique, smart and polished.',        1050,  '40', 'Dior',         'Formal',   22),
(14, 'Trainer Hi',   'High-top sneaker with YSL logo, dressy enough for business-casual settings.',     790,  '41', 'SaintLaurent', 'Formal',   35),
(15, 'Flashtrek',    'Chunky hiking-inspired sneaker with Gucci lettering and aggressive lug sole.',    980,  '43', 'Gucci',        'Outdoor',  20),
(16, 'Archlight',    'Curved-sole sneaker for long walks with Louis Vuitton monogram detail.',         1150,  '44', 'LouisVuitton', 'Outdoor',  12),
(17, 'Combat Boot',  'Chunky lug-sole combat boot in black leather with silver zippers.',              1100,  '41', 'Dior',         'Outdoor',  12),
(18, 'Walk n Dior',  'Eye-catching sneaker with CD signature embroidery, made for nights out.',         950,  '39', 'Dior',         'Party',    15),
(19, 'Medusa Mule',  'Slip-on mule with oversized Versace Medusa emblem, bold party statement.',        820,  '40', 'Versace',      'Party',    25),
(20, 'Sock Boot',    'Stretch-knit stiletto ankle boot, sleek and striking for evening wear.',          990,  '38', 'Balenciaga',   'Party',    16);

-- --------------------------------------------------------
-- Orders
-- --------------------------------------------------------

INSERT INTO `orders` (`id`, `user_id`, `total`, `created_at`) VALUES
(1,  2,   995, '2025-06-03'),
(2,  3,  1890, '2025-06-07'),
(3,  4,  1050, '2025-06-14'),
(4,  5,  1910, '2025-06-19'),
(5,  6,   890, '2025-06-25'),
(6,  7,  2130, '2025-07-01'),
(7,  8,  1150, '2025-07-09'),
(8,  9,   875, '2025-07-15'),
(9,  10, 1945, '2025-07-22'),
(10, 2,  2300, '2025-07-28'),
(11, 3,   920, '2025-08-04'),
(12, 4,  2090, '2025-08-11'),
(13, 5,   990, '2025-08-17'),
(14, 6,  1050, '2025-08-23'),
(15, 7,   750, '2025-09-01');

-- --------------------------------------------------------
-- Order Items
-- --------------------------------------------------------

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1,   1,  1,  1,  995),
(2,   2,  2,  1, 1100),
(3,   2, 14,  1,  790),
(4,   3,  7,  1, 1050),
(5,   4,  9,  1, 1090),
(6,   4, 19,  1,  820),
(7,   5,  3,  1,  890),
(8,   6, 16,  1, 1150),
(9,   6, 11,  1,  980),
(10,  7, 16,  1, 1150),
(11,  8, 10,  1,  875),
(12,  9, 13,  1, 1050),
(13,  9,  6,  1,  895),
(14, 10, 12,  1, 1200),
(15, 10, 17,  1, 1100),
(16, 11,  8,  1,  920),
(17, 12, 20,  1,  990),
(18, 12,  2,  1, 1100),
(19, 13, 20,  1,  990),
(20, 14,  7,  1, 1050),
(21, 15,  4,  1,  750);

COMMIT;