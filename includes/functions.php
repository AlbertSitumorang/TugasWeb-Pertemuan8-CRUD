<?php
/**
 * functions.php
 * Kumpulan helper function: flash message & output sanitizer.
 */

/**
 * Simpan flash message ke session (dipakai bersama pola redirect).
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type, // 'success' atau 'error'
        'message' => $message,
    ];
}

/**
 * Ambil flash message lalu hapus dari session (hanya tampil sekali).
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Wrapper htmlspecialchars() singkat untuk semua output ke HTML.
 * Mencegah XSS pada data yang berasal dari database / input user.
 */
function h(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format angka ke Rupiah, contoh: 150000 -> Rp 150.000
 */
function rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}
