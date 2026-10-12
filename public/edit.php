<?php
/**
 * Product Manager - Edit Data Produk
 * Menerapkan: Ambil Data Berdasarkan ID, Validasi Server-Side, Update Prepared Statement, PRG
 */

session_start();
require_once __DIR__ . "/../config/db.php";

// 1. Validasi ID Produk dari Parameter URL (Slide 12)
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$productId || $productId <= 0) {
    header("Location: index.php");
    exit;
}

// 2. Ambil Data Produk Terkini dari Database
$fetchStmt = $pdo->prepare("SELECT id, name, category, price, stock FROM products WHERE id = :id LIMIT 1");
$fetchStmt->execute(['id' => $productId]);
$existingProduct = $fetchStmt->fetch();

if (!$existingProduct) {
    // Jika ID tidak ditemukan, redirect aman ke index (Slide 12)
    header("Location: index.php");
    exit;
}

$formData = [
    'name'     => $existingProduct['name'],
    'category' => $existingProduct['category'],
    'price'    => $existingProduct['price'],
    'stock'    => $existingProduct['stock']
];

$validationErrors = [];

// 3. Tangani Pengiriman Form (Method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name']     = trim($_POST['name'] ?? '');
    $formData['category'] = trim($_POST['category'] ?? 'Umum');
    $rawPrice             = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $rawStock             = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    // Validasi Nama Produk
    if (mb_strlen($formData['name']) < 3) {
        $validationErrors['name'] = 'Nama produk minimal 3 karakter.';
    }

    // Validasi Kategori
    if ($formData['category'] === '') {
        $formData['category'] = 'Umum';
    }

    // Validasi Nilai Harga
    if ($rawPrice === false || $rawPrice <= 0) {
        $validationErrors['price'] = 'Harga harus lebih besar dari 0.';
    } else {
        $formData['price'] = $rawPrice;
    }

    // Validasi Jumlah Stok
    if ($rawStock === false || $rawStock < 0) {
        $validationErrors['stock'] = 'Stok tidak boleh negatif.';
    } else {
        $formData['stock'] = $rawStock;
    }

    // Eksekusi Update Jika Lolos Validasi (Slide 12)
    if (empty($validationErrors)) {
        $updateStmt = $pdo->prepare(
            "UPDATE products 
             SET name = :name, category = :category, price = :price, stock = :stock 
             WHERE id = :id"
        );

        $updateStmt->execute([
            'name'     => $formData['name'],
            'category' => $formData['category'],
            'price'    => $formData['price'],
            'stock'    => $formData['stock'],
            'id'       => $productId
        ]);

        // Post-Redirect-Get (PRG)
        header("Location: index.php?status=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<main class="app-container-narrow">

    <div class="form-card">
        <header class="form-header">
            <h2>Edit Produk</h2>
            <p>Perbarui rincian informasi produk yang tersimpan di database.</p>
        </header>

        <form action="edit.php?id=<?= (int)$productId ?>" method="POST" novalidate>

            <!-- Input Nama Produk -->
            <div class="form-group">
                <label for="product_name">Nama Produk *</label>
                <input 
                    type="text" 
                    id="product_name" 
                    name="name" 
                    class="form-control <?= isset($validationErrors['name']) ? 'is-invalid' : '' ?>" 
                    value="<?= htmlspecialchars($formData['name'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if (isset($validationErrors['name'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($validationErrors['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <!-- Input Kategori -->
            <div class="form-group">
                <label for="product_category">Kategori Produk</label>
                <input 
                    type="text" 
                    id="product_category" 
                    name="category" 
                    class="form-control" 
                    value="<?= htmlspecialchars($formData['category'], ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <!-- Input Harga -->
            <div class="form-group">
                <label for="product_price">Harga (Rupiah) *</label>
                <input 
                    type="number" 
                    id="product_price" 
                    name="price" 
                    class="form-control <?= isset($validationErrors['price']) ? 'is-invalid' : '' ?>" 
                    step="500" 
                    min="1"
                    value="<?= htmlspecialchars((string)$formData['price'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if (isset($validationErrors['price'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($validationErrors['price'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <!-- Input Stok -->
            <div class="form-group">
                <label for="product_stock">Jumlah Stok *</label>
                <input 
                    type="number" 
                    id="product_stock" 
                    name="stock" 
                    class="form-control <?= isset($validationErrors['stock']) ? 'is-invalid' : '' ?>" 
                    min="0"
                    value="<?= htmlspecialchars((string)$formData['stock'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if (isset($validationErrors['stock'])): ?>
                    <span class="invalid-feedback"><?= htmlspecialchars($validationErrors['stock'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <!-- Tombol Aksi -->
            <div class="form-actions">
                <a href="index.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>

        </form>
    </div>

</main>

</body>
</html>