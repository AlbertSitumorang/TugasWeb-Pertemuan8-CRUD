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

try {
    // ---------- BONUS: Transaction pada delete + log aktivitas ----------
    $pdo->beginTransaction();

    // Ambil dulu nama produk untuk keperluan log & flash message
    $stmt = $pdo->prepare("SELECT nama_produk FROM produk WHERE id_produk = :id");
    $stmt->execute([':id' => $id]);
    $produk = $stmt->fetch();

    if (!$produk) {
        $pdo->rollBack();
        setFlash('error', 'Produk tidak ditemukan atau sudah dihapus.');
        header('Location: index.php');
        exit;
    }

    $namaProduk = $produk['nama_produk'];

    // 1. Catat log aktivitas SEBELUM data dihapus
    $logStmt = $pdo->prepare(
        "INSERT INTO log_aktivitas (aksi, nama_produk, keterangan)
         VALUES ('DELETE', :nama_produk, :keterangan)"
    );
    $logStmt->execute([
        ':nama_produk' => $namaProduk,
        ':keterangan'  => "Produk ID {$id} dihapus dari inventaris",
    ]);

    // 2. Hapus data produk
    $deleteStmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = :id");
    $deleteStmt->execute([':id' => $id]);

    // Jika kedua query berhasil, commit. Jika ada error, otomatis lempar exception -> rollback.
    $pdo->commit();

    setFlash('success', "Produk \"{$namaProduk}\" berhasil dihapus (tercatat di log aktivitas).");
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlash('error', 'Gagal menghapus produk: ' . $e->getMessage());
}

header('Location: index.php');
exit;
