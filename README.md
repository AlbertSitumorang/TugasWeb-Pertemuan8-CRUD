# CRUD Inventaris — Tugas Rutin 8

Aplikasi CRUD sederhana untuk mengelola data inventaris (produk, kategori, supplier)
menggunakan PHP native + PDO + MySQL.

## Struktur Folder
```
crud-inventaris/
├── config/
│   └── database.php        # Koneksi PDO Singleton
├── includes/
│   └── functions.php       # Helper: flash message, htmlspecialchars, format rupiah
├── process/
│   ├── create_process.php  # Proses simpan produk baru
│   ├── update_process.php  # Proses update produk
│   └── export.php          # Bonus: export laporan CSV
├── assets/
│   └── style.css           # Styling UI (tema gelap)
├── sql/
│   └── inventaris_db.sql   # Struktur database + data seed
├── index.php                # Halaman list produk (JOIN, search, pagination)
├── create.php                # Form tambah produk
├── update.php                # Form edit produk (pre-filled)
└── delete.php                # Proses hapus (dengan transaction + log aktivitas)
```

## Cara Menjalankan (XAMPP / Laragon)

1. **Copy folder** `crud-inventaris` ke dalam `htdocs` (XAMPP) atau `www` (Laragon).

2. **Import database**:
   - Buka phpMyAdmin → tab **Import** → pilih file `sql/inventaris_db.sql`.
   - Atau lewat terminal:
     ```
     mysql -u root -p < sql/inventaris_db.sql
     ```
   - Ini akan otomatis membuat database `inventaris_db`, 3 tabel utama
     (`kategori`, `supplier`, `produk` dengan foreign key), 1 tabel bonus
     (`log_aktivitas`), dan mengisi masing-masing tabel dengan 5 data seed.

3. **Cek kredensial database** di `config/database.php` (default XAMPP: user `root`, password kosong — biasanya tidak perlu diubah).

4. **Buka di browser**:
   ```
   http://localhost/crud-inventaris/
   ```

## Requirement Checklist

| # | Requirement | Status |
|---|---|---|
| 1 | Database `inventaris_db` dengan 3 tabel + FK |  `kategori`, `supplier`, `produk` (FK ke keduanya) |
| 2 | Minimal 5 data seed per tabel | Masing-masing tabel 5 baris |
| 3 | Koneksi PDO dengan Singleton pattern | `config/database.php` |
| 4 | Halaman list produk dengan JOIN 2 tabel | `index.php` (JOIN kategori & supplier) |
| 5 | Form create dengan dropdown kategori & supplier | `create.php` |
| 6 | Fitur update (form pre-filled) & delete (konfirmasi) | `update.php`, konfirmasi JS di `index.php`, proses di `delete.php` |
| 7 | Semua query input pakai prepared statements | Semua query pakai `$pdo->prepare()` |
| 8 | Output HTML pakai `htmlspecialchars()` | Fungsi `h()` di `includes/functions.php`, dipakai di semua output |
| 9 | Flash message sukses/gagal (redirect pattern) | `setFlash()` / `getFlash()` + `header('Location: ...')` |
| 10 | UI rapi | `assets/style.css` tema gelap dengan aksen pink/hijau |

### Bonus yang diimplementasikan
- **Transaction pada delete** — `delete.php` memakai `beginTransaction()` / `commit()` / `rollBack()`,
  sekaligus mencatat setiap penghapusan ke tabel `log_aktivitas`.
- **Fitur pencarian** — kotak cari di `index.php` (`LIKE` dengan prepared statement).
- **Pagination** — 5 produk per halaman, memakai `LIMIT`/`OFFSET`.
- **Export laporan** — tombol "Export CSV" di `index.php` → `process/export.php`.

## Catatan Keamanan
- Semua query yang melibatkan input user (create, update, delete, search) menggunakan
  **prepared statements** dengan parameter binding — **tidak ada** string concatenation
  langsung ke SQL, sehingga aman dari SQL Injection.
- Semua output ke halaman HTML dibungkus fungsi `h()` (wrapper `htmlspecialchars()`)
  untuk mencegah XSS.
- Constraint FK (`ON DELETE RESTRICT`) mencegah kategori/supplier terhapus selama masih
  dipakai oleh produk — jaga integritas data.


## ScreenShoot 

![Gambar](Gambar/Screenshot%201.png)
![Gambar](Gambar/Screenshot%202.png)
![Gambar](Gambar/Screenshot%203.png)
