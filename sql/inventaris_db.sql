-- =========================================================
-- CRUD Inventaris - Tugas Rutin 8
-- Database: inventaris_db
-- =========================================================

CREATE DATABASE IF NOT EXISTS inventaris_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

-- ---------------------------------------------------------
-- Tabel 1: kategori
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel 2: supplier
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS supplier (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(150) NOT NULL,
    kontak VARCHAR(50) NOT NULL,
    alamat VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel 3: produk (memiliki FK ke kategori & supplier)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    id_kategori INT NOT NULL,
    id_supplier INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori)
        REFERENCES kategori(id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (id_supplier)
        REFERENCES supplier(id_supplier) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel Bonus: log_aktivitas (untuk transaction log pada delete)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    aksi VARCHAR(20) NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Seed data: kategori (5 baris)
-- ---------------------------------------------------------
INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'),
('Alat Tulis Kantor'),
('Furnitur'),
('Peralatan Dapur'),
('Aksesoris Komputer');

-- ---------------------------------------------------------
-- Seed data: supplier (5 baris)
-- ---------------------------------------------------------
INSERT INTO supplier (nama_supplier, kontak, alamat) VALUES
('PT Sumber Jaya Elektronik', '061-4567890', 'Jl. Gatot Subroto No. 12, Medan'),
('CV Mitra Kantor Sejahtera', '061-4551122', 'Jl. Setia Budi No. 45, Medan'),
('UD Meubel Indah', '0813-6543-2100', 'Jl. Sisingamangaraja No. 88, Medan'),
('PT Dapur Nusantara', '061-4223344', 'Jl. Iskandar Muda No. 20, Medan'),
('CV Komputer Prima', '0812-6098-7654', 'Jl. Ringroad No. 5, Medan');

-- ---------------------------------------------------------
-- Seed data: produk (5 baris, mereferensikan kategori & supplier di atas)
-- ---------------------------------------------------------
INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga) VALUES
('Keyboard Mechanical RGB', 5, 5, 25, 350000.00),
('Kertas HVS A4 80gr', 2, 2, 100, 45000.00),
('Kursi Kantor Ergonomis', 3, 3, 15, 850000.00),
('Blender Multifungsi', 4, 4, 10, 275000.00),
('Mouse Wireless Silent', 5, 5, 40, 120000.00);
