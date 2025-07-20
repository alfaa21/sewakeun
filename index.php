<?php
include_once 'includes/session_bootstrap.php';
ob_start(); // Start output buffering
include 'koneksi.php';

// Handle AJAX add to cart request
if (isset($_POST['add_to_cart_ajax'])) {
    $id = (int)$_POST['add_to_cart_ajax'];
    $found = false;
    if (isset($_SESSION['user_id'])) {
        // User login, simpan ke database
        $user_id = $_SESSION['user_id'];
      
        $cek = mysqli_query($conn, "SELECT id, qty FROM cart_items WHERE user_id=$user_id AND product_id=$id");
        if ($row = mysqli_fetch_assoc($cek)) {
         
            $new_qty = $row['qty'] + 1;
            mysqli_query($conn, "UPDATE cart_items SET qty=$new_qty WHERE id=" . $row['id']);
        } else {
            
            mysqli_query($conn, "INSERT INTO cart_items (user_id, product_id, qty) VALUES ($user_id, $id, 1)");
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
        exit();
    } else {
        // Belum login, simpan di session
        if (!isset($_SESSION['chart_produk'])) {
            $_SESSION['chart_produk'] = [];
        }
        foreach ($_SESSION['chart_produk'] as &$item) {
            if ($item['product_id'] === $id) {
                $item['qty'] += 1;
                $found = true;
                break;
            }
        }
        unset($item);
        if (!$found) {
            $_SESSION['chart_produk'][] = [
                'product_id' => $id,
                'qty' => 1,
                'duration' => 1
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
        exit();
    }
}

include 'includes/_header.php';

$products_query = mysqli_query($conn, "SELECT p.*, k.nama AS nama_kategori, l.nama AS nama_lokasi FROM produk p JOIN kategori k ON p.kategori_id = k.id JOIN lokasi l ON p.lokasi_id = l.id ORDER BY p.id DESC") or die(mysqli_error($conn));

// Check for message from other pages (e.g., login, register)
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);


$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
$kategori_list = [];
while($k = mysqli_fetch_assoc($kategori_query)) {
    $kategori_list[] = $k;
}


$kategori_icons = [
    'Elektronik' => ['icon' => 'fa-solid fa-plug', 'color' => '#1976d2'],
    'Kamera' => ['icon' => 'fa-solid fa-camera', 'color' => '#8e24aa'],
    'Aksesoris' => ['icon' => 'fa-solid fa-gem', 'color' => '#ff1744'],
    'Lainnya' => ['icon' => 'fa-solid fa-box', 'color' => '#1976d2'],
    'Pakaian' => ['icon' => 'fa-solid fa-shirt', 'color' => '#007bff'],
    'Fashion' => ['icon' => 'fa-solid fa-shirt', 'color' => '#007bff'],
    'Perabotan' => ['icon' => 'fa-solid fa-couch', 'color' => '#ff9800'],
    'Outdoor' => ['icon' => 'fa-solid fa-campground', 'color' => '#43a047'],
];
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<style>

.landing-navbar {
    background: #fff;
    border-bottom: 2px solid #eaeaea;
    padding: 0.5rem 0;
    position: sticky;
    top: 0;
    z-index: 1050;
}
.landing-navbar .logo {
    font-size: 2.2rem;
    font-weight: bold;
    color: #223;
    letter-spacing: 2px;
    margin-right: 1.5rem;
}
.landing-navbar .search-bar {
    flex: 1 1 0%;
    max-width: 500px;
    margin: 0 1rem;
}
.landing-navbar .search-bar input {
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #ccc;
    padding: 0.5rem 1rem;
}
.landing-navbar .btn-kategori {
    background: #223;
    color: #fff;
    border-radius: 0.4rem;
    font-weight: 600;
    margin-right: 1rem;
}
.landing-navbar .btn-konsultasi {
    background: #fff;
    border: 1px solid #007bff;
    color: #007bff;
    border-radius: 0.4rem;
    font-weight: 600;
    margin-left: 1rem;
}
.landing-navbar .btn-konsultasi:hover {
    background: #007bff;
    color: #fff;
}
.landing-navbar .telp {
    font-weight: 600;
    color: #223;
    margin-left: 1rem;
}
.hero-landing {
    position: relative;
    min-height: 480px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: url('https://c1.wallpaperflare.com/preview/439/1004/900/souk-discount-bazaar-alley.jpg') center center/cover no-repeat;
    /* border-radius: 0 0 32px 32px; */crop
    border-radius: 0;
    margin-bottom: 0;
    overflow: hidden;
    
}

/* Hapus margin-top pada container utama jika ada */
.container.mt-4, .container.mt-5, main.container {
    margin-top: 0 !important;
}
.hero-landing .overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.55);
    z-index: 1;
}
.hero-landing .hero-content {
    position: relative;
    z-index: 2;
    color: #fff;
    text-align: center;
    max-width: 900px;
    margin: 0 auto;
}
.hero-landing .hero-content h1 {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 1.2rem;
    line-height: 1.1;
    text-shadow: 0 2px 12px rgba(0,0,0,0.25);
}
.hero-landing .hero-content .sub {
    font-size: 1.3rem;
    font-style: italic;
    margin-bottom: 2rem;
    color: #b6fffa;
}
.hero-landing .hero-content .btn-hero {
    background: #a6ff00;
    color: #223;
    font-weight: 700;
    font-size: 1.2rem;
    padding: 1rem 2.5rem;
    border-radius: 0.5rem;
    border: none;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.hero-landing .hero-content .btn-hero:hover {
    background: #8be000;
    color: #223;
}
.trusted-bar {
    background: #223;
    color: #fff;
    padding: 1.2rem 0.5rem;
    text-align: center;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
    border-radius: 0;
}
.trusted-bar .trusted-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}
@media (max-width: 768px) {
    .hero-landing .hero-content h1 {
        font-size: 1.5rem;
    }
    .hero-landing {
        min-height: 320px;
    }
    .trusted-bar {
        flex-direction: column;
        gap: 0.7rem;
        font-size: 0.95rem;
    }
}

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
    margin-bottom: 3rem; 
    text-align: center;
    width: 100%;
}

