<?php
/**
 * Product Manager - Hapus Produk
 * Menerapkan: Pembatasan Method POST, Validasi CSRF Token (hash_equals), Prepared Statement DELETE & PRG
 */

session_start();
require_once __DIR__ . "/../config/db.php";

// 1. Tolak Request Non-POST (Slide 12: Delete hanya via POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// 2. Verifikasi Keamanan CSRF Token (Slide 13)
$submittedCsrf = $_POST['csrf'] ?? '';
$sessionCsrf   = $_SESSION['csrf'] ?? '';

if (empty($sessionCsrf) || !hash_equals($sessionCsrf, $submittedCsrf)) {
    http_response_code(403);
    exit("Akses ditolak: Token CSRF tidak valid atau telah kedaluwarsa.");
}

// 3. Validasi Parameter ID Produk (Slide 12)
$productId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$productId || $productId <= 0) {
    header("Location: index.php");
    exit;
}

// 4. Eksekusi Hapus dengan Prepared Statement
$deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$deleteStmt->execute(['id' => $productId]);

// 5. Pola Post-Redirect-Get (PRG)
header("Location: index.php?status=deleted");
exit;