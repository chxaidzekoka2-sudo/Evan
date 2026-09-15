-- ====================================================================
-- Evan — database schema
-- Multilingual (ka/en/ru) e-commerce: categories, products, sizes,
-- images, orders. Import this in phpMyAdmin (XAMPP).
-- ====================================================================

CREATE DATABASE IF NOT EXISTS evan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE evan_db;

-- --------------------------------------------------------------------
-- categories: მამაკაცის / ქალის / ფეხსაცმელი / აქსესუარები
-- parent_id საშუალებას იძლევა მომავალში ქვეკატეგორიები დაემატოს
-- --------------------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(60) NOT NULL UNIQUE,
    name_ka VARCHAR(120) NOT NULL,
    name_en VARCHAR(120) NOT NULL,
    name_ru VARCHAR(120) NOT NULL,
    parent_id INT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- sizes: ერთი საერთო ცხრილი ტანსაცმლის (S/M/L) და ფეხსაცმლის (36-44)
-- ზომებისთვის — ტიპი განასხვავებს, რომ ფილტრში სწორად დაჯგუფდეს
-- --------------------------------------------------------------------
CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(10) NOT NULL,
    type ENUM('clothing','shoe') NOT NULL DEFAULT 'clothing',
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- products
-- --------------------------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sku VARCHAR(40) NOT NULL UNIQUE,
    name_ka VARCHAR(180) NOT NULL,
    name_en VARCHAR(180) NOT NULL,
    name_ru VARCHAR(180) NOT NULL,
    description_ka TEXT,
    description_en TEXT,
    description_ru TEXT,
    price DECIMAL(10,2) NOT NULL,
    is_new TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- product_images — ერთ პროდუქტს შეიძლება ჰქონდეს რამდენიმე ფოტო
