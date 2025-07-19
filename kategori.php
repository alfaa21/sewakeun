<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

// Ambil semua kategori
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
$kategori_list = [];
while ($k = mysqli_fetch_assoc($kategori_query)) {
    $kategori_list[] = $k;
}

// Ambil kategori aktif dari GET
$kategori_id = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

// Ambil parameter search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query produk berdasarkan kategori dan search
$where_conditions = [];
if ($kategori_id > 0) {
    $where_conditions[] = "kategori_id = $kategori_id";
}
if (!empty($search)) {
    $where_conditions[] = "p.nama LIKE '%$search%'";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$produk_query = mysqli_query($conn, "SELECT p.*, l.nama AS lokasi FROM produk p JOIN lokasi l ON p.lokasi_id = l.id $where_clause ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk Berdasarkan Kategori</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- PROMO CARDS RECTANGULAR --- */
.promo-cards {
    display: flex;
    gap: 2rem;
    margin-bottom: 3rem;
    justify-content: center;
    align-items: stretch;
    flex-wrap: nowrap;
    margin-top: 2.5rem;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
}
.promo-card-modern {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    background: #fff;
    border-radius: 0;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    overflow: hidden;
    min-height: 160px;
    border: none;
    position: relative;
    width: 100%;
    flex: 1 1 0;
    max-width: 520px;
    margin: 0;
    height: 170px;
    transition: box-shadow 0.2s, transform 0.2s;
}
.promo-card-modern:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.16);
    transform: translateY(-4px) scale(1.02);
}
.promo-card-modern .promo-img {
    width: 120px;
    height: 100%;
    object-fit: cover;
    background: #f8f9fa;
    display: block;
    border-radius: 0;
}
.promo-card-modern .promo-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    font-size: 0.9rem;
    font-weight: 700;
    padding: 0.2em 0.8em;
    border-radius: 1em;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}
.promo-card-modern .promo-content {
    flex: 1 1 0;
    padding: 0.8rem 0.7rem 0.7rem 0.7rem;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    min-width: 0;
    word-break: break-word;
    overflow-wrap: break-word;
}
.promo-card-modern .promo-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
    color: #222;
}
.promo-card-modern .promo-desc {
    font-size: 0.88rem;
    color: #444;
    margin-bottom: 0.7rem;
}
.promo-card-modern .promo-btn {
    width: fit-content;
    padding: 0.35rem 0.9rem;
    font-size: 0.92rem;
    border-radius: 6px;
    font-weight: 600;
    border: none;
    background: #007bff;
    color: #fff;
    transition: background 0.2s;
    margin-top: auto;
}
.promo-card-modern .promo-btn:hover {
    background: #0056b3;
}
.promo-card-modern.bg-dark {
    background: #23272b;
}
.promo-card-modern.bg-dark .promo-btn {
    background: #ffc107;
    color: #222;
}
.promo-card-modern.bg-dark .promo-btn:hover {
    background: #e6a800;
}
.promo-card-modern.bg-dark .promo-title,
.promo-card-modern.bg-dark .promo-desc {
    color: #fff;
}
.promo-card-modern.bg-dark .promo-badge {
    background: #ffc107;
    color: #222;
}
.promo-card-modern .promo-badge {
    /* badge warna default merah */
    background: #ff1744;
    color: #fff;
}
.promo-card-modern[style*='background:#ffe082;'] .promo-badge {
    background: #ff9800;
    color: #fff;
}
@media (max-width: 1200px) {
    .promo-cards {
        gap: 1.2rem;
        max-width: 98vw;
    }
    .promo-card-modern {
        max-width: 98vw;
        min-height: 120px;
        height: 130px;
    }
    .promo-card-modern .promo-img {
        width: 80px;
    }
}
@media (max-width: 900px) {
    .promo-cards {
        flex-direction: column;
        gap: 1.2rem;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1.2rem;
    }
    .promo-card-modern {
        max-width: 98vw;
        min-height: unset;
        height: auto;
        flex-direction: column;
    }
    .promo-card-modern .promo-img {
        width: 100%;
        height: 90px;
        border-radius: 0;
    }
}

