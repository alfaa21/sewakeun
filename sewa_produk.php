<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

// Redirect jika user belum login atau bukan user
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'user') {
    header('Location: login.php');
    exit();
}

// Pastikan ada ID produk yang dikirim
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php'); // Kembali ke halaman utama jika tidak ada ID
    exit();
}

$product_id = intval($_GET['id']);

// Ambil detail produk dari database
$product_query = mysqli_query($conn, "SELECT p.*, k.nama AS nama_kategori, l.nama AS nama_lokasi FROM produk p JOIN kategori k ON p.kategori_id = k.id JOIN lokasi l ON p.lokasi_id = l.id WHERE p.id = $product_id") or die(mysqli_error($conn));
$product = mysqli_fetch_assoc($product_query);

// Redirect jika produk tidak ditemukan
if (!$product) {
    header('Location: index.php');
    exit();
}

// Ambil data user yang login
$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT username, email, no_hp, alamat FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

// Ambil data kurir dari database
$kurir_query = mysqli_query($conn, "SELECT * FROM kurir");
$kurir_list = [];
while($k = mysqli_fetch_assoc($kurir_query)) {
    $kurir_list[] = $k;
}

// Ambil promo yang sudah diklaim user dan masih aktif
$claimed_promos = [];
$promo_query = mysqli_query($conn, "SELECT p.* FROM claimed_promos cp JOIN promos p ON cp.promo_id = p.id WHERE cp.user_id = $user_id AND p.status = 'aktif' AND p.tanggal_mulai <= CURDATE() AND p.tanggal_berakhir >= CURDATE() AND (p.limit_penggunaan IS NULL OR p.penggunaan_sekarang < p.limit_penggunaan)");
while ($row = mysqli_fetch_assoc($promo_query)) {
    $claimed_promos[] = $row;
}

include 'includes/_header.php';
?>

