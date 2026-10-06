<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Database::getInstance()->getConnection();

$keyword = trim($_GET['cari'] ?? '');

$sql = "SELECT p.id_produk, p.nama_produk, k.nama_kategori, s.nama_supplier, p.stok, p.harga
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        JOIN supplier s ON p.id_supplier = s.id_supplier";

if ($keyword !== '') {
    $sql .= " WHERE p.nama_produk LIKE :kw";
}
$sql .= " ORDER BY p.id_produk ASC";

$stmt = $pdo->prepare($sql);
if ($keyword !== '') {
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
}
$stmt->execute();
$rows = $stmt->fetchAll();

// ---------- BONUS: Export Laporan CSV ----------
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan_inventaris_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
// BOM agar Excel membaca UTF-8 dengan benar
fwrite($output, "\xEF\xBB\xBF");

fputcsv($output, ['ID Produk', 'Nama Produk', 'Kategori', 'Supplier', 'Stok', 'Harga']);

foreach ($rows as $row) {
    fputcsv($output, [
        $row['id_produk'],
        $row['nama_produk'],
        $row['nama_kategori'],
        $row['nama_supplier'],
        $row['stok'],
        $row['harga'],
    ]);
}

fclose($output);
exit;
