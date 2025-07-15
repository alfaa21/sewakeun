<?php include 'includes/_header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">
                <i class="fas fa-gift text-primary"></i> Promo & Diskon
            </h1>
            <p class="text-center text-muted mb-5">Dapatkan penawaran terbaik untuk penyewaan barang favorit Anda</p>
        </div>
    </div>

    <?php if (empty($promos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-gift fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Belum ada promo saat ini</h4>
            <p class="text-muted">Nantikan promo menarik berikutnya!</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($promos as $promo): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card promo-card h-100 shadow-sm">
                        <?php if ($promo['gambar']): ?>
                            <img src="<?= htmlspecialchars($promo['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($promo['nama']) ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <span class="badge bg-danger fs-6 mb-2">
                                    <?php
                                    switch ($promo['tipe']) {
                                        case 'percentage':
                                            echo $promo['nilai'] . '% OFF';
                                            break;
                                        case 'fixed':
                                            echo 'Rp ' . number_format($promo['nilai'], 0, ',', '.') . ' OFF';
                                            break;
                                        case 'free_shipping':
                                            echo 'FREE SHIPPING';
                                            break;
                                    }
                                    ?>
                                </span>
                                
                                <?php if ($promo['kategori_id']): ?>
                                    <span class="badge bg-info ms-1"><?= htmlspecialchars($promo['nama_kategori']) ?></span>
                                <?php endif; ?>
                                
                                <?php if ($promo['produk_id']): ?>
                                    <span class="badge bg-warning ms-1">Produk Spesifik</span>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="card-title"><?= htmlspecialchars($promo['nama']) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($promo['deskripsi']) ?></p>
                            
                            <div class="mt-auto">
                                <div class="row text-muted small mb-3">
                                    <div class="col-6">
                                        <i class="fas fa-calendar-alt"></i> 
                                        Berakhir: <?= date('d M Y', strtotime($promo['tanggal_berakhir'])) ?>
                                    </div>
                                    <div class="col-6 text-end">
                                        <i class="fas fa-users"></i> 
                                        <?= $promo['penggunaan_sekarang'] ?>/<?= $promo['limit_penggunaan'] ?: '∞' ?>
                                    </div>
                                </div>
                                
                                <?php if ($promo['min_transaksi'] > 0): ?>
                                    <div class="alert alert-info small mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        Min. transaksi Rp <?= number_format($promo['min_transaksi'], 0, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-grid">
                                    <button class="btn btn-primary copy-promo-btn" data-kode="<?= htmlspecialchars($promo['kode_promo']) ?>">
                                        <i class="fas fa-copy"></i> Salin Kode: <?= htmlspecialchars($promo['kode_promo']) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal untuk detail promo -->
<div class="modal fade" id="promoDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Promo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="promoDetailContent"></div>
            </div>
        </div>
    </div>
</div>

<style>
.promo-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 12px;
}

.promo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.promo-card .card-img-top {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.badge {
    font-weight: 600;
}

.copy-promo-btn {
    border-radius: 8px;
    font-weight: 600;
}

.copy-promo-btn:hover {
    transform: scale(1.02);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy promo code functionality
    const copyButtons = document.querySelectorAll('.copy-promo-btn');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const kode = this.getAttribute('data-kode');
            
            // Copy to clipboard
            navigator.clipboard.writeText(kode).then(() => {
                // Show success message
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Kode Disalin!';
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                alert('Gagal menyalin kode promo');
            });
        });
    });
});
</script>

<?php include 'includes/_footer.php'; ?> 