<main class="container mt-4" style="padding-top:60px;">
    <div class="mb-3">
        <a href="javascript:history.back()" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row g-4">
        <!-- Kiri: Alamat & Produk -->
        <div class="col-lg-7">
            <!-- Card Alamat Pengiriman -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold mb-1"><i class="fas fa-map-marker-alt text-success me-2"></i>Rumah &bull; <?= htmlspecialchars($_SESSION['username']) ?></div>
                        <div class="text-muted" style="max-width:400px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= htmlspecialchars($user_data['alamat'] ?? '-') ?>
                        </div>
                        <div class="text-muted" style="font-size:0.97rem;">
                            <i class="fas fa-phone-alt me-1"></i><?= htmlspecialchars($user_data['no_hp'] ?? '-') ?>
                        </div>
                    </div>
                    <a href="user_dashboard.php" class="btn btn-outline-secondary btn-sm">Ganti</a>
                </div>
            </div>
            <!-- Card Produk -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex align-items-center">
                    <img src="<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:12px;margin-right:18px;">
                    <div style="flex:1;">
                        <div class="fw-bold" style="font-size:1.1rem;"> <?= htmlspecialchars($product['nama']) ?> </div>
                        <div class="text-muted mb-1" style="font-size:0.98rem;">Kategori: <?= htmlspecialchars($product['nama_kategori']) ?> &bull; Lokasi: <?= htmlspecialchars($product['nama_lokasi']) ?></div>
                        <div class="mb-1">Harga: <span class="text-primary fw-bold">Rp <?= number_format($product['harga'],0,',','.') ?>/<?= htmlspecialchars($product['duration_unit']) ?></span></div>
                        <div class="text-muted" style="font-size:0.97rem;">Stok: <?= htmlspecialchars($product['stock']) ?> | Maksimal sewa: <?= htmlspecialchars($product['max_duration']) ?> <?= htmlspecialchars($product['duration_unit']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kanan: Form Sewa -->
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top:80px;z-index:1;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulir Penyewaan</h5>
                </div>
                <div class="card-body">
                    <form action="proses_sewa.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="produk_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <input type="hidden" id="harga-produk" value="<?= htmlspecialchars($product['harga']) ?>">
                        <input type="hidden" id="biaya-kurir-list" value='<?php echo json_encode(array_column($kurir_list, 'biaya', 'id')); ?>'>
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="alamat_pengiriman" value="<?= htmlspecialchars($user_data['alamat'] ?? '') ?>">
                        <div class="mb-3">
                            <label for="lama_sewa" class="form-label">Lama Sewa (<?= htmlspecialchars($product['duration_unit']) ?>)</label>
                            <input type="number" class="form-control" id="lama_sewa" name="lama_sewa" min="1" max="<?= htmlspecialchars($product['max_duration']) ?>" value="1" required>
                            <small class="form-text text-muted">Maksimal sewa: <?= htmlspecialchars($product['max_duration']) ?> <?= htmlspecialchars($product['duration_unit']) ?></small>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai Sewa</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="tanggal_pengembalian" name="tanggal_pengembalian" readonly required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah Produk</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?= htmlspecialchars($product['stock']) ?>" value="1" required>
                            <small class="form-text text-muted">Stok tersedia: <?= htmlspecialchars($product['stock']) ?></small>
                        </div>
                        <!-- Metode Pembayaran (Otomatis Saldo) -->
                        <div class="mb-3">
                            <label for="metode_pembayaran_single" class="form-label">Metode Pembayaran</label>
                            <input type="text" class="form-control" id="metode_pembayaran_single" value="Saldo" readonly>
                            <input type="hidden" name="metode_pembayaran" value="Saldo">
                        </div>
                        <?php if (count($claimed_promos) > 0): ?>
                        <div class="mb-3">
                            <label for="pilih_promo" class="form-label">Pilih Promo/Diskon</label>
                            <select class="form-select" id="pilih_promo" name="pilih_promo">
                                <option value="">Tidak pakai promo</option>
                                <?php foreach($claimed_promos as $promo): ?>
                                    <option value="<?= $promo['id'] ?>"
                                        data-tipe="<?= $promo['tipe'] ?>"
                                        data-nilai="<?= $promo['nilai'] ?>"
                                        data-maxdiskon="<?= $promo['max_diskon'] ?>">
                                        <?= htmlspecialchars($promo['nama']) ?> (<?= $promo['tipe'] === 'percentage' ? $promo['nilai'].'% OFF' : ($promo['tipe'] === 'fixed' ? 'Potongan Rp '.number_format($promo['nilai'],0,',','.') : 'Gratis Ongkir') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Metode Pengiriman</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pengiriman" id="pengiriman_cod" value="COD" checked>
                                <label class="form-check-label" for="pengiriman_cod">Ambil di Tempat</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metode_pengiriman" id="pengiriman_kurir" value="Jemput Kurir">
                                <label class="form-check-label" for="pengiriman_kurir">Jemput Kurir</label>
                            </div>
                        </div>
                        <div class="mb-3" id="pilihan-kurir-group" style="display:none;">
                            <label for="kurir_id" class="form-label">Pilih Kurir</label>
                            <select class="form-select" id="kurir_id" name="kurir_id">
                                <option value="">Pilih Kurir</option>
                                <?php foreach($kurir_list as $kurir): ?>
                                    <option value="<?= $kurir['id'] ?>"><?= htmlspecialchars($kurir['nama']) ?> (Rp <?= number_format($kurir['biaya'],0,',','.') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Tulis catatan jika ada..."></textarea>
                        </div>
                        <div class="mt-4 p-3 bg-success text-white rounded text-center">
                            <div id="biaya-kurir-info" style="display:none;font-size:1em;">
                                Biaya Kurir: Rp <span id="biaya-kurir">0</span>
                            </div>
                            <div id="total-sebelum-promo" style="font-size:1em;display:none;">
                                Total Sebelum Promo: Rp <span id="total-sebelum">0</span>
                            </div>
                            <div id="diskon-info" style="font-size:1em;display:none;">
                                Diskon: -Rp <span id="diskon-nominal">0</span>
                            </div>
                            <h5 class="mb-0">Total Biaya: Rp <span id="total-biaya">0</span></h5>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-4" style="font-size:1.15rem;font-weight:600;">Bayar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hargaProduk = parseFloat(document.getElementById('harga-produk').value);
        const lamaSewaInput = document.getElementById('lama_sewa');
        const quantityInput = document.getElementById('quantity');
        const totalBiayaSpan = document.getElementById('total-biaya');
        const minDuration = parseInt(lamaSewaInput.min);
        const maxDuration = parseInt(lamaSewaInput.max);
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        const tanggalPengembalianInput = document.getElementById('tanggal_pengembalian');
        const biayaKurirList = JSON.parse(document.getElementById('biaya-kurir-list').value);
        const radioCod = document.getElementById('pengiriman_cod');
        const radioKurir = document.getElementById('pengiriman_kurir');
        const groupKurir = document.getElementById('pilihan-kurir-group');
        const selectKurir = document.getElementById('kurir_id');
        const biayaKurirInfo = document.getElementById('biaya-kurir-info');
        const biayaKurirSpan = document.getElementById('biaya-kurir');
        const promoSelect = document.getElementById('pilih_promo');
        const diskonInfo = document.createElement('div');
        diskonInfo.className = 'mt-2 text-success';
        totalBiayaSpan.parentNode.appendChild(diskonInfo);

        const totalSebelumPromoDiv = document.getElementById('total-sebelum-promo');
        const totalSebelumSpan = document.getElementById('total-sebelum');
        const diskonInfoDiv = document.getElementById('diskon-info');
        const diskonNominalSpan = document.getElementById('diskon-nominal');

        function hitungDiskon(total) {
            if (!promoSelect || !promoSelect.value) return {diskon: 0, label: ''};
            const selected = promoSelect.options[promoSelect.selectedIndex];
            const tipe = selected.getAttribute('data-tipe');
            const nilai = parseFloat(selected.getAttribute('data-nilai'));
            const maxDiskon = parseFloat(selected.getAttribute('data-maxdiskon'));
            let diskon = 0;
            let label = '';
            if (tipe === 'percentage') {
                diskon = total * (nilai / 100);
                if (!isNaN(maxDiskon) && maxDiskon > 0) diskon = Math.min(diskon, maxDiskon);
                label = `Diskon: -Rp ${diskon.toLocaleString('id-ID')} (${nilai}%${maxDiskon>0?`, maks. Rp ${maxDiskon.toLocaleString('id-ID')}`:''})`;
            } else if (tipe === 'fixed') {
                diskon = nilai;
                label = `Diskon: -Rp ${diskon.toLocaleString('id-ID')}`;
            } else if (tipe === 'free_shipping') {
                // Diskon ongkir, hanya jika ada biaya kurir
                const biayaKurir = parseInt(biayaKurirSpan.innerText.replace(/\D/g, '')) || 0;
                diskon = biayaKurir;
                label = biayaKurir > 0 ? `Gratis Ongkir: -Rp ${biayaKurir.toLocaleString('id-ID')}` : '';
            }
            return {diskon, label};
        }

        function calculateTotal() {
            const lamaSewa = parseInt(lamaSewaInput.value);
            const quantity = parseInt(quantityInput.value);
            if (isNaN(lamaSewa) || lamaSewa > maxDuration || isNaN(quantity) || quantity < 1) {
                totalBiayaSpan.innerText = '0';
                diskonInfo.innerText = '';
                biayaKurirInfo.style.display = 'none';
                totalSebelumPromoDiv.style.display = 'none';
                diskonInfoDiv.style.display = 'none';
                return;
            }
            let total = hargaProduk * lamaSewa * quantity;
            let biayaKurir = 0;
            if (radioKurir.checked && selectKurir.value && biayaKurirList[selectKurir.value]) {
                biayaKurir = parseInt(biayaKurirList[selectKurir.value]);
                biayaKurirInfo.style.display = '';
                biayaKurirSpan.innerText = biayaKurir.toLocaleString('id-ID');
            } else {
                biayaKurirInfo.style.display = 'none';
            }
            total += biayaKurir;
            // Hitung diskon
            const {diskon, label} = hitungDiskon(total);
            let totalSetelahDiskon = total - diskon;
            if (totalSetelahDiskon < 0) totalSetelahDiskon = 0;
            totalBiayaSpan.innerText = totalSetelahDiskon.toLocaleString('id-ID');
            // Tampilkan info total sebelum promo dan diskon jika promo dipilih
            if (promoSelect && promoSelect.value && diskon > 0) {
                totalSebelumPromoDiv.style.display = '';
                totalSebelumSpan.innerText = total.toLocaleString('id-ID');
                diskonInfoDiv.style.display = '';
                diskonNominalSpan.innerText = diskon.toLocaleString('id-ID');
            } else {
                totalSebelumPromoDiv.style.display = 'none';
                diskonInfoDiv.style.display = 'none';
            }
            diskonInfo.innerText = label;
        }

        function calculateTanggalPengembalian() {
            const lamaSewa = parseInt(lamaSewaInput.value);
            const tanggalMulai = tanggalMulaiInput.value;
            if (!tanggalMulai || isNaN(lamaSewa) || lamaSewa > maxDuration) {
                tanggalPengembalianInput.value = '';
                return;
            }
            const startDate = new Date(tanggalMulai);
            // Tambah lama sewa (dalam hari)
            startDate.setDate(startDate.getDate() + lamaSewa);
            const yyyy = startDate.getFullYear();
            const mm = String(startDate.getMonth() + 1).padStart(2, '0');
            const dd = String(startDate.getDate()).padStart(2, '0');
            tanggalPengembalianInput.value = `${yyyy}-${mm}-${dd}`;
        }

        // Set min date for tanggal_mulai to today
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = `${yyyy}-${mm}-${dd}`;
        tanggalMulaiInput.min = todayStr;
        if (!tanggalMulaiInput.value) {
            tanggalMulaiInput.value = todayStr;
        }

        lamaSewaInput.addEventListener('input', function() {
            calculateTotal();
            calculateTanggalPengembalian();
        });
        quantityInput.addEventListener('input', calculateTotal);
        tanggalMulaiInput.addEventListener('input', calculateTanggalPengembalian);
        radioCod.addEventListener('change', calculateTotal);
        radioKurir.addEventListener('change', calculateTotal);
        selectKurir.addEventListener('change', calculateTotal);
        if (promoSelect) promoSelect.addEventListener('change', calculateTotal);
        // Inisialisasi awal
        calculateTotal();
        calculateTanggalPengembalian();

        // Show/hide pilihan kurir
        function toggleKurir() {
            if (radioKurir.checked) {
                groupKurir.style.display = '';
                groupKurir.querySelector('select').setAttribute('required', 'required');
            } else {
                groupKurir.style.display = 'none';
                groupKurir.querySelector('select').removeAttribute('required');
            }
        }
        radioCod.addEventListener('change', toggleKurir);
        radioKurir.addEventListener('change', toggleKurir);
        toggleKurir();
    });
</script>

</body>
</html> 