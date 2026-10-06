<?php
/**
 * Database.php
 * Koneksi PDO ke MySQL menggunakan Singleton Design Pattern.
 * Menjamin hanya ada SATU instance koneksi database di seluruh aplikasi.
 */

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    // --- Ubah kredensial ini sesuai environment kamu (XAMPP/Laragon default) ---
    private const HOST    = 'localhost';
    private const DBNAME  = 'inventaris_db';
    private const USER    = 'root';
    private const PASS    = '';
    private const CHARSET = 'utf8mb4';

    // Constructor private -> tidak bisa di-instantiate dari luar class (new Database() dilarang)
    private function __construct()
    {
        $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DBNAME . ";charset=" . self::CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // pakai native prepared statement
        ];

        try {
            $this->connection = new PDO($dsn, self::USER, self::PASS, $options);
        } catch (PDOException $e) {
            // Jangan tampilkan detail sensitif di production, di sini untuk keperluan tugas
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    // Cegah cloning object (bagian penting Singleton pattern)
    private function __clone() {}

    // Cegah unserialize membuat instance baru
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    /**
     * Titik akses tunggal ke instance Database.
     * Jika belum ada instance, buat baru. Jika sudah ada, kembalikan yang lama.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