.product-list {
    margin-bottom: 3rem;
    padding: 0 1rem;
}

.product-grid-5-col {
    display: flex;
    flex-wrap: wrap;
    gap: 1.2rem; 
    justify-content: center;
    margin: 0 auto;
    max-width: 1800px; 
}

.product-card-item {
    flex: 0 0 calc(16.666% - 1rem); 
    max-width: calc(16.666% - 1rem);
    background: #fff;
    border-radius: 10px; 
    box-shadow: 0 6px 20px rgba(0,0,0,0.08); 
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: auto; 
    display: flex;
    flex-direction: column;
    overflow: hidden;
    text-decoration: none; 
    color: inherit;
}

.product-card-item:hover {
    transform: translateY(-6px); 
    box-shadow: 0 10px 35px rgba(0,0,0,0.18) !important; 
}

.product-card-item .product-img-wrap {
    width: 100%;
    aspect-ratio: 1 / 0.8; 
    background: #f0f2f5; 
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 10px 10px 0 0; 
}

.product-card-item .img-fluid {
    width: 100%;
    height: 100%;
    object-fit: cover; 
    transition: transform 0.3s ease; 
}

.product-card-item:hover .img-fluid {
    transform: scale(1.08); 
}

.product-card-item .card-body {
    padding: 0.8rem 0.9rem 0.9rem 0.9rem; 
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between; 
}

