-- ====================================================================
-- migration_2.sql — თუ უკვე გაქვს evan_db შექმნილი (schema.sql უკვე
-- დაიმპორტე ადრე) და არ გინდა მთლიანად თავიდან წაშლა/გადატვირთვა,
-- უბრალოდ ეს ფაილი დაიმპორტე დამატებით. schema.sql-საც განახლდა იგივე
-- ცხრილებით, ახალი დაყენებისთვის.
-- ====================================================================
USE evan_db;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