-- image_path არის ფარდობითი გზა /uploads/products/-დან
-- --------------------------------------------------------------------
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- product_sizes — many-to-many, თან მარაგის რაოდენობაც
-- --------------------------------------------------------------------
CREATE TABLE product_sizes (
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    stock_qty INT NOT NULL DEFAULT 0,
    PRIMARY KEY (product_id, size_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- newsletter_subscribers — footer-ის "Sign Up" ფორმიდან
-- --------------------------------------------------------------------
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- contact_messages — contact.php ფორმიდან
-- --------------------------------------------------------------------
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- admin_users — admin panel-ის ავტორიზაცია.
-- პაროლი აქ არ იქმნება SQL-ით (bcrypt ჰეშისთვის PHP სჭირდება) —
-- admin/setup.php ერთჯერადად შექმნის პირველ ანგარიშს დაბადებისას.
-- --------------------------------------------------------------------
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------------------
-- settings — საკონტაქტო/სოც-ლინკები, Kalipso-ს მსგავსად
-- --------------------------------------------------------------------
CREATE TABLE settings (
    setting_key VARCHAR(60) PRIMARY KEY,
    setting_value VARCHAR(500)
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('whatsapp', ''),
('facebook', ''),
('instagram', ''),
('telegram', ''),
('phone', ''),
('email', '');

-- --------------------------------------------------------------------
-- orders / order_items — checkout-ისთვის (ნაბიჯი 5-ში გამოვიყენებთ)
-- --------------------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(40),
    shipping_address VARCHAR(400),
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'GEL',
    payment_status ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
    payment_provider VARCHAR(40),
    payment_reference VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    size_label VARCHAR(10),
    qty INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- ====================================================================
-- SEED DATA
-- ====================================================================

INSERT INTO categories (slug, name_ka, name_en, name_ru, sort_order) VALUES
('men',         'მამაკაცის ტანსაცმელი', "Men's Clothing",   'Мужская одежда',  1),
('women',       'ქალის ტანსაცმელი',     "Women's Clothing", 'Женская одежда',  2),
('shoes',       'ფეხსაცმელი',           'Shoes',             'Обувь',           3),
('accessories', 'აქსესუარები',          'Accessories',       'Аксессуары',      4);

INSERT INTO sizes (label, type, sort_order) VALUES
('XS','clothing',1), ('S','clothing',2), ('M','clothing',3), ('L','clothing',4), ('XL','clothing',5),
('36','shoe',10), ('37','shoe',11), ('38','shoe',12), ('39','shoe',13), ('40','shoe',14),
('41','shoe',15), ('42','shoe',16), ('43','shoe',17), ('44','shoe',18);

-- სადემონსტრაციო პროდუქტები — image_path სახელები დროებითია.
-- ატვირთე რეალური ფოტოები uploads/products/-ში ამავე სახელებით,
-- ან შეცვალე ბილიკი admin panel-იდან (ნაბიჯი 4-ში დავამატებთ).

INSERT INTO products (category_id, sku, name_ka, name_en, name_ru, price, is_new) VALUES
(2, 'EV-1001', 'ოვერსაიზ შალის ქურთუკი', 'Oversized Wool Coat',  'Oversized шерстяное пальто', 385.00, 1),
(1, 'EV-1002', 'კლასიკური ჯინსის ჟაკეტი', 'Classic Denim Jacket', 'Классическая джинсовая куртка', 219.00, 1),
(2, 'EV-1003', 'ნაქსოვი სვიტერი', 'Ribbed Knit Sweater', 'Вязаный свитер в рубчик', 165.00, 1),
(1, 'EV-1004', 'შერჩეული შარვალი', 'Tailored Trousers', 'Классические брюки', 195.00, 0),
(3, 'EV-1005', 'ტყავის ჩელსი ბუტსები', 'Leather Chelsea Boots', 'Кожаные ботинки челси', 459.00, 1),
(3, 'EV-1006', 'ბრეზენტის სნეიკერსი', 'Canvas Sneakers', 'Кеды из парусины', 179.00, 0),
(3, 'EV-1007', 'ჩამოსაცმელი ჩექმები', 'Suede Ankle Boots', 'Замшевые ботильоны', 329.00, 0),
(3, 'EV-1008', 'სარბენი სნეიკერსი', 'Running Sneakers', 'Кроссовки для бега', 249.00, 0),
(3, 'EV-1009', 'კლასიკური ლოფერები', 'Classic Loafers', 'Классические лоферы', 289.00, 0),
(3, 'EV-1010', 'ქუსლიანი სანდლები', 'Heeled Sandals', 'Босоножки на каблуке', 199.00, 0);

INSERT INTO product_images (product_id, image_path, sort_order) VALUES
(1, 'placeholder.jpg', 0),
(2, 'placeholder.jpg', 0),
(3, 'placeholder.jpg', 0),
(4, 'placeholder.jpg', 0),
(5, 'placeholder.jpg', 0),
(6, 'placeholder.jpg', 0),
(7, 'placeholder.jpg', 0),
(8, 'placeholder.jpg', 0),
(9, 'placeholder.jpg', 0),
(10, 'placeholder.jpg', 0);

-- ტანსაცმლის ზომები (XS-XL) — ID 1-5; ფეხსაცმლის ზომები (36-44) — ID 6-14
INSERT INTO product_sizes (product_id, size_id, stock_qty) VALUES
(1,1,4),(1,2,8),(1,3,10),(1,4,6),
(2,1,5),(2,2,9),(2,3,9),(2,4,5),
(3,2,6),(3,3,8),(3,4,4),
(4,1,3),(4,2,7),(4,3,7),(4,4,3),
(5,10,4),(5,11,6),(5,12,6),(5,13,4),(5,14,2),
(6,9,5),(6,10,7),(6,11,7),(6,12,5),(6,13,3),
(7,6,3),(7,7,5),(7,8,5),(7,9,3),
(8,10,6),(8,11,7),(8,12,6),(8,13,4),
(9,10,4),(9,11,6),(9,12,6),(9,13,4),(9,14,2),
(10,6,4),(10,7,6),(10,8,6),(10,9,4);
