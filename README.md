# Product Manager — Katalog & Inventaris Barang

Aplikasi web manajemen katalog dan inventaris produk berbasis PHP, MySQL, dan CSS.

# Teknologi yang digunakan
- Bahasa Pemrograman: PHP
- Basis Data: MySQL / PDO (`store_db`)
- Keamanan: Prepared Statement, Sanitasi Output `htmlspecialchars`, Token CSRF `hash_equals`, dan Pola PRG (*Post-Redirect-Get*)
- Desain: HTML, CSS & Flexbox Responsif

---

# Struktur File Proyek

```text
product-manager/
├── config/
│   └── db.php                  # Koneksi PDO aman & inisialisasi sesi CSRF
├── database/
│   └── store_db.sql            # Skema tabel products & seed data kopi
├── public/
│   ├── assets/
│   │   └── style.css           # Styling Box Model, Flexbox & komponen UI
│   ├── index.php               # Halaman utama (READ + fitur pencarian GET)
│   ├── create.php              # Form penambahan produk (CREATE + PRG)
│   ├── edit.php                # Form ubah data produk (UPDATE + PRG)
│   └── delete.php              # Endpoint hapus produk (DELETE + CSRF)
└── README.md                   # Petunjuk instalasi dan dokumentasi teknis
```

---

# Panduan Menjalankan Aplikasi
Menggunakan XAMPP / Laragon:

1. Pindahkan folder proyek ini ke direktori web server:
   - XAMPP: C:/xampp/htdocs/
   - Jalankan Apache & MySQL dari panel kontrol.

2. Salin Folder Proyek:
   - Pindahkan/salin folder `product-manager` ke dalam direktori `htdocs` XAMPP.

3. Akses Aplikasi:
   - Buka alamat URL berikut di browser:
     (http://localhost/product-manager/public/)

---
