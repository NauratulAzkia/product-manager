<?php
/**
 * Konfigurasi Database PDO & Sesi Aplikasi
 * Modul: Pemrograman Web - Integrasi PHP & MySQL
 */

$dbHost = 'localhost';
$dbName = 'store_db';
$dbUser = 'root';
$dbPass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
} catch (PDOException $err) {
    http_response_code(500);
    exit("Gagal terhubung ke database: " . htmlspecialchars($err->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Inisialisasi sesi untuk penanganan CSRF & pesan flash
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Token proteksi CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