.product-card-item .card-title {
    font-size: 0.95rem; 
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
    font-size: 0.82rem; /* Ukuran tombol disesuaikan */
    padding: 0.35rem 0.55rem;
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

</style>

<section class="hero-landing mb-0">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1>SEWA ALAT SEHARI-HARI <br><span style="text-decoration: underline #a6ff00 6px;">PALING LENGKAP DI JOGJA!!</span></h1>
        <div class="sub">“PENUHI KEBUTUHAN ANDA BERSAMA KAMI!”</div>
        <a href="#produk" class="btn btn-hero">→ KLIK DI SINI UNTUK PILIH PRODUK TERBARU KAMI</a>
    </div>
</section>
<!-- TRUSTED BAR -->
<div class="trusted-bar">
    <div class="trusted-item"><i class="fas fa-check text-success"></i> TIM PROFESIONAL CEPAT DAN TANGGAP</div>
    <div class="trusted-item"><i class="fas fa-check text-success"></i> DARI PEMESANAN, PENGIRIMAN, SAMPAI PENGEMBALIAN, SEMUANYA KITA URUS.</div>
    <div class="trusted-item"><i class="fas fa-check text-success"></i> DIPERCAYA BANYAK PERUSAHAAN LOKAL SAMPAI INTERNASIONAL</div>
</div>

<div class="promo-cards">
    <div class="promo-card-modern" style="background:#00cfff;">
        <img src="assets/images/DSLR-LEICA-260x200.jpg" class="promo-img" alt="Promo Diskon Akhir Tahun">
        <div class="promo-content">
            <div class="promo-title" >Diskon Akhir Tahun!</div>
            <div class="promo-desc">Dapatkan diskon hingga 30% untuk semua kategori.</div>
            <button class="promo-btn" onclick="window.location.href='promo.php'">Lihat Promo</button>
            <span class="promo-badge">30% OFF</span>
        </div>
    </div>
    <div class="promo-card-modern bg-dark">
        <img src="assets/images/playstation-5.jpg" class="promo-img" alt="Promo Sewa Lebih Lama">
        <div class="promo-content">
            <div class="promo-title">Sewa Lebih Lama, Hemat Lebih Banyak</div>
            <div class="promo-desc">Diskon khusus untuk penyewaan di atas 7 hari.</div>
            <button class="promo-btn" onclick="window.location.href='promo.php'">Pelajari Lebih Lanjut</button>
            <span class="promo-badge">Hemat!</span>
        </div>
    </div>
    <div class="promo-card-modern" style="background:#ffe082;">
        <img src="assets/images/Kamera sony.jpg" class="promo-img" alt="Promo Kamera">
        <div class="promo-content">
            <div class="promo-title">Promo Kamera & Elektronik</div>
            <div class="promo-desc">Sewa kamera, proyektor, dan alat elektronik lain dengan harga spesial minggu ini!</div>
            <button class="promo-btn" onclick="window.location.href='promo.php'">Lihat Detail</button>
            <span class="promo-badge" style="background:#ff9800;">Hot!</span>
        </div>
    </div>
</div>

<!-- SECTION KATEGORI PILIHAN (icon) -->
<section class="kategori-section">
    <h2>Kategori Pilihan</h2>
    <div class="kategori-grid">
        <?php foreach($kategori_list as $k): ?>
            <?php
                $icon = $kategori_icons[$k['nama']]['icon'] ?? 'fa-solid fa-box';
                $color = $kategori_icons[$k['nama']]['color'] ?? '#1976d2';
            ?>
            <a href="kategori.php?cat=<?= $k['id'] ?>" class="kategori-card" style="text-decoration:none;">
                <span class="kategori-icon" style="color:<?= $color ?>;background:#f6f8fa;width:60px;height:60px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;">
                    <i class="<?= $icon ?>" style="color:<?= $color ?>;font-size:2.5rem;"></i>
                </span>
                <div class="kategori-nama" style="font-size:1.08rem;font-weight:600;text-align:center;margin-bottom:0.1rem;color:#222;word-break:break-word;line-height:1.2;">
                    <?= htmlspecialchars($k['nama']) ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

        <section class="product-list">
            <h2 class="product-list-title" id="produk">Produk Terbaru</h2>
            <div class="product-grid-5-col">
            <?php if (mysqli_num_rows($products_query) > 0): ?>
                <?php while($p = mysqli_fetch_assoc($products_query)): ?>
                        <div class="product-card-item">
                            <a href="produk_detail.php?id=<?= $p['id'] ?>" class="product-card-link">
                                <div class="product-img-wrap">
                                    <img src="<?= htmlspecialchars($p['gambar']) ?>" class="img-fluid" alt="<?= htmlspecialchars($p['nama']) ?>">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" title="<?= htmlspecialchars($p['nama']) ?>"><?= htmlspecialchars($p['nama']) ?></h5>
                                    <p class="product-price">Rp <?= number_format($p['harga'],0,',','.') ?>/hari</p>
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
                                            for ($i = 0; $i < $empty_star; $i++) echo '<i class="far fa-star text-warning"></i>';
                                            echo " <span class=\"text-muted small\">($total_review Ulasan, $avg_rating/5)</span>";
                                        } else {
                                            for ($i = 0; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
                                            echo ' <span class="text-muted small">0 Ulasan</span>';
                                        }
                                    ?>
                                    </div>
                                    <p class="product-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($p['nama_lokasi']) ?></p>
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
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">Tidak ada produk ditemukan.</p>
                </div>
            <?php endif; ?>
            </div>
        </section>

<!-- Toast Container for notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notifikasi</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Produk berhasil ditambahkan ke keranjang!
            </div>
        </div>
</div>

        <script>
    document.addEventListener('DOMContentLoaded', function () {
        const addToCartForms = document.querySelectorAll('.add-to-cart-form');
        const liveToast = document.getElementById('liveToast');
        const toastBody = liveToast.querySelector('.toast-body');
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(liveToast);

        addToCartForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        toastBody.textContent = data.message;
                        toastBootstrap.show();
                    } else {
                        // Handle error or other status
                        toastBody.textContent = data.message || 'Terjadi kesalahan.';
                        liveToast.classList.remove('text-bg-success');
                        liveToast.classList.add('text-bg-danger');
                        toastBootstrap.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastBody.textContent = 'Terjadi kesalahan saat menambahkan produk.';
                    liveToast.classList.remove('text-bg-success');
                    liveToast.classList.add('text-bg-danger');
                    toastBootstrap.show();
                });
            });
        });
    });
        </script>

</body>
</html>
