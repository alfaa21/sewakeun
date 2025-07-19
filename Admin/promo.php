<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Handle tambah/edit/hapus promo
$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit']);
    $q = mysqli_query($conn, "SELECT * FROM promos WHERE id=$edit_id");
    $edit_data = mysqli_fetch_assoc($q);
}
if (isset($_POST['save_promo'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
    $nilai = floatval($_POST['nilai']);
    $kode_promo = mysqli_real_escape_string($conn, $_POST['kode_promo']);
    $min_transaksi = floatval($_POST['min_transaksi']);
    $max_diskon = $_POST['max_diskon'] !== '' ? floatval($_POST['max_diskon']) : 'NULL';
    $kategori_id = $_POST['kategori_id'] !== '' ? intval($_POST['kategori_id']) : 'NULL';
    $produk_id = $_POST['produk_id'] !== '' ? intval($_POST['produk_id']) : 'NULL';
    $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
    $tanggal_berakhir = mysqli_real_escape_string($conn, $_POST['tanggal_berakhir']);
    $limit_penggunaan = $_POST['limit_penggunaan'] !== '' ? intval($_POST['limit_penggunaan']) : 'NULL';
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    if ($id > 0) {
        $q = "UPDATE promos SET nama='$nama', deskripsi='$deskripsi', tipe='$tipe', nilai=$nilai, kode_promo='$kode_promo', min_transaksi=$min_transaksi, max_diskon=$max_diskon, kategori_id=$kategori_id, produk_id=$produk_id, tanggal_mulai='$tanggal_mulai', tanggal_berakhir='$tanggal_berakhir', limit_penggunaan=$limit_penggunaan, status='$status' WHERE id=$id";
    } else {
        $q = "INSERT INTO promos (nama, deskripsi, tipe, nilai, kode_promo, min_transaksi, max_diskon, kategori_id, produk_id, tanggal_mulai, tanggal_berakhir, limit_penggunaan, status) VALUES ('$nama', '$deskripsi', '$tipe', $nilai, '$kode_promo', $min_transaksi, $max_diskon, $kategori_id, $produk_id, '$tanggal_mulai', '$tanggal_berakhir', $limit_penggunaan, '$status')";
    }
    mysqli_query($conn, $q);
    header('Location: promo.php');
    exit();
}
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM promos WHERE id=$id");
    header('Location: promo.php');
    exit();
}
// Ambil data promo
$promos = mysqli_query($conn, "SELECT p.*, k.nama AS nama_kategori, pr.nama AS nama_produk FROM promos p LEFT JOIN kategori k ON p.kategori_id=k.id LEFT JOIN produk pr ON p.produk_id=pr.id ORDER BY p.created_at DESC");
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$produk = mysqli_query($conn, "SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Promo - Admin</title>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .promo-table th, .promo-table td { padding: 10px 8px; text-align: left; }
        .promo-table th { background: #f6f8fa; font-weight: 600; }
        .promo-table td { font-size: 0.97em; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.92em; font-weight: 500; color: #fff; margin: 0 2px; }
        .badge.aktif { background: #2ecc71; }
        .badge.nonaktif { background: #e74c3c; }
        .badge.percentage { background: #f39c12; }
        .badge.fixed { background: #3498db; }
        .badge.free_shipping { background: #27ae60; }
        .promo-actions a, .promo-actions button { margin-right: 6px; }
        .promo-form-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 28px 24px; max-width: 540px; margin: 24px auto; }
        .promo-form-card h3 { margin-top: 0; margin-bottom: 18px; }
        .promo-form-card .form-group { margin-bottom: 14px; }
        .promo-form-card label { font-weight: 500; color: #444; margin-bottom: 2px; display: block; }
        .promo-form-card input, .promo-form-card select, .promo-form-card textarea { width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1em; background: #fafbfc; color: #333; margin-bottom: 2px; }
        .promo-form-card input:focus, .promo-form-card select:focus, .promo-form-card textarea:focus { border: 1.5px solid #3498db; outline: none; background: #fff; }
        .promo-form-card button { margin-top: 8px; }
        .promo-table { width: 100%; border-collapse: collapse; margin-top: 32px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
        .promo-table tbody tr:hover { background: #eaf6ff; }
        .promo-table th, .promo-table td { border-bottom: 1px solid #eee; }
        .promo-table th:last-child, .promo-table td:last-child { text-align: center; }
        @media (max-width: 700px) { .promo-form-card, .promo-table { padding: 10px 2vw; } }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar" style="background:#0d6efd!important;color:#fff!important;">
        <div class="sidebar-logo">
            <i class="fas fa-box-open"></i>
            <h3>Sewakeun Admin</h3>
        </div>
        <nav class="sidebar-nav">
        <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="item.php"><i class="fas fa-boxes"></i> Barang</a></li>
                <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                <li><a href="pesanan.php"><i class="fas fa-receipt"></i> Pesanan</a></li>
                <li><a href="Transaksi.php"><i class="fas fa-dollar-sign"></i> Transaksi</a></li>
                <li><a href="chat_admin.php"><i class="fas fa-comments"></i> Chat</a></li>
                <li><a href="promo.php" class="active"><i class="fas fa-tags"></i> Promo</a></li>
                <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>
    <main class="main-content">
        <header class="main-header">
            <h2>Kelola Promo</h2>
        </header>
        <?php if ($edit_mode): ?>
        <div class="promo-form-card">
            <h3><?= $edit_data ? 'Edit Promo' : 'Tambah Promo' ?></h3>
            <form method="post">
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                <div class="form-group">
                    <label for="nama">Nama Promo</label>
                    <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="2" required><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="tipe">Tipe Promo</label>
                    <select name="tipe" id="tipe" required>
                        <option value="percentage" <?= (isset($edit_data['tipe']) && $edit_data['tipe']==='percentage')?'selected':'' ?>>Persentase (%)</option>
                        <option value="fixed" <?= (isset($edit_data['tipe']) && $edit_data['tipe']==='fixed')?'selected':'' ?>>Potongan Tetap (Rp)</option>
                        <option value="free_shipping" <?= (isset($edit_data['tipe']) && $edit_data['tipe']==='free_shipping')?'selected':'' ?>>Gratis Ongkir</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nilai">Nilai Promo</label>
                    <input type="number" name="nilai" id="nilai" value="<?= htmlspecialchars($edit_data['nilai'] ?? 0) ?>" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="kode_promo">Kode Promo</label>
                    <input type="text" name="kode_promo" id="kode_promo" value="<?= htmlspecialchars($edit_data['kode_promo'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="min_transaksi">Minimum Transaksi</label>
                    <input type="number" name="min_transaksi" id="min_transaksi" value="<?= htmlspecialchars($edit_data['min_transaksi'] ?? 0) ?>" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="max_diskon">Maksimal Diskon (untuk persentase)</label>
                    <input type="number" name="max_diskon" id="max_diskon" value="<?= htmlspecialchars($edit_data['max_diskon'] ?? '') ?>" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="kategori_id">Kategori (Opsional)</label>
                    <select name="kategori_id" id="kategori_id">
                        <option value="">Semua Kategori</option>
                        <?php mysqli_data_seek($kategori, 0); while($k = mysqli_fetch_assoc($kategori)): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($edit_data['kategori_id']) && $edit_data['kategori_id']==$k['id'])?'selected':'' ?>><?= htmlspecialchars($k['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="produk_id">Produk (Opsional)</label>
                    <select name="produk_id" id="produk_id">
                        <option value="">Semua Produk</option>
                        <?php mysqli_data_seek($produk, 0); while($p = mysqli_fetch_assoc($produk)): ?>
                        <option value="<?= $p['id'] ?>" <?= (isset($edit_data['produk_id']) && $edit_data['produk_id']==$p['id'])?'selected':'' ?>><?= htmlspecialchars($p['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="<?= htmlspecialchars($edit_data['tanggal_mulai'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="tanggal_berakhir">Tanggal Berakhir</label>
                    <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" value="<?= htmlspecialchars($edit_data['tanggal_berakhir'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="limit_penggunaan">Limit Penggunaan</label>
                    <input type="number" name="limit_penggunaan" id="limit_penggunaan" value="<?= htmlspecialchars($edit_data['limit_penggunaan'] ?? '') ?>" min="0">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" required>
                        <option value="aktif" <?= (isset($edit_data['status']) && $edit_data['status']==='aktif')?'selected':'' ?>>Aktif</option>
                        <option value="nonaktif" <?= (isset($edit_data['status']) && $edit_data['status']==='nonaktif')?'selected':'' ?>>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" name="save_promo" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Promo</button>
                <a href="promo.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
        <?php endif; ?>
        <section>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <h3 style="margin:0;">Daftar Promo</h3>
                <a href="promo.php?edit=0" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Promo</a>
            </div>
            <table class="promo-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        <th>Min. Transaksi</th>
                        <th>Maks. Diskon</th>
                        <th>Kategori</th>
                        <th>Produk</th>
                        <th>Periode</th>
                        <th>Limit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($promo = mysqli_fetch_assoc($promos)): ?>
                    <tr>
                        <td><?= htmlspecialchars($promo['nama']) ?></td>
                        <td><span class="badge <?= $promo['status'] ?>"><?= htmlspecialchars($promo['kode_promo']) ?></span></td>
                        <td><span class="badge <?= $promo['tipe'] ?>"><?= $promo['tipe'] ?></span></td>
                        <td><?= $promo['tipe']==='percentage' ? $promo['nilai'].'%' : ($promo['tipe']==='fixed' ? 'Rp '.number_format($promo['nilai'],0,',','.') : 'Gratis Ongkir') ?></td>
                        <td><?= $promo['min_transaksi']>0 ? 'Rp '.number_format($promo['min_transaksi'],0,',','.') : '-' ?></td>
                        <td><?= $promo['max_diskon']>0 ? 'Rp '.number_format($promo['max_diskon'],0,',','.') : '-' ?></td>
                        <td><?= $promo['kategori_id'] ? htmlspecialchars($promo['nama_kategori']) : '-' ?></td>
                        <td><?= $promo['produk_id'] ? htmlspecialchars($promo['nama_produk']) : '-' ?></td>
                        <td><?= date('d M y', strtotime($promo['tanggal_mulai'])) . ' - ' . date('d M y', strtotime($promo['tanggal_berakhir'])) ?></td>
                        <td><?= $promo['limit_penggunaan'] ? $promo['limit_penggunaan'] : '-' ?></td>
                        <td><span class="badge <?= $promo['status'] ?>"><?= ucfirst($promo['status']) ?></span></td>
                        <td class="promo-actions">
                            <a href="promo.php?edit=<?= $promo['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="promo.php?hapus=<?= $promo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus promo ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html> 