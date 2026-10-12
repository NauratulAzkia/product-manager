-- =======================================================
-- Database Schema: store_db
-- Mini Project: Product Manager - Katalog & Inventaris Barang
-- =======================================================

CREATE DATABASE IF NOT EXISTS store_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE store_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS products;
SET FOREIGN_KEY_CHECKS = 1;

-- Tabel products sesuai spesifikasi modul relasional
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Umum',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Data awal: Katalog Kopi Nusantara
INSERT INTO products (name, category, price, stock) VALUES
('Bubuk Kopi Robusta Temanggung 500gr', 'Kopi Robusta', 100000.00, 25),
('Kopi Arabika Bali Kintamani 200gr', 'Kopi Arabika', 120000.00, 18),
('Kopi Bubuk Arabica Aceh Gayo 500gr', 'Kopi Arabika', 175000.00, 12);
