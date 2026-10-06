<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

$nama_produk = trim($_POST['nama_produk'] ?? '');
$id_kategori = (int) ($_POST['id_kategori'] ?? 0);
$id_supplier = (int) ($_POST['id_supplier'] ?? 0);
$stok        = (int) ($_POST['stok'] ?? 0);
$harga       = (float) ($_POST['harga'] ?? 0);

// Validasi sederhana
if ($nama_produk === '' || $id_kategori <= 0 || $id_supplier <= 0 || $stok < 0 || $harga < 0) {
    setFlash('error', 'Data tidak valid. Pastikan semua field terisi dengan benar.');
    header('Location: ../create.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga)
         VALUES (:nama_produk, :id_kategori, :id_supplier, :stok, :harga)"
    );
    $stmt->execute([
        ':nama_produk' => $nama_produk,
        ':id_kategori' => $id_kategori,
        ':id_supplier' => $id_supplier,
        ':stok'        => $stok,
        ':harga'       => $harga,
    ]);

    setFlash('success', "Produk \"{$nama_produk}\" berhasil ditambahkan.");
} catch (PDOException $e) {
    setFlash('error', 'Gagal menambahkan produk: ' . $e->getMessage());
}

header('Location: ../index.php');
exit;
