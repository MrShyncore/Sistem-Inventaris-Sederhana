-- ============================================
-- Sistem Inventaris Barang — Skrip Database
-- ============================================
-- Cara import:
--   phpMyAdmin : tab Import → pilih file ini → Go
--   MySQL CLI  : mysql -u root -p < database.sql
--
-- Kredensial login default (DEMO):
--   Username: admin
--   Password: admin123
--
-- ⚠️ Hash di bawah adalah hash bcrypt dari 'admin123'.
--    Untuk produksi, WAJIB ganti password setelah login pertama.
-- ============================================

CREATE DATABASE IF NOT EXISTS db_inventaris
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_inventaris;

-- --------------------------------------------
-- Tabel users: otentikasi login
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------
-- Tabel barang: data inventaris
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    jumlah INT NOT NULL DEFAULT 0,
    harga DECIMAL(15,2) NOT NULL DEFAULT 0,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------
-- User admin default (password: admin123)
-- Generate ulang dengan: php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- --------------------------------------------
INSERT IGNORE INTO users (username, password, nama) VALUES
('admin',
 '$2y$10$N9GrgsTw.6XfKrauOemgAeSeNIXRmKwiN/fBenOpf.AK1a/7SgCP.',
 'Administrator');

-- --------------------------------------------
-- Data contoh barang
-- --------------------------------------------
INSERT IGNORE INTO barang (kode_barang, nama_barang, kategori, jumlah, harga, keterangan) VALUES
('ELK-001', 'Laptop Asus VivoBook',  'elektronik',  5,  7500000.00, 'Garansi resmi 1 tahun'),
('ELK-002', 'Mouse Logitech M331',   'elektronik', 20,   185000.00, 'Wireless'),
('ATK-001', 'Kertas HVS A4',         'atk',        50,    55000.00, 'Rim 500 lembar'),
('FUR-001', 'Meja Kerja Kayu',       'furniture',   8,   850000.00, 'Ukuran 120x60 cm'),
('ELK-003', 'Proyektor Epson X500',  'elektronik',  2,  6200000.00, 'Untuk ruang meeting');