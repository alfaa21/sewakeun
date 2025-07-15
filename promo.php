<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

// Proses klaim promo
if (isset($_POST['claim_promo_id']) && isset($_SESSION['user_id'])) {
    $promo_id = intval($_POST['claim_promo_id']);
    $user_id = $_SESSION['user_id'];
    // Cek apakah sudah pernah claim
    $cek = mysqli_query($conn, "SELECT id FROM claimed_promos WHERE user_id=$user_id AND promo_id=$promo_id");
    if (mysqli_num_rows($cek) == 0) {
        $ins = mysqli_query($conn, "INSERT INTO claimed_promos (user_id, promo_id) VALUES ($user_id, $promo_id)");
        if ($ins) {
            $claim_message = ['type'=>'success','text'=>'Promo berhasil diklaim!'];
        } else {
            $claim_message = ['type'=>'danger','text'=>'Gagal klaim promo.'];
        }
    } else {
        $claim_message = ['type'=>'info','text'=>'Promo sudah pernah diklaim.'];
    }
}

// Load PromoModel
require_once 'models/PromoModel.php';
$promoModel = new PromoModel($conn);
$promos = $promoModel->getActivePromos();

include 'includes/_header.php';
?>

<div class="container mt-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold text-primary mb-3">
                <i class="fas fa-gift"></i> Promo & Diskon
            </h1>
            <p class="lead text-muted">Dapatkan penawaran terbaik untuk penyewaan barang favorit Anda</p>
            <div class="d-flex justify-content-center flex-wrap gap-3 mt-4">
                <button class="btn btn-outline-primary active" onclick="filterPromos('all')">
                    <i class="fas fa-th-large"></i> Semua Promo
                </button>
                <button class="btn btn-outline-danger" onclick="filterPromos('percentage')">
                    <i class="fas fa-percentage"></i> Diskon Persen
                </button>
                <button class="btn btn-outline-warning" onclick="filterPromos('fixed')">
                    <i class="fas fa-tags"></i> Potongan Harga
                </button>
                <button class="btn btn-outline-success" onclick="filterPromos('free_shipping')">
                    <i class="fas fa-shipping-fast"></i> Gratis Ongkir
                </button>
            </div>
        </div>
    </div>

    <?php if (isset($claim_message)): ?>
        <div class="alert alert-<?= $claim_message['type'] ?> alert-dismissible fade show promo-toast-alert" role="alert">
            <?= htmlspecialchars($claim_message['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($promos)): ?>
        <!-- Empty State -->
        <div class="row">
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-gift fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Belum ada promo saat ini</h3>
                    <p class="text-muted mb-4">Nantikan promo menarik berikutnya! Kami akan memberikan penawaran terbaik untuk Anda.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Promo Grid -->
        <div class="row" id="promoGrid">
            <?php foreach ($promos as $promo): ?>
                <?php
                    // Menentukan tema warna dan ikon berdasarkan tipe promo
                    $theme = [];
                    switch ($promo['tipe']) {
                        case 'percentage':
                            $theme['color'] = 'danger'; // Merah untuk diskon
                            $theme['icon'] = 'fas fa-percentage';
                            $theme['text'] = $promo['nilai'] . '% OFF';
                            break;
                        case 'fixed':
                            $theme['color'] = 'warning'; // Oranye untuk potongan harga
                            $theme['icon'] = 'fas fa-tags';
                            $theme['text'] = 'Rp ' . number_format($promo['nilai'], 0, ',', '.');
                            break;
                        case 'free_shipping':
                            $theme['color'] = 'success'; // Hijau untuk gratis ongkir
                            $theme['icon'] = 'fas fa-shipping-fast';
                            $theme['text'] = 'Gratis Ongkir';
                            break;
                        default:
                            $theme['color'] = 'primary';
                            $theme['icon'] = 'fas fa-gift';
                            $theme['text'] = 'Promo Spesial';
                    }
                ?>
                <div class="col-lg-4 col-md-6 mb-4 promo-item" data-type="<?= htmlspecialchars($promo['tipe']) ?>">
                    <div class="card promo-card-simple h-100 shadow-sm border-0 text-center">
                        <div class="card-body d-flex flex-column p-4">
                            <!-- Ikon Promo -->
                            <div class="promo-icon-wrapper mb-3 mx-auto">
                                <div class="promo-icon bg-<?= $theme['color'] ?>-subtle text-<?= $theme['color'] ?>">
                                    <i class="<?= $theme['icon'] ?> fa-2x"></i>
                                </div>
                            </div>
                            
                            <!-- Judul & Deskripsi -->
                            <h5 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($promo['nama']) ?></h5>
                            <p class="card-text text-muted small mb-3"><?= htmlspecialchars($promo['deskripsi']) ?></p>
                            
                            <!-- Badge Nilai Promo -->
                            <div class="mb-4">
                                <span class="badge fs-6 rounded-pill bg-<?= $theme['color'] ?>"><?= $theme['text'] ?></span>
                            </div>

                            <!-- Detail & Tombol Aksi -->
                            <div class="mt-auto w-100">
                                <div class="text-muted small mb-3">
                                    <i class="fas fa-calendar-alt"></i>
                                    Berakhir: <?= date('d M Y', strtotime($promo['tanggal_berakhir'])) ?>
                                </div>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <?php
                                    // Cek apakah user sudah claim promo ini
                                    $user_id = $_SESSION['user_id'];
                                    $cek_claim = mysqli_query($conn, "SELECT id FROM claimed_promos WHERE user_id=$user_id AND promo_id=".$promo['id']);
                                    $sudah_claim = mysqli_num_rows($cek_claim) > 0;
                                    ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="claim_promo_id" value="<?= $promo['id'] ?>">
                                        <button type="submit" class="btn btn-warning" <?= $sudah_claim ? 'disabled' : '' ?>>
                                            <?= $sudah_claim ? 'Sudah Diklaim' : 'Claim Promo' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <button class="btn btn-link btn-sm text-secondary mt-2" 
                                        onclick="showPromoDetail(<?= htmlspecialchars(json_encode($promo)) ?>)">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Promo Detail Modal (Struktur tidak diubah, fungsionalitas tetap sama) -->
<div class="modal fade" id="promoDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-gift text-primary"></i> Detail Promo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="promoDetailContent">
                <!-- Content will be loaded here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Kustom untuk Tampilan Kartu Promo Baru */
.promo-card-simple {
    transition: all 0.3s ease;
    border-radius: 1rem; /* Sudut lebih melengkung */
}

.promo-card-simple:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}

.promo-icon-wrapper {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.promo-icon {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.copy-promo-btn {
    font-weight: 600;
}

.empty-state {
    padding: 3rem 0;
}

/* Filter buttons styling */
.btn-outline-primary, .btn-outline-danger, .btn-outline-warning, .btn-outline-success {
    transition: all 0.2s ease;
}

.btn-outline-primary.active, .btn-outline-primary:hover,
.btn-outline-danger.active, .btn-outline-danger:hover,
.btn-outline-warning.active, .btn-outline-warning:hover,
.btn-outline-success.active, .btn-outline-success:hover {
    color: #fff;
}
.promo-toast-alert {
    position: fixed;
    top: 80px;
    right: 32px;
    min-width: 280px;
    z-index: 9999;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
    opacity: 0.98;
}
@media (max-width: 600px) {
    .promo-toast-alert { right: 8px; left: 8px; min-width:unset; }
}
</style>

<script>
// Simpan posisi scroll sebelum submit claim promo
const claimForms = document.querySelectorAll('form[action=""], form:not([action])');
claimForms.forEach(form => {
    form.addEventListener('submit', function() {
        sessionStorage.setItem('promoScrollY', window.scrollY);
    });
});
// Setelah reload, restore posisi scroll
window.addEventListener('DOMContentLoaded', function() {
    const y = sessionStorage.getItem('promoScrollY');
    if (y !== null) {
        window.scrollTo(0, parseInt(y));
        sessionStorage.removeItem('promoScrollY');
    }
});

// JavaScript yang ada tidak perlu diubah, hanya memastikan class .copy-promo-btn dan fungsi showPromoDetail tetap terpanggil
document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-promo-btn');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const kode = this.getAttribute('data-kode');
            const nama = this.getAttribute('data-nama');
            
            navigator.clipboard.writeText(kode).then(() => {
                const originalText = this.innerHTML;
                const originalClasses = this.className;

                this.innerHTML = '<i class="fas fa-check"></i> Disalin!';
                // Ganti warna tombol sementara
                this.className = this.className.replace(/btn-(danger|warning|success|primary)/, 'btn-dark');

                showToast('Kode promo ' + nama + ' berhasil disalin!', 'success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.className = originalClasses;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                showToast('Gagal menyalin kode promo', 'error');
            });
        });
    });
});

