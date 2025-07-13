<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';
include 'includes/_header.php';

$id = intval($_GET['id'] ?? 0);
$product_data = null;

if ($id > 0) {
    $product_query = mysqli_query($conn, "SELECT produk.*, kategori.nama AS kategori, lokasi.nama AS lokasi FROM produk JOIN kategori ON produk.kategori_id = kategori.id JOIN lokasi ON produk.lokasi_id = lokasi.id WHERE produk.id=$id") or die(mysqli_error($conn));
    $product_data = mysqli_fetch_assoc($product_query);
}

if (!$product_data) {
    echo '<main class="container mt-5"><div class="alert alert-danger">Produk tidak ditemukan atau ID tidak valid.</div></main>';
    // Optionally redirect to index.php
    // header("Location: index.php");
    // exit();
} else {
?>

<?php
$from = $_GET['from'] ?? '';
$back_url = 'index.php';
if ($from === 'ai') {
    $back_url = 'chat.php';
} elseif ($from === 'index') {
    $back_url = 'index.php';
}
?>
<a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>

<main class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <img src="<?= htmlspecialchars($product_data['gambar']) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($product_data['nama']) ?>">
        </div>
        <div class="col-md-6">
            <h1 class="mb-3"><?= htmlspecialchars($product_data['nama']) ?></h1>
            <p class="lead text-primary fw-bold">Rp <?= number_format($product_data['harga'],0,',','.') ?> / <?= htmlspecialchars($product_data['duration_unit']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($product_data['kategori']) ?></p>
            <p><strong>Lokasi:</strong> <?= htmlspecialchars($product_data['lokasi']) ?></p>
            <p><strong>Stok Tersedia:</strong> <span class="badge bg-<?= ($product_data['stock'] > 0) ? 'success' : 'danger' ?>"><?= htmlspecialchars($product_data['stock']) ?></span></p>
            <p><strong>Durasi Maksimal Sewa:</strong> <?= htmlspecialchars($product_data['max_duration']) ?> <?= htmlspecialchars($product_data['duration_unit']) ?></p>
            <hr>
            <h4>Deskripsi Produk:</h4>
            <p><?= nl2br(htmlspecialchars($product_data['deskripsi'])) ?></p>
            <hr>
            <?php if ($product_data['stock'] > 0): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="d-flex gap-2">
                        <a href="sewa_produk.php?id=<?= htmlspecialchars($product_data['id']) ?>" class="btn btn-success btn-lg flex-fill">
                            <i class="fas fa-handshake me-2"></i> Sewa Sekarang
                        </a>
                        <button type="button" class="btn btn-primary btn-lg add-to-cart-btn" data-id="<?= htmlspecialchars($product_data['id']) ?>" title="Tambah ke Keranjang">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <a href="message.php?produk_id=<?= htmlspecialchars($product_data['id']) ?>" class="btn btn-info btn-lg" title="Chat Admin">
                            <i class="fas fa-comments"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-success btn-lg"><i class="fas fa-sign-in-alt me-2"></i> Login untuk Sewa</a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn btn-danger btn-lg" disabled><i class="fas fa-times-circle me-2"></i> Stok Habis</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-5">
        <h2>Ulasan Produk</h2>
        <div class="row">
        <?php
            $product_id = $product_data['id'];
            $reviews_query = mysqli_query($conn, "SELECT reviews.*, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE product_id=$product_id ORDER BY review_date DESC") or die(mysqli_error($conn));
        if (mysqli_num_rows($reviews_query) > 0) {
            while ($review = mysqli_fetch_assoc($reviews_query)) {
            ?>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-1">By <?= htmlspecialchars($review['username']) ?></h5>
                                <div class="text-warning mb-2">
                                    <?php for ($i = 0; $i < $review['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                                    <?php for ($i = $review['rating']; $i < 5; $i++) echo '<i class="far fa-star"></i>'; ?>
                                    <small class="text-muted ms-2"><?= htmlspecialchars($review['rating']) ?>/5</small>
                                </div>
                                <p class="card-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                <small class="text-muted">Ditulis pada: <?= htmlspecialchars(date('d M Y', strtotime($review['review_date']))) ?></small>
                            </div>
                        </div>
                    </div>
            <?php
            }
        } else {
                echo '<div class="col-12"><p class="text-muted">Belum ada ulasan untuk produk ini.</p></div>';
        }
        ?>
        </div>
    </div>
</main>

<?php
}
// Removed include 'includes/_footer.php';
?>
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
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    const liveToast = document.getElementById('liveToast');
    const toastBody = liveToast.querySelector('.toast-body');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(liveToast);

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('add_to_cart_ajax', productId);

            fetch('chart_produk.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                toastBody.textContent = data.message;
                toastBootstrap.show();
            })
            .catch(error => {
                toastBody.textContent = 'Terjadi kesalahan saat menambahkan produk.';
                liveToast.classList.remove('text-bg-success');
                liveToast.classList.add('text-bg-danger');
                toastBootstrap.show();
            });
        });
    }
});
</script> 