/* SECTION KATEGORI PILIHAN */
.kategori-section {
    margin: 2.5rem auto 2.5rem auto;
    max-width: 1000px;
    padding: 0 1rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.kategori-section h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 3rem; /* Menambahkan margin bawah */
    text-align: center;
    width: 100%;
}
.kategori-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.kategori-card {
    cursor: pointer;
    transition: box-shadow 0.18s, border 0.18s;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    border: 1.5px solid #e0e0e0;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 1.1rem 1.2rem 0.7rem 1.2rem;
    min-width: 120px;
    max-width: 140px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    text-decoration: none;
    color: inherit;
}
.kategori-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.13);
    border: 1.5px solid #007bff;
    color: #007bff;
}
.kategori-card .kategori-icon {
    font-size: 2.5rem;
    color: #007bff;
    margin-bottom: 0.7rem;
    background: #f6f8fa;
    border-radius: 12px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.kategori-card .kategori-nama {
    font-size: 1.08rem;
    font-weight: 600;
    text-align: center;
    margin-bottom: 0.1rem;
    color: #222;
}
.kategori-card .kategori-sub {
    font-size: 0.95rem;
    color: #888;
    text-align: center;
}
@media (max-width: 900px) {
    .kategori-grid {
        gap: 0.7rem;
    }
    .kategori-card {
        min-width: 90px;
        max-width: 100px;
        padding: 0.7rem 0.5rem 0.5rem 0.5rem;
    }
    .kategori-card .kategori-icon {
        font-size: 1.5rem;
        width: 40px;
        height: 40px;
    }
}
.product-list-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 3rem; /* Margin bawah sedikit lebih besar */
    text-align: center;
    width: 100%;
}

/* Product Grid Styles (Modern & Refined) */
.product-list {
    margin-bottom: 3rem;
    padding: 0 1rem;
}

.product-grid-5-col {
    display: flex;
    flex-wrap: wrap;
    gap: 1.2rem; /* Jarak antar card */
    justify-content: center;
    margin: 0 auto;
    max-width: 1800px; /* Lebar maksimal untuk 6 kolom (disesuaikan) */
}

.product-card-item {
    flex: 0 0 calc(16.666% - 1rem); /* 6 kolom dengan gap */
    max-width: calc(16.666% - 1rem);
    background: #fff;
    border-radius: 10px; /* Sudut membulat modern di atas */
    box-shadow: 0 6px 20px rgba(0,0,0,0.08); /* Bayangan lebih modern */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: auto; /* Tinggi menyesuaikan konten */
    display: flex;
    flex-direction: column;
    overflow: hidden;
    text-decoration: none; /* Hilangkan underline pada link card */
    color: inherit;
}

.product-card-item:hover {
    transform: translateY(-6px); /* Sedikit naik saat hover */
    box-shadow: 0 10px 35px rgba(0,0,0,0.18) !important; /* Bayangan lebih kuat saat hover */
}

.product-card-item .product-img-wrap {
    width: 100%;
    aspect-ratio: 1 / 0.8; /* Rasio aspek gambar 1:0.8 (lebih lebar) */
    background: #f0f2f5; /* Background abu-abu muda untuk gambar */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 10px 10px 0 0; /* Sudut atas membulat, bawah tajam */
}

.product-card-item .img-fluid {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Gambar mengisi penuh area, cropping jika perlu */
    transition: transform 0.3s ease; /* Transisi halus untuk zoom gambar */
}

.product-card-item:hover .img-fluid {
    transform: scale(1.08); /* Zoom gambar sedikit saat hover */
}

.product-card-item .card-body {
    padding: 0.8rem 0.9rem 0.9rem 0.9rem; /* Padding konten disesuaikan */
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Untuk mendorong tombol ke bawah */
}

.product-card-item .card-title {
    font-size: 0.95rem; /* Ukuran font judul disesuaikan */
    font-weight: 600;
    margin-bottom: 0.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #333;
}

.product-card-item .product-price {
    font-size: 0.9rem; /* Ukuran font harga disesuaikan */
    font-weight: 700;
    color: #007bff; /* Warna biru harga */
    margin-bottom: 0.4rem;
}

.product-card-item .product-rating {
    font-size: 0.82rem; /* Ukuran font rating disesuaikan */
    margin-bottom: 0.4rem;
}

.product-card-item .product-location,
.product-card-item .product-extra {
    font-size: 0.82rem; /* Ukuran font lokasi/stok disesuaikan */
    color: #777;
    margin-bottom: 0.2rem;
}

.product-card-item .d-flex.flex-row.gap-2.w-100 {
    margin-top: auto; /* Dorong tombol ke bawah */
    padding-top: 0.6rem; /* Padding atas tombol disesuaikan */
    border-top: 1px solid #f0f0f0; /* Garis pemisah tombol */
}

.product-card-item .btn {
    font-size: 0.62rem; /* Ukuran tombol disesuaikan */
    padding: 0.1rem 1rem;
    border-radius: 6px; /* Sudut tombol membulat */
    font-weight: 600;
}

/* Responsif */
/* 4 Kolom */
@media (max-width: 1600px) {
    .product-grid-5-col {
        gap: 1rem;
        max-width: 1400px; /* Max width untuk 5 kolom */
    }
    .product-card-item {
        flex: 0 0 calc(20% - 0.8rem); /* 5 kolom */
        max-width: calc(20% - 0.8rem);
    }
}