function filterPromos(type) {
    const promoItems = document.querySelectorAll('.promo-item');
    const filterButtons = document.querySelectorAll('.d-flex.justify-content-center .btn');
    
    filterButtons.forEach(btn => btn.classList.remove('active'));
    
    const activeButton = document.querySelector(`[onclick="filterPromos('${type}')"]`);
    if(activeButton) {
        activeButton.classList.add('active');
    }
    
    promoItems.forEach(item => {
        if (type === 'all' || item.getAttribute('data-type') === type) {
            item.style.display = 'block';
            item.style.animation = 'fadeIn 0.5s ease';
        } else {
            item.style.display = 'none';
        }
    });
}

function showPromoDetail(promo) {
    const modal = document.getElementById('promoDetailModal');
    const content = document.getElementById('promoDetailContent');
    
    let promoValueText = '';
    switch(promo.tipe) {
        case 'percentage':
            promoValueText = `${promo.nilai}% Potongan`;
            break;
        case 'fixed':
            promoValueText = `Rp ${Number(promo.nilai).toLocaleString('id-ID')} Potongan`;
            break;
        case 'free_shipping':
            promoValueText = 'Gratis Biaya Pengiriman';
            break;
    }

    content.innerHTML = `
        <div class="text-center mb-4">
            <div class="promo-icon bg-${promo.tipe === 'percentage' ? 'danger' : promo.tipe === 'fixed' ? 'warning' : 'success'}-subtle text-${promo.tipe === 'percentage' ? 'danger' : promo.tipe === 'fixed' ? 'warning' : 'success'}" style="width: 100px; height: 100px; border-radius: 50%; margin: auto; display: flex; align-items: center; justify-content: center;">
                <i class="fas ${promo.tipe === 'percentage' ? 'fa-percentage' : promo.tipe === 'fixed' ? 'fa-tags' : 'fa-shipping-fast'} fa-3x"></i>
            </div>
        </div>
        <h4 class="text-center">${promo.nama}</h4>
        <p class="text-center text-muted">${promo.deskripsi}</p>
        <hr>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Kode Promo
                <strong class="bg-light p-2 rounded"><code>${promo.kode_promo}</code></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Jenis Promo
                <strong>${promoValueText}</strong>
            </li>
            ${promo.min_transaksi > 0 ? `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Minimum Transaksi
                    <strong>Rp ${Number(promo.min_transaksi).toLocaleString('id-ID')}</strong>
                </li>
            ` : ''}
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Berlaku Sampai
                <strong>${new Date(promo.tanggal_berakhir).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</strong>
            </li>
            ${promo.limit_penggunaan ? `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Limit Penggunaan
                    <strong>${promo.penggunaan_sekarang}/${promo.limit_penggunaan}</strong>
                </li>
            ` : ''}
        </ul>
    `;
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

function showToast(message, type = 'info') {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'position-fixed top-0 end-0 p-3';
    toastContainer.style.zIndex = '9999';
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    document.body.appendChild(toastContainer);
    
    const toastInstance = new bootstrap.Toast(toast);
    toastInstance.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toastContainer);
    });
}

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
</script>
