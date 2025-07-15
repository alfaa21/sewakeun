<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Load PromoModel
require_once '../models/PromoModel.php';
$promoModel = new PromoModel($conn);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $nama = mysqli_real_escape_string($conn, $_POST['nama']);
                $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
                $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
                $nilai = (float)$_POST['nilai'];
                $kode_promo = mysqli_real_escape_string($conn, $_POST['kode_promo']);
                $min_transaksi = (float)$_POST['min_transaksi'];
                $max_diskon = !empty($_POST['max_diskon']) ? (float)$_POST['max_diskon'] : null;
                $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
                $produk_id = !empty($_POST['produk_id']) ? (int)$_POST['produk_id'] : null;
                $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
                $tanggal_berakhir = mysqli_real_escape_string($conn, $_POST['tanggal_berakhir']);
                $limit_penggunaan = !empty($_POST['limit_penggunaan']) ? (int)$_POST['limit_penggunaan'] : null;
                
                // Handle image upload
                $gambar = null;
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $target_dir = "../assets/images/promos/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                    $gambar = $target_dir . uniqid('promo_') . '.' . $file_extension;
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar)) {
                        $gambar = str_replace('../', '', $gambar);
                    } else {
                        $gambar = null;
                    }
                }
                
                $query = "INSERT INTO promos (nama, deskripsi, tipe, nilai, kode_promo, min_transaksi, max_diskon, kategori_id, produk_id, tanggal_mulai, tanggal_berakhir, limit_penggunaan, gambar) 
                          VALUES ('$nama', '$deskripsi', '$tipe', $nilai, '$kode_promo', $min_transaksi, " . 
                          ($max_diskon ? $max_diskon : 'NULL') . ", " . 
                          ($kategori_id ? $kategori_id : 'NULL') . ", " . 
                          ($produk_id ? $produk_id : 'NULL') . ", '$tanggal_mulai', '$tanggal_berakhir', " . 
                          ($limit_penggunaan ? $limit_penggunaan : 'NULL') . ", " . 
                          ($gambar ? "'$gambar'" : 'NULL') . ")";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = 'Promo berhasil ditambahkan!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Gagal menambahkan promo: ' . mysqli_error($conn);
                    $_SESSION['message_type'] = 'danger';
                }
                break;
                
            case 'update':
                $id = (int)$_POST['id'];
                $nama = mysqli_real_escape_string($conn, $_POST['nama']);
                $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
                $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
                $nilai = (float)$_POST['nilai'];
                $kode_promo = mysqli_real_escape_string($conn, $_POST['kode_promo']);
                $min_transaksi = (float)$_POST['min_transaksi'];
                $max_diskon = !empty($_POST['max_diskon']) ? (float)$_POST['max_diskon'] : null;
                $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
                $produk_id = !empty($_POST['produk_id']) ? (int)$_POST['produk_id'] : null;
                $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
                $tanggal_berakhir = mysqli_real_escape_string($conn, $_POST['tanggal_berakhir']);
                $limit_penggunaan = !empty($_POST['limit_penggunaan']) ? (int)$_POST['limit_penggunaan'] : null;
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                
                $query = "UPDATE promos SET 
                          nama = '$nama', 
                          deskripsi = '$deskripsi', 
                          tipe = '$tipe', 
                          nilai = $nilai, 
                          kode_promo = '$kode_promo', 
                          min_transaksi = $min_transaksi, 
                          max_diskon = " . ($max_diskon ? $max_diskon : 'NULL') . ", 
                          kategori_id = " . ($kategori_id ? $kategori_id : 'NULL') . ", 
                          produk_id = " . ($produk_id ? $produk_id : 'NULL') . ", 
                          tanggal_mulai = '$tanggal_mulai', 
                          tanggal_berakhir = '$tanggal_berakhir', 
                          limit_penggunaan = " . ($limit_penggunaan ? $limit_penggunaan : 'NULL') . ", 
                          status = '$status' 
                          WHERE id = $id";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = 'Promo berhasil diperbarui!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Gagal memperbarui promo: ' . mysqli_error($conn);
                    $_SESSION['message_type'] = 'danger';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $query = "DELETE FROM promos WHERE id = $id";
                
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = 'Promo berhasil dihapus!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Gagal menghapus promo: ' . mysqli_error($conn);
                    $_SESSION['message_type'] = 'danger';
                }
                break;
        }
        
        header('Location: promo.php');
        exit();
    }
}