/* 3 Kolom */
@media (max-width: 1200px) {
    .product-grid-5-col {
        gap: 1rem;
        max-width: 1000px; /* Max width untuk 4 kolom */
    }
    .product-card-item {
        flex: 0 0 calc(25% - 0.75rem); /* 4 kolom */
        max-width: calc(25% - 0.75rem);
    }
}

/* 2 Kolom */
@media (max-width: 992px) {
    .product-grid-5-col {
        gap: 1rem;
        max-width: 98vw;
    }
    .product-card-item {
        flex: 0 0 calc(33.333% - 0.66rem); /* 3 kolom */
        max-width: calc(33.333% - 0.66rem);
    }
}

/* 1 Kolom */
@media (max-width: 768px) {
    .product-grid-5-col {
        gap: 0.8rem;
    }
    .product-card-item {
        flex: 0 0 calc(50% - 0.4rem); /* 2 kolom */
        max-width: calc(50% - 0.4rem);
    }
}

/* Mobile (single column) */
@media (max-width: 576px) {
    .product-grid-5-col {
        gap: 1rem;
    }
    .product-card-item {
        flex: 0 0 calc(100% - 0.5rem); /* 1 kolom */
        max-width: calc(100% - 0.5rem);
    }
    .product-card-item .card-title {
        font-size: 1rem;
    }
    .product-card-item .product-price {
        font-size: 0.9rem;
    }
    .product-card-item .product-rating,
    .product-card-item .product-location,
    .product-card-item .product-extra {
        font-size: 0.85rem;
    }
}

.product-card-link {
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    text-decoration: none;
    color: inherit;
}
        body { background: #f6f8fa; }
        .kategori-page-container { display: flex; gap: 32px; max-width: 1800px; margin: 0 auto; padding: 2rem 1rem; }
        .sidebar-kategori { width: 260px; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 2rem 1.2rem; height: fit-content; }
        .sidebar-kategori h4 { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; }
        .sidebar-kategori ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-kategori li { margin-bottom: 0.7rem; }
        .sidebar-kategori a { color: #333; text-decoration: none; font-weight: 500; transition: color 0.18s; }
        .sidebar-kategori a.active, .sidebar-kategori a:hover { color: #007bff; font-weight: 700; }
        .kategori-product-main { flex: 1; }
        .product-list-title { font-size: 2rem; font-weight: 700; margin-bottom: 2.2rem; text-align: left; }
        
        /* Search Form Styles */
        .search-container {
            background: transparent;
            min-width: 300px;
        }
        
        .search-form .input-group {
            width: 100%;
        }
        
        .search-input {
            height: 40px;
            font-size: 14px !important;
            background: #fff;
        }
        
        .search-input:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
        }
        
        .search-btn {
            height: 40px;
            padding: 0 16px !important;
        }
        
        .search-btn:hover {
            background: #0056b3 !important;
            border-color: #0056b3 !important;
        }
        
        .search-btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        
        @media (max-width: 768px) {
            .search-container {
                min-width: 200px;
            }
            .product-list-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
                gap: 1rem;
            }
            .search-container {
                min-width: 100%;
            }
            .d-flex.align-items-center.gap-3 {
                width: 100%;
            }
        }
        
        /* No Results Styles */
        .no-results-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid #e0e0e0;
            margin: 2rem 0;
        }
        
        .no-results-container h4 {
            color: #666;
            font-weight: 600;
        }
        
        .no-results-container a {
            color: #007bff;
        }
        
        .no-results-container a:hover {
            color: #0056b3;
        }
        
        /* Search Results Info */
        .search-results-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        
        .search-results-info small {
            color: #1976d2;
            font-weight: 500;
        }
    </style>
