<?php
/**
 * Product Manager - Halaman Utama (Katalog & Inventaris)
 * Mengimplementasikan: READ data, Filter/Pencarian GET, Proteksi XSS & CSRF
 */

session_start();
require_once __DIR__ . "/../config/db.php";

$searchKeyword = trim($_GET['q'] ?? '');
$actionStatus  = trim($_GET['status'] ?? '');

// 1. Eksekusi Query Produk (Mendukung Fitur Bonus Search Sesuai Slide 19)
if ($searchKeyword !== '') {
    $sql = "SELECT id, name, category, price, stock 
            FROM products 
            WHERE name LIKE :kw_name OR category LIKE :kw_cat 
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $param = '%' . $searchKeyword . '%';
    $stmt->execute([
        'kw_name' => $param,
        'kw_cat'  => $param
    ]);
} else {
    $sql = "SELECT id, name, category, price, stock 
            FROM products 
            ORDER BY id DESC";
    $stmt = $pdo->query($sql);
}

$productList = $stmt->fetchAll();
$totalItems  = count($productList);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<main class="app-container">

    <!-- Header Terpusat Sesuai Permintaan Revisi -->
    <header class="hero-header">
        <span class="header-badge">Inventaris Toko</span>
        <h1 class="hero-title">Product Manager</h1>
        <div class="hero-actions">
            <a href="create.php" class="btn btn-primary">+ Tambah Produk Baru</a>
        </div>
    </header>

    <!-- Notifikasi Flash Message (Pola Post-Redirect-Get) -->
    <?php if ($actionStatus === 'created'): ?>
        <div class="alert-banner alert-success" id="flash-alert" role="alert">
            <span>✓ Produk baru berhasil ditambahkan ke inventaris.</span>
        </div>
    <?php elseif ($actionStatus === 'updated'): ?>
        <div class="alert-banner alert-success" id="flash-alert" role="alert">
            <span>✓ Perubahan data produk berhasil disimpan.</span>
        </div>
    <?php elseif ($actionStatus === 'deleted'): ?>
        <div class="alert-banner alert-success" id="flash-alert" role="alert">
            <span>✓ Produk berhasil dihapus dari inventaris.</span>
        </div>
    <?php endif; ?>

    <!-- Toolbar: Form Pencarian & Total Data -->
    <section class="toolbar-panel">
        <form action="index.php" method="GET" class="search-form">
            <div class="search-input-wrapper">
                <input 
                    type="text" 
                    name="q" 
                    class="search-input" 
                    placeholder="Cari berdasarkan nama atau kategori kopi..."
                    value="<?= htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>
            <button type="submit" class="btn btn-outline">Cari</button>
            <?php if ($searchKeyword !== ''): ?>
                <a href="index.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </form>

        <div class="toolbar-counter">
            Total Produk: <strong><?= (int)$totalItems ?></strong> item
        </div>
    </section>

    <!-- Daftar Produk: Grid Flexbox Responsif (Slide 15) -->
    <section class="products-grid">
        <?php if (empty($productList)): ?>
            <div class="empty-box">
                <div class="empty-icon">📦</div>
                <h2 class="empty-title">Tidak Ada Produk Ditemukan</h2>
                <p class="empty-desc">
                    <?= $searchKeyword !== '' ? 'Tidak ada hasil untuk kata kunci "' . htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8') . '".' : 'Belum ada produk dalam database.' ?>
                </p>
                <a href="create.php" class="btn btn-primary">+ Tambah Produk Sekarang</a>
            </div>
        <?php else: ?>
            <?php foreach ($productList as $item): ?>
                <article class="product-card">
                    <div>
                        <div class="card-top">
                            <span class="badge-category">
                                <?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <?php 
                                $currentStock = (int)$item['stock'];
                                $stockDotClass = ($currentStock === 0) ? 'danger' : (($currentStock <= 5) ? 'warning' : '');
                            ?>
                            <span class="stock-indicator">
                                <span class="stock-dot <?= $stockDotClass ?>"></span>
                                Stok: <?= $currentStock ?>
                            </span>
                        </div>

                        <!-- Escape Output HTML Mencegah XSS (Slide 10) -->
                        <h2 class="product-title">
                            <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                    </div>

                    <div>
                        <div class="card-meta">
                            <span class="meta-label">Harga Satuan</span>
                            <p class="product-price">
                                Rp <?= number_format((float)$item['price'], 0, ',', '.') ?>
                            </p>
                        </div>

                        <div class="card-actions">
                            <a href="edit.php?id=<?= (int)$item['id'] ?>" class="btn btn-outline btn-sm">
                                ✎ Edit
                            </a>

                            <!-- Form Hapus dengan Proteksi CSRF (Slide 13) -->
                            <form action="delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    🗑 Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

<script>
// Auto-dismiss notifikasi flash message setelah 4 detik
const flashAlert = document.getElementById('flash-alert');
if (flashAlert) {
    setTimeout(() => {
        flashAlert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        flashAlert.style.opacity = '0';
        flashAlert.style.transform = 'translateY(-6px)';
        setTimeout(() => flashAlert.remove(), 400);
    }, 4000);
}
</script>

</body>
</html>