-- ===========================================================
-- INSERT MISSING FOOD ITEMS
-- ===========================================================
-- Run this in phpMyAdmin (SQL tab) on your "project" database.
-- Your existing Burgers (IDs 1-7) and Pizzas (IDs 8-13) are
-- already there - this only adds the missing categories.
--
-- IMPORTANT: The category pages (fried_chicken.php, pasta.php,
-- momo.php, cold_drink.php) already have ORDER buttons that
-- link to order.php?id=14 through order.php?id=36. These
-- INSERT statements use the exact same IDs so the links match.
-- ===========================================================


-- -----------------------------------------------
-- FRIED CHICKEN (IDs 14 - 19)
-- -----------------------------------------------
INSERT INTO foods (f_id, f_name, price, image) VALUES
(14, 'CLASSIC CRISPY CHICKEN', 450.00, 'IMAGES/crispy chicken.jpg'),
(15, 'SPICY HOT WINGS',        350.00, 'IMAGES/hot wings.jpg'),
(16, 'CHICKEN DRUMSTICKS',     300.00, 'IMAGES/drumsticks.jpg'),
(17, 'CHICKEN POPCORN',        320.00, 'IMAGES/chicken popcorn.jpg'),
(18, 'CHICKEN STRIPS',         360.00, 'IMAGES/chicken strips.jpg'),
(19, 'FAMILY BUCKET',          950.00, 'IMAGES/family bucket chicken.jpg');


-- -----------------------------------------------
-- PASTA (IDs 20 - 25)
-- -----------------------------------------------
INSERT INTO foods (f_id, f_name, price, image) VALUES
(20, 'CREAMY ALFREDO PASTA', 350.00, 'IMAGES/alfredo pasta.jpg'),
(21, 'SPAGHETTI',            380.00, 'IMAGES/spaghetti.jpg'),
(22, 'RED SAUSE PASTA',      320.00, 'IMAGES/red sause pasta.jpg'),
(23, 'MUSHROOM PASTA',       360.00, 'IMAGES/mushroom pasta.jpg'),
(24, 'VEG PASTA',            300.00, 'IMAGES/veg pasta.jpg'),
(25, 'BAKED CHEESY PASTA',   420.00, 'IMAGES/baked cheesy pasta.jpg');


-- -----------------------------------------------
-- MOMO (IDs 26 - 32)
-- -----------------------------------------------
INSERT INTO foods (f_id, f_name, price, image) VALUES
(26, 'STEAMED CHICKEN MOMO', 180.00, 'IMAGES/steamed momo.jpg'),
(27, 'BUFF MOMO',            170.00, 'IMAGES/buff momo.jpg'),
(28, 'FRIED MOMO',           200.00, 'IMAGES/fried momo.jpg'),
(29, 'CHILI MOMO',           240.00, 'IMAGES/chili momo.jpg'),
(30, 'VEG MOMO',             150.00, 'IMAGES/veg momo.jpg'),
(31, 'CHEESE MOMO',          220.00, 'IMAGES/cheese momo.jpg'),
(32, 'JHOL MOMO',            220.00, 'IMAGES/jhol momo.jpg');


-- -----------------------------------------------
-- COLD DRINK (IDs 33 - 36)
-- -----------------------------------------------
INSERT INTO foods (f_id, f_name, price, image) VALUES
(33, 'PEPSI',     90.00, 'IMAGES/pepsi.jpg'),
(34, 'SPRITE',    90.00, 'IMAGES/sprite.jpg'),
(35, 'COCA COLA', 90.00, 'IMAGES/coca cola.jpg'),
(36, 'FANTA',     90.00, 'IMAGES/fanta.jpg');
