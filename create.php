<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance()->getConnection();

// Ambil data untuk dropdown kategori & supplier
$kategoriList = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$supplierList = $pdo->query("SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Produk — CRUD Inventaris</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container container-form">
    <header class="page-header">
        <div>
            <span class="badge">📦 Sistem Inventaris</span>
            <h1>Tambah Produk Baru</h1>
        </div>
        <a href="index.php" class="btn btn-outline">← Kembali</a>
    </header>

    <form action="process/create_process.php" method="POST" class="card-form">
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" required maxlength="150" placeholder="Contoh: Keyboard Mechanical RGB">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select id="id_kategori" name="id_kategori" required>
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= (int) $k['id_kategori'] ?>"><?= h($k['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_supplier">Supplier</label>
                <select id="id_supplier" name="id_supplier" required>
                    <option value="" disabled selected>-- Pilih Supplier --</option>
                    <?php foreach ($supplierList as $s): ?>
                        <option value="<?= (int) $s['id_supplier'] ?>"><?= h($s['nama_supplier']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" required min="0" value="0">
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" required min="0" step="0.01" value="0">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Simpan Produk</button>
            <a href="index.php" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
