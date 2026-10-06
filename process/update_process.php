<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

$id_produk   = (int) ($_POST['id_produk'] ?? 0);
$nama_produk = trim($_POST['nama_produk'] ?? '');
$id_kategori = (int) ($_POST['id_kategori'] ?? 0);
$id_supplier = (int) ($_POST['id_supplier'] ?? 0);
$stok        = (int) ($_POST['stok'] ?? 0);
$harga       = (float) ($_POST['harga'] ?? 0);

if ($id_produk <= 0 || $nama_produk === '' || $id_kategori <= 0 || $id_supplier <= 0 || $stok < 0 || $harga < 0) {
    setFlash('error', 'Data tidak valid. Pastikan semua field terisi dengan benar.');
    header('Location: ../index.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "UPDATE produk
         SET nama_produk = :nama_produk,
             id_kategori  = :id_kategori,
             id_supplier  = :id_supplier,
             stok         = :stok,
             harga        = :harga
         WHERE id_produk  = :id_produk"
    );
    $stmt->execute([
        ':nama_produk' => $nama_produk,
        ':id_kategori' => $id_kategori,
        ':id_supplier' => $id_supplier,
        ':stok'        => $stok,
        ':harga'       => $harga,
        ':id_produk'   => $id_produk,
    ]);

    setFlash('success', "Produk \"{$nama_produk}\" berhasil diperbarui.");
} catch (PDOException $e) {
    setFlash('error', 'Gagal memperbarui produk: ' . $e->getMessage());
}

header('Location: ../index.php');
exit;
