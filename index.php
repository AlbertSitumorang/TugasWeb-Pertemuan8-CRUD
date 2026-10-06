<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance()->getConnection();

// ---------- PENCARIAN (BONUS) ----------
$keyword = trim($_GET['cari'] ?? '');

// ---------- PAGINATION (BONUS) ----------
$perPage    = 5;
$page       = max(1, (int) ($_GET['page'] ?? 1));
$offset     = ($page - 1) * $perPage;

// Hitung total data (untuk pagination), menggunakan prepared statement
if ($keyword !== '') {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE nama_produk LIKE :kw");
    $countStmt->execute([':kw' => "%$keyword%"]);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM produk");
}
$totalData  = (int) $countStmt->fetchColumn();
$totalPages = (int) ceil($totalData / $perPage);

// ---------- QUERY LIST PRODUK DENGAN JOIN 2 TABEL (kategori & supplier) ----------
$sql = "SELECT p.id_produk, p.nama_produk, p.stok, p.harga,
               k.nama_kategori, s.nama_supplier
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        JOIN supplier s ON p.id_supplier = s.id_supplier";

if ($keyword !== '') {
    $sql .= " WHERE p.nama_produk LIKE :kw";
}
$sql .= " ORDER BY p.id_produk DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($keyword !== '') {
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD Inventaris — Tugas Rutin 8</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">

    <header class="page-header">
        <div>
            <span class="badge">📦 Sistem Inventaris</span>
            <h1>Daftar Produk</h1>
        </div>
        <div class="header-actions">
            <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
            <a href="process/export.php<?= $keyword !== '' ? '?cari=' . urlencode($keyword) : '' ?>" class="btn btn-outline">⬇ Export CSV</a>
        </div>
    </header>

    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?>">
            <?= h($flash['message']) ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="GET" class="search-bar">
        <input type="text" name="cari" placeholder="Cari nama produk..." value="<?= h($keyword) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="index.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produkList)): ?>
                    <tr>
                        <td colspan="7" class="text-center empty-state">Tidak ada data produk ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = $offset + 1; ?>
                    <?php foreach ($produkList as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= h($row['nama_produk']) ?></td>
                            <td><span class="pill"><?= h($row['nama_kategori']) ?></span></td>
                            <td><?= h($row['nama_supplier']) ?></td>
                            <td class="<?= (int)$row['stok'] <= 10 ? 'stok-rendah' : '' ?>"><?= (int) $row['stok'] ?></td>
                            <td><?= rupiah($row['harga']) ?></td>
                            <td class="text-center action-cell">
                                <a href="update.php?id=<?= (int) $row['id_produk'] ?>" class="btn btn-small btn-edit">Edit</a>
                                <a href="delete.php?id=<?= (int) $row['id_produk'] ?>"
                                   class="btn btn-small btn-delete"
                                   onclick="return confirm('Yakin ingin menghapus produk &quot;<?= h(addslashes($row['nama_produk'])) ?>&quot;? Tindakan ini tidak bisa dibatalkan.');">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?page=<?= $i ?><?= $keyword !== '' ? '&cari=' . urlencode($keyword) : '' ?>"
                   class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <p class="footnote">Total produk: <?= $totalData ?> | Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></p>
</div>
</body>
</html>