</head>
<body>
<?php include 'includes/_header.php'; ?>
<div class="kategori-page-container">
    <!-- Sidebar Kategori -->
    <aside class="sidebar-kategori">
        <h4>Kategori</h4>
        <ul>
            <?php foreach ($kategori_list as $kat): ?>
                <li>
                    <a href="kategori.php?cat=<?= $kat['id'] ?>" class="<?= $kategori_id == $kat['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($kat['nama']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
    <!-- Daftar Produk -->
    <main class="kategori-product-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="product-list-title">
                <?php
                $nama_kat = '';
                foreach ($kategori_list as $kat) {
                    if ($kat['id'] == $kategori_id) $nama_kat = $kat['nama'];
                }
                echo $nama_kat ? 'Produk Kategori: ' . htmlspecialchars($nama_kat) : 'Pilih Kategori';
                ?>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Search Form -->
                <div class="search-container">
                    <form method="GET" class="search-form m-0">
                        <input type="hidden" name="cat" value="<?= $kategori_id ?>">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control search-input" 
                                   placeholder="Cari produk..." 
                                   value="<?= htmlspecialchars($search) ?>"
                                   style="border-radius: 8px 0 0 8px; border: 2px solid #e0e0e0; padding: 8px 16px; font-size: 14px;">
                            <button type="submit" class="btn btn-primary search-btn" 
                                    style="border-radius: 0 8px 8px 0; padding: 8px 16px; border: 2px solid #007bff; background: #007bff;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <a href="index.php" class="btn btn-light" title="Kembali ke Beranda">
                    <i class="fas fa-home"></i>
                </a>
            </div>
        </div>
        
        <?php if (!empty($search)): ?>
            <div class="mb-3">
                <small class="text-muted">
                    Hasil pencarian untuk: "<strong><?= htmlspecialchars($search) ?></strong>"
                    <a href="kategori.php?cat=<?= $kategori_id ?>" class="text-decoration-none ms-2">
                        <i class="fas fa-times"></i> Hapus filter
                    </a>
                </small>
            </div>
        <?php endif; ?>
        
        <?php 
        $total_products = mysqli_num_rows($produk_query);
        if ($total_products > 0 && !empty($search)): 
        ?>
            <div class="search-results-info mb-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Ditemukan <?= $total_products ?> produk untuk pencarian "<?= htmlspecialchars($search) ?>"
                </small>
            </div>
        <?php endif; ?>
        
        <?php 
        if ($total_products == 0): 
        ?>
            <div class="no-results-container text-center py-5">
                <div class="no-results-icon mb-3">
                    <i class="fas fa-search" style="font-size: 4rem; color: #ccc;"></i>
                </div>
                <h4 class="text-muted mb-2">
                    <?php if (!empty($search)): ?>
                        Tidak ada produk yang ditemukan untuk "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Tidak ada produk dalam kategori ini
                    <?php endif; ?>
                </h4>
                <p class="text-muted">
                    <?php if (!empty($search)): ?>
                        Coba kata kunci lain atau <a href="kategori.php?cat=<?= $kategori_id ?>" class="text-decoration-none">hapus filter pencarian</a>
                    <?php else: ?>
                        Silakan pilih kategori lain atau kembali ke <a href="index.php" class="text-decoration-none">beranda</a>
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
        <div class="product-grid-5-col">
            <?php while ($p = mysqli_fetch_assoc($produk_query)): ?>
                <div class="product-card-item">
                    <a href="produk_detail.php?id=<?= $p['id'] ?>&cat=<?= $kategori_id ?>" class="product-card-link">
                        <div class="product-img-wrap">
                            <img src="<?= htmlspecialchars($p['gambar']) ?>" class="img-fluid" alt="<?= htmlspecialchars($p['nama']) ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" title="<?= htmlspecialchars($p['nama']) ?>"><?= htmlspecialchars($p['nama']) ?></h5>
                            <p class="product-price" style="color:#007bff;font-weight:700;">Rp <?= number_format($p['harga'],0,',','.') ?>/hari</p>
                            <div class="product-rating">
                            <?php
                                $product_id = $p['id'];
                                $review_stat = mysqli_query($conn, "SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM reviews WHERE product_id=$product_id");
                                $stat = mysqli_fetch_assoc($review_stat);
                                $total_review = (int)($stat['total'] ?? 0);
                                $avg_rating = round($stat['avg_rating'] ?? 0, 1);
                                if ($total_review > 0) {
                                    $full_star = floor($avg_rating);
                                    $half_star = ($avg_rating - $full_star) >= 0.5 ? 1 : 0;
                                    $empty_star = 5 - $full_star - $half_star;
                                    for ($i = 0; $i < $full_star; $i++) echo '<i class="fas fa-star text-warning"></i>';
                                    if ($half_star) echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                    for ($i = $full_star + $half_star; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
                                    echo " <span class=\"text-muted small\">($total_review Ulasan, $avg_rating/5)</span>";
                                } else {
                                    for ($i = 0; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
                                    echo ' <span class="text-muted small">0 Ulasan</span>';
                                }
                            ?>
                            </div>
                            <p class="product-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($p['lokasi']) ?></p>
                            <p class="product-extra">Stok: <?= htmlspecialchars($p['stock']) ?></p>
                            <div class="d-flex flex-row gap-2 w-100">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="sewa_produk.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-handshake me-1"></i>Sewa
                                    </a>
                                    <form method="post" class="d-inline add-to-cart-form">
                                        <input type="hidden" name="add_to_cart_ajax" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Tambah ke Keranjang">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php?notif=login_sewa" class="btn btn-success btn-sm flex-fill"><i class="fas fa-sign-in-alt me-1"></i>Login untuk Sewa</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html> 