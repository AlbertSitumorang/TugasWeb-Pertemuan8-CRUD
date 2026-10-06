<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance()->getConnection();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlash('error', 'ID produk tidak valid.');
    header('Location: index.php');
    exit;
}

// Ambil data produk yang mau diedit (prepared statement)
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    setFlash('error', 'Produk tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$kategoriList = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$supplierList = $pdo->query("SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Produk — CRUD Inventaris</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container container-form">
    <header class="page-header">
        <div>
            <span class="badge">📦 Sistem Inventaris</span>
            <h1>Edit Produk</h1>
        </div>
        <a href="index.php" class="btn btn-outline">← Kembali</a>
    </header>

    <form action="process/update_process.php" method="POST" class="card-form">
        <input type="hidden" name="id_produk" value="<?= (int) $produk['id_produk'] ?>">

        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" required maxlength="150"
                   value="<?= h($produk['nama_produk']) ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select id="id_kategori" name="id_kategori" required>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= (int) $k['id_kategori'] ?>"
                            <?= (int) $k['id_kategori'] === (int) $produk['id_kategori'] ? 'selected' : '' ?>>
                            <?= h($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_supplier">Supplier</label>
                <select id="id_supplier" name="id_supplier" required>
                    <?php foreach ($supplierList as $s): ?>
                        <option value="<?= (int) $s['id_supplier'] ?>"
                            <?= (int) $s['id_supplier'] === (int) $produk['id_supplier'] ? 'selected' : '' ?>>
                            <?= h($s['nama_supplier']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" required min="0" value="<?= (int) $produk['stok'] ?>">
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" required min="0" step="0.01" value="<?= h((string) $produk['harga']) ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="index.php" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