// Get all promos for display
$promos_query = "SELECT p.*, k.nama AS nama_kategori, pr.nama AS nama_produk 
                 FROM promos p 
                 LEFT JOIN kategori k ON p.kategori_id = k.id 
                 LEFT JOIN produk pr ON p.produk_id = pr.id 
                 ORDER BY p.created_at DESC";
$promos_result = mysqli_query($conn, $promos_query);

// Get categories and products for form
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama");
$produk_query = mysqli_query($conn, "SELECT * FROM produk ORDER BY nama");

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Promo - Admin</title>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-gift"></i>
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                    <li><a href="item.php"><i class="fas fa-box"></i> Produk</a></li>
                    <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                    <li><a href="pembayaran.php"><i class="fas fa-credit-card"></i> Pembayaran</a></li>
                    <li><a href="promo.php" class="active"><i class="fas fa-gift"></i> Promo</a></li>
                    <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2><i class="fas fa-gift"></i> Manajemen Promo</h2>
                        <p class="text-muted">Kelola promo dan diskon untuk meningkatkan penjualan</p>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Add Promo Button -->
                <div class="row mb-4">
                    <div class="col-12">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                            <i class="fas fa-plus"></i> Tambah Promo Baru
                        </button>
                    </div>
                </div>

                <!-- Promo Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Promo</h5>
                                <h3><?= mysqli_num_rows($promos_result) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Promo Aktif</h5>
                                <h3><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM promos WHERE status = 'aktif' AND tanggal_berakhir >= CURDATE()")) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Promo Expired</h5>
                                <h3><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM promos WHERE tanggal_berakhir < CURDATE()")) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Penggunaan</h5>
                                <h3><?= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM promo_usage"))['total'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promo Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Daftar Promo</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Kode</th>
                                                <th>Tipe</th>
                                                <th>Nilai</th>
                                                <th>Status</th>
                                                <th>Berlaku Sampai</th>
                                                <th>Penggunaan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($promo = mysqli_fetch_assoc($promos_result)): ?>
                                                <tr>
                                                    <td><?= $promo['id'] ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($promo['nama']) ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?= htmlspecialchars($promo['deskripsi']) ?></small>
                                                    </td>
                                                    <td>
                                                        <code><?= htmlspecialchars($promo['kode_promo']) ?></code>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        switch ($promo['tipe']) {
                                                            case 'percentage':
                                                                echo '<span class="badge bg-info">Persentase</span>';
                                                                break;
                                                            case 'fixed':
                                                                echo '<span class="badge bg-warning">Potongan Tetap</span>';
                                                                break;
                                                            case 'free_shipping':
                                                                echo '<span class="badge bg-success">Free Shipping</span>';
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        switch ($promo['tipe']) {
                                                            case 'percentage':
                                                                echo $promo['nilai'] . '%';
                                                                break;
                                                            case 'fixed':
                                                                echo 'Rp ' . number_format($promo['nilai'], 0, ',', '.');
                                                                break;
                                                            case 'free_shipping':
                                                                echo 'Free Shipping';
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status_class = $promo['status'] === 'aktif' ? 'success' : 'secondary';
                                                        $status_text = $promo['status'] === 'aktif' ? 'Aktif' : 'Nonaktif';
                                                        ?>
                                                        <span class="badge bg-<?= $status_class ?>"><?= $status_text ?></span>
                                                    </td>
                                                    <td>
                                                        <?= date('d M Y', strtotime($promo['tanggal_berakhir'])) ?>
                                                        <?php if (strtotime($promo['tanggal_berakhir']) < time()): ?>
                                                            <br><small class="text-danger">Expired</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= $promo['penggunaan_sekarang'] ?>/<?= $promo['limit_penggunaan'] ?: '∞' ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" onclick="editPromo(<?= htmlspecialchars(json_encode($promo)) ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deletePromo(<?= $promo['id'] ?>, '<?= htmlspecialchars($promo['nama']) ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Promo Modal -->
    <div class="modal fade" id="addPromoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Promo Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Promo *</label>
                                    <input type="text" class="form-control" name="nama" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kode Promo *</label>
                                    <input type="text" class="form-control" name="kode_promo" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tipe Promo *</label>
                                    <select class="form-control" name="tipe" required>
                                        <option value="percentage">Persentase (%)</option>
                                        <option value="fixed">Potongan Tetap (Rp)</option>
                                        <option value="free_shipping">Free Shipping</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Nilai *</label>
                                    <input type="number" class="form-control" name="nilai" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Maksimal Diskon (Opsional)</label>
                                    <input type="number" class="form-control" name="max_diskon" step="0.01">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Minimum Transaksi</label>
                                    <input type="number" class="form-control" name="min_transaksi" step="0.01" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Limit Penggunaan (Opsional)</label>
                                    <input type="number" class="form-control" name="limit_penggunaan">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori (Opsional)</label>
                                    <select class="form-control" name="kategori_id">
                                        <option value="">Semua Kategori</option>
                                        <?php while ($kategori = mysqli_fetch_assoc($kategori_query)): ?>
                                            <option value="<?= $kategori['id'] ?>"><?= htmlspecialchars($kategori['nama']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Produk Spesifik (Opsional)</label>
                                    <select class="form-control" name="produk_id">
                                        <option value="">Semua Produk</option>
                                        <?php while ($produk = mysqli_fetch_assoc($produk_query)): ?>
                                            <option value="<?= $produk['id'] ?>"><?= htmlspecialchars($produk['nama']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Mulai *</label>
                                    <input type="date" class="form-control" name="tanggal_mulai" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Berakhir *</label>
                                    <input type="date" class="form-control" name="tanggal_berakhir" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Gambar Promo (Opsional)</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Promo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Promo Modal -->
    <div class="modal fade" id="editPromoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Promo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body" id="editPromoForm">
                        <!-- Form will be loaded here -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deletePromoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus promo <strong id="deletePromoName"></strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deletePromoId">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editPromo(promo) {
            const modal = document.getElementById('editPromoModal');
            const form = document.getElementById('editPromoForm');
            
            form.innerHTML = `
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="${promo.id}">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Promo *</label>
                            <input type="text" class="form-control" name="nama" value="${promo.nama}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kode Promo *</label>
                            <input type="text" class="form-control" name="kode_promo" value="${promo.kode_promo}" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="3">${promo.deskripsi}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tipe Promo *</label>
                            <select class="form-control" name="tipe" required>
                                <option value="percentage" ${promo.tipe === 'percentage' ? 'selected' : ''}>Persentase (%)</option>
                                <option value="fixed" ${promo.tipe === 'fixed' ? 'selected' : ''}>Potongan Tetap (Rp)</option>
                                <option value="free_shipping" ${promo.tipe === 'free_shipping' ? 'selected' : ''}>Free Shipping</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Nilai *</label>
                            <input type="number" class="form-control" name="nilai" step="0.01" value="${promo.nilai}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Maksimal Diskon (Opsional)</label>
                            <input type="number" class="form-control" name="max_diskon" step="0.01" value="${promo.max_diskon || ''}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Minimum Transaksi</label>
                            <input type="number" class="form-control" name="min_transaksi" step="0.01" value="${promo.min_transaksi}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Limit Penggunaan (Opsional)</label>
                            <input type="number" class="form-control" name="limit_penggunaan" value="${promo.limit_penggunaan || ''}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="aktif" ${promo.status === 'aktif' ? 'selected' : ''}>Aktif</option>
                                <option value="nonaktif" ${promo.status === 'nonaktif' ? 'selected' : ''}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Berakhir *</label>
                            <input type="date" class="form-control" name="tanggal_berakhir" value="${promo.tanggal_berakhir}" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Promo</button>
                </div>
            `;
            
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
        
        function deletePromo(id, nama) {
            document.getElementById('deletePromoId').value = id;
            document.getElementById('deletePromoName').textContent = nama;
            
            const modal = new bootstrap.Modal(document.getElementById('deletePromoModal'));
            modal.show();
        }
    </script>
</body>
</html> 