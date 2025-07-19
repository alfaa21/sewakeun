<?php
include_once 'koneksi.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
if (!isset($_SESSION['user_id'])) {
    echo '<li class="p-3 text-center text-muted">Belum login</li>';
    exit();
}
$user_id = $_SESSION['user_id'];
$now = date('Y-m-d');
$besok = date('Y-m-d', strtotime('+1 day'));
// Query transaksi yang masa_sewa dan besok tanggal pengembalian
$q = mysqli_query($conn, "SELECT t.id_transaksi, t.tanggal_selesai, t.tanggal_mulai, t.lama_sewa, t.status_transaksi, p.nama AS nama_produk FROM transaksi t JOIN produk p ON t.id_produk=p.id WHERE t.id_user='$user_id' AND t.status_transaksi='masa_sewa' AND t.tanggal_selesai='$besok'")
    or die('<li class="p-3 text-danger">Query error: ' . mysqli_error($conn) . '</li>');
$notifs = [];
while($n = mysqli_fetch_assoc($q)) {
    $notifs[] = $n;
}
if (count($notifs) == 0) {
    echo '<li class="p-3 text-center text-muted">Tidak ada notifikasi</li>';
    exit();
}
?>
<style>
.notif-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(30,60,90,0.10);
    margin-bottom: 16px;
    padding: 1.1rem 1.3rem 1rem 1.3rem;
    border: 1px solid #e3e6ee;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}
.notif-card:last-child { margin-bottom: 0; }
.notif-card .notif-icon {
    color: #ff9800;
    font-size: 1.7rem;
    margin-top: 2px;
    flex-shrink: 0;
}
.notif-card .notif-content {
    flex: 1;
}
.notif-card .notif-title {
    font-weight: 700;
    font-size: 1.08rem;
    margin-bottom: 0.2rem;
    color: #222;
}
.notif-card .notif-produk {
    color: #1976d2;
    font-weight: 600;
    margin-bottom: 0.1rem;
}
.notif-card .notif-tanggal {
    color: #e53935;
    font-weight: 700;
    margin-bottom: 0.2rem;
}
.notif-card .notif-desc {
    color: #555;
    font-size: 0.97rem;
}
</style>
<?php foreach($notifs as $n): ?>
<li class="notif-item" style="background:transparent;border:none;padding:0;">
    <div class="notif-card">
        <div class="notif-icon"><i class="fas fa-bell"></i></div>
        <div class="notif-content">
            <div class="notif-title">ID Pesanan: #<?= htmlspecialchars($n['id_transaksi']) ?></div>
            <div class="notif-produk">Produk: <?= htmlspecialchars($n['nama_produk']) ?></div>
            <div class="notif-tanggal">Tanggal Pengembalian: <?= date('d M Y', strtotime($n['tanggal_selesai'])) ?></div>
            <div class="notif-desc">Segera kembalikan barang sewa Anda besok agar tidak terkena denda keterlambatan.</div>
        </div>
    </div>
</li>
<?php endforeach; ?> 