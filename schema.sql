-- ============================================
-- schema.sql
-- Tugas Rutin 8 - CRUD Inventaris
-- ============================================

CREATE DATABASE IF NOT EXISTS inventaris_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE inventaris_db;

-- ============================================
-- Tabel kategori barang
-- ============================================
CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_category_name (category_name)
) ENGINE=InnoDB;

-- ============================================
-- Tabel lokasi penyimpanan barang
-- ============================================
CREATE TABLE locations (
    location_id   INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_location_name (location_name)
) ENGINE=InnoDB;

-- ============================================
-- Tabel inventaris barang
-- ============================================
CREATE TABLE inventories (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name    VARCHAR(150) NOT NULL,
    quantity     INT NOT NULL DEFAULT 0,
    category_id  INT NOT NULL,
    location_id  INT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_inventory_category
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_inventory_location
        FOREIGN KEY (location_id) REFERENCES locations(location_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_inventory_category (category_id),
    INDEX idx_inventory_location (location_id)
) ENGINE=InnoDB;

-- ============================================
-- Tabel log aktivitas (untuk bonus transaction)
-- ============================================
CREATE TABLE activity_logs (
    log_id     INT AUTO_INCREMENT PRIMARY KEY,
    action     VARCHAR(20) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id  INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Seed Data
-- ============================================
INSERT INTO categories (category_name) VALUES
('Elektronik'),
('Alat Tulis Kantor'),
('Furnitur');

INSERT INTO locations (location_name) VALUES
('Gudang A'),
('Gudang B'),
('Ruang Lab Komputer');

INSERT INTO inventories (item_name, quantity, category_id, location_id) VALUES
('Laptop Lenovo ThinkPad', 5, 1, 3),
('Proyektor Epson', 2, 1, 1),
('Kertas A4', 50, 2, 2),
('Meja Kerja', 10, 3, 1),
('Kursi Kantor', 15, 3, 2);