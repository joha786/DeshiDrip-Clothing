CREATE DATABASE IF NOT EXISTS clothing_brand;
USE clothing_brand;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(255),
    address TEXT,
    phone VARCHAR(30),
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    size_chart TEXT NOT NULL,
    available_sizes VARCHAR(255) NOT NULL DEFAULT 'S,M,L,XL',
    price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    gender ENUM('Men','Women') NOT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_new_arrival TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    selected_size VARCHAR(50) NOT NULL DEFAULT '',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','rejected','cancelled') NOT NULL DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    selected_size VARCHAR(50) NOT NULL DEFAULT '',
    unit_price DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO users (name, email, password_hash, role, address, phone) VALUES
('Admin User', 'admin@gmail.com', '$2y$10$tJTFkw2a1SIEdnSNv8Ls5uJOsopNVzMQbp0PmfJ6iHUtoUeHiBoEC', 'admin', 'Dhaka', '01700000000'),
('Customer User', 'customer@gmail.com', '$2y$10$tJTFkw2a1SIEdnSNv8Ls5uJOsopNVzMQbp0PmfJ6iHUtoUeHiBoEC', 'customer', 'Dhaka', '01800000000');

UPDATE users SET password_hash = '$2y$10$tJTFkw2a1SIEdnSNv8Ls5uJOsopNVzMQbp0PmfJ6iHUtoUeHiBoEC' WHERE email IN ('admin@gmail.com', 'customer@gmail.com', 'admin@example.com', 'customer@example.com');

INSERT IGNORE INTO categories (id, name, parent_id) VALUES
(1, 'Men', NULL),
(2, 'Women', NULL),
(3, 'Shirts', 1),
(4, 'Pants', 1),
(5, 'T-Shirts', 1),
(6, 'Salwar', 2),
(7, 'Jeans', 2),
(8, 'Kurtis', 2);

ALTER TABLE products ADD COLUMN IF NOT EXISTS is_featured TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_new_arrival TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS available_sizes VARCHAR(255) NOT NULL DEFAULT 'S,M,L,XL';
ALTER TABLE cart ADD COLUMN IF NOT EXISTS selected_size VARCHAR(50) NOT NULL DEFAULT '';
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS selected_size VARCHAR(50) NOT NULL DEFAULT '';
ALTER TABLE orders MODIFY status ENUM('pending','confirmed','rejected','cancelled') NOT NULL DEFAULT 'pending';

