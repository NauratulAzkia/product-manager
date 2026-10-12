<?php
/**
 * Product Manager - Tambah Produk Baru
 * Menerapkan: Form Input, Validasi Server-Side, Cek Duplikasi, PRG (Post-Redirect-Get)
 */

session_start();
require_once __DIR__ . "/../config/db.php";

$formData = [
    'name'     => '',
    'category' => 'Kopi Robusta',
    'price'    => '',
    'stock'    => '0'
];

$validationErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normalisasi input
    $formData['name']     = trim($_POST['name'] ?? '');
    $formData['category'] = trim($_POST['category'] ?? 'Umum');
    $rawPrice             = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $rawStock             = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    // 1. Validasi Nama Produk (Slide 6 & 17: Minimal 3 karakter)
    if (mb_strlen($formData['name']) < 3) {
        $validationErrors['name'] = 'Nama produk wajib diisi minimal 3 karakter.';
    }

    // 2. Validasi Kategori
    if ($formData['category'] === '') {
        $formData['category'] = 'Umum';
    }

    // 3. Validasi Harga (Slide 6 & 17: Harus lebih dari 0)
    if ($rawPrice === false || $rawPrice <= 0) {
        $validationErrors['price'] = 'Harga harus berupa angka lebih besar dari 0.';
    } else {
        $formData['price'] = $rawPrice;
    }

    // 4. Validasi Stok (Slide 6 & 17: Tidak boleh negatif)
    if ($rawStock === false || $rawStock < 0) {
        $validationErrors['stock'] = 'Jumlah stok tidak boleh berupa nilai negatif.';
    } else {
        $formData['stock'] = $rawStock;
    }

    // 5. Cek Keunikan Nama Produk (Slide 17: nama unik)
    if (empty($validationErrors['name'])) {
        $checkStmt = $pdo->prepare("SELECT id FROM products WHERE name = :name LIMIT 1");
        $checkStmt->execute(['name' => $formData['name']]);
        if ($checkStmt->fetch()) {
            $validationErrors['name'] = 'Nama produk sudah terdaftar. Gunakan nama lain.';
        }
    }

    // 6. Eksekusi Simpan & Pola PRG (Slide 7)
    if (empty($validationErrors)) {
        $insertStmt = $pdo->prepare(
            "INSERT INTO products (name, category, price, stock) 
             VALUES (:name, :category, :price, :stock)"
        );

        $insertStmt->execute([
            'name'     => $formData['name'],
            'category' => $formData['category'],
            'price'    => $formData['price'],
            'stock'    => $formData['stock']
        ]);

        // Post-Redirect-Get: Mencegah duplikasi data saat refresh browser
        header("Location: index.php?status=created");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<main class="app-container-narrow">

    <div class="form-card">
        <header class="form-header">
            <h2>Tambah Produk Baru</h2>
            <p>Masukkan rincian informasi produk ke dalam katalog inventaris.</p>
        </header>

        <form action="create.php" method="POST" novalidate>

            <!-- Input Nama Produk -->
            <div class="form-group">
                <label for="product_name">Nama Produk *</label>
                <input 
                    type="text" 
                    id="product_name" 
                    name="name" 
                    class="form-control <?= isset($validationErrors['name']) ? 'is-invalid' : '' ?>" 
                    placeholder="Contoh: Bubuk Kopi Robusta Temanggung 500gr"
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
                    placeholder="Contoh: Kopi Robusta / Kopi Arabika / Biji Kopi"
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
                    placeholder="Contoh: 100000"
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
                    placeholder="0"
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
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>

        </form>
    </div>

</main>

</body>
</html>