INSERT IGNORE INTO products (id, name, description, size_chart, available_sizes, price, category_id, image_path, stock, gender, is_featured, is_new_arrival) VALUES
(1, 'Classic Oxford Shirt', 'Cotton shirt for office and casual wear.', 'S: 38, M: 40, L: 42, XL: 44', 'S,M,L,XL', 1450.00, 3, 'public/uploads/products/men-shirt.svg', 20, 'Men', 1, 0),
(2, 'Slim Fit Chino Pants', 'Comfortable chino pants with clean finishing.', 'S: 30, M: 32, L: 34, XL: 36', '30,32,34,36', 1890.00, 4, 'public/uploads/products/men-pants.svg', 15, 'Men', 0, 1),
(3, 'Everyday Graphic T-Shirt', 'Soft cotton t-shirt for daily wear.', 'S, M, L, XL', 'S,M,L,XL', 850.00, 5, 'public/uploads/products/men-tshirt.svg', 30, 'Men', 1, 1),
(4, 'Printed Salwar Set', 'Lightweight salwar set with elegant print.', 'S: 36, M: 38, L: 40, XL: 42', 'S,M,L,XL', 2450.00, 6, 'public/uploads/products/women-salwar.svg', 12, 'Women', 1, 0),
(5, 'High Rise Denim Jeans', 'Stretch denim jeans with high rise fit.', 'S: 28, M: 30, L: 32, XL: 34', '28,30,32,34', 2100.00, 7, 'public/uploads/products/women-jeans.svg', 18, 'Women', 0, 1),
(6, 'Cotton Kurti', 'Comfortable cotton kurti for regular wear.', 'S, M, L, XL', 'S,M,L,XL', 1350.00, 8, 'public/uploads/products/women-kurti.svg', 22, 'Women', 1, 1),
(7, 'Linen Summer Shirt', 'Breathable linen-blend shirt for warm days and casual plans.', 'S: 38, M: 40, L: 42, XL: 44', 'S,M,L,XL', 1650.00, 3, 'public/uploads/products/men-shirt.svg', 16, 'Men', 1, 1),
(8, 'Check Casual Shirt', 'Soft check shirt with a relaxed fit for everyday wear.', 'S: 38, M: 40, L: 42, XL: 44', 'S,M,L,XL', 1290.00, 3, 'public/uploads/products/men-shirt.svg', 24, 'Men', 0, 1),
(9, 'Tapered Formal Pants', 'Clean tapered pants suitable for office and evening outings.', 'S: 30, M: 32, L: 34, XL: 36', '30,32,34,36', 2250.00, 4, 'public/uploads/products/men-pants.svg', 14, 'Men', 1, 0),
(10, 'Weekend Cargo Pants', 'Durable cargo pants with useful pockets and comfortable movement.', 'S: 30, M: 32, L: 34, XL: 36', '30,32,34,36', 1990.00, 4, 'public/uploads/products/men-pants.svg', 17, 'Men', 0, 1),
(11, 'Minimal Logo T-Shirt', 'Soft cotton t-shirt with a small chest logo and clean finish.', 'S, M, L, XL', 'S,M,L,XL', 780.00, 5, 'public/uploads/products/men-tshirt.svg', 35, 'Men', 1, 1),
(12, 'Oversized Street T-Shirt', 'Oversized cotton t-shirt made for relaxed streetwear styling.', 'S, M, L, XL', 'S,M,L,XL', 980.00, 5, 'public/uploads/products/men-tshirt.svg', 28, 'Men', 0, 1),
(14, 'Daily Comfort Salwar', 'Easy-care salwar set designed for regular day-to-day use.', 'S: 36, M: 38, L: 40, XL: 42', 'S,M,L,XL', 1950.00, 6, 'public/uploads/products/women-salwar.svg', 19, 'Women', 0, 1),
(15, 'Straight Fit Denim Jeans', 'Classic straight fit jeans with comfortable stretch denim.', 'S: 28, M: 30, L: 32, XL: 34', '28,30,32,34', 2200.00, 7, 'public/uploads/products/women-jeans.svg', 21, 'Women', 1, 1),
(16, 'Block Print Kurti', 'Light cotton kurti with a block print pattern for daily wear.', 'S, M, L, XL', 'S,M,L,XL', 1550.00, 8, 'public/uploads/products/women-kurti.svg', 23, 'Women', 1, 1),
(17, 'Mandarin Collar Shirt', 'Smart mandarin collar shirt for semi-formal and casual styling.', 'S: 38, M: 40, L: 42, XL: 44', 'S,M,L,XL', 1750.00, 3, 'public/uploads/products/men-shirt.svg', 18, 'Men', 1, 0),
(18, 'Relaxed Jogger Pants', 'Comfortable jogger-style pants for travel, errands, and relaxed days.', 'S: 30, M: 32, L: 34, XL: 36', '30,32,34,36', 1690.00, 4, 'public/uploads/products/men-pants.svg', 20, 'Men', 0, 1),
(19, 'Floral Long Kurti', 'Long floral kurti with breathable fabric and a comfortable daily fit.', 'S, M, L, XL', 'S,M,L,XL', 1850.00, 8, 'public/uploads/products/women-kurti.svg', 15, 'Women', 1, 1),
(20, 'Ankle Fit Denim Jeans', 'Modern ankle fit jeans with soft stretch and clean stitching.', 'S: 28, M: 30, L: 32, XL: 34', '28,30,32,34', 2350.00, 7, 'public/uploads/products/women-jeans.svg', 16, 'Women', 0, 1);


UPDATE products SET is_featured = 1 WHERE id IN (1, 3, 4, 6);
UPDATE products SET is_new_arrival = 1 WHERE id IN (2, 3, 5, 6);

UPDATE products
SET image_path = CASE
    WHEN category_id = 3 THEN 'public/uploads/products/men-shirt.svg'
    WHEN category_id = 4 THEN 'public/uploads/products/men-pants.svg'
    WHEN category_id = 5 THEN 'public/uploads/products/men-tshirt.svg'
    WHEN category_id = 6 THEN 'public/uploads/products/women-salwar.svg'
    WHEN category_id = 7 THEN 'public/uploads/products/women-jeans.svg'
    WHEN category_id = 8 THEN 'public/uploads/products/women-kurti.svg'
    ELSE 'public/uploads/products/men-tshirt.svg'
END
WHERE image_path IS NULL OR image_path = '';
