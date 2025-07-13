<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

$id = intval($_GET['id'] ?? 0);
$product_data = null;

if ($id > 0) {
    $query_product = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id");
    $product_data = mysqli_fetch_assoc($query_product);
    if (!$product_data) {
        // Produk tidak ditemukan, redirect
        header('Location: item.php');
        exit();
    }
} else {
    // ID tidak valid, redirect
    header('Location: item.php');
    exit();
}

// Handle update produk
if (isset($_POST['edit_produk'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = intval($_POST['kategori_id']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $lokasi_id = intval($_POST['lokasi_id']);
    $stock = intval($_POST['stock']);
    $max_duration = intval($_POST['max_duration']);
    $duration_unit = mysqli_real_escape_string($conn, $_POST['duration_unit']);

    $gambar = $product_data['gambar']; // Gambar yang sudah ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != '') {
        $target = '../assets/images/' . basename($_FILES['gambar']['name']);
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $gambar = 'assets/images/' . basename($_FILES['gambar']['name']);
        }
    }

    $query = "UPDATE produk SET nama='$nama', kategori_id=$kategori_id, gambar='$gambar', deskripsi='$deskripsi', harga=$harga, lokasi_id=$lokasi_id, stock=$stock, max_duration=$max_duration, duration_unit='$duration_unit' WHERE id=$id";
    mysqli_query($conn, $query);
    header('Location: item.php');
    exit();
}

// Ambil data kategori dan lokasi untuk dropdown
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$lokasi = mysqli_query($conn, "SELECT * FROM lokasi");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Edit Barang</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    .edit-produk-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 32px 28px 24px 28px;
        max-width: 520px;
        margin: 32px auto 0 auto;
    }
    .edit-produk-card h3 {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 22px;
        color: #0d6efd;
    }
    .edit-produk-card label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        color: #222;
    }
    .edit-produk-card input[type="text"],
    .edit-produk-card input[type="number"],
    .edit-produk-card input[type="file"],
    .edit-produk-card select,
    .edit-produk-card textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 16px;
        font-size: 1rem;
        background: #fafbfc;
        transition: border 0.2s;
    }
    .edit-produk-card input[type="text"]:focus,
    .edit-produk-card input[type="number"]:focus,
    .edit-produk-card textarea:focus,
    .edit-produk-card select:focus {
        border: 1.5px solid #0d6efd;
        outline: none;
        background: #fff;
    }
    .edit-produk-card img {
        max-width: 140px;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    }
    .edit-produk-card button[type="submit"] {
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1.05em;
        cursor: pointer;
        margin-top: 8px;
        transition: background 0.2s;
    }
    .edit-produk-card button[type="submit"]:hover {
        background: #2563eb;
    }
    .edit-produk-card .btn-back {
        display: inline-block;
        margin-left: 12px;
        background: #e0e7ef;
        color: #0d6efd;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 1em;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }
    .edit-produk-card .btn-back:hover {
        background: #d1e3fa;
        color: #0a58ca;
    }
    @media (max-width: 700px) {
        .edit-produk-card {
            padding: 18px 8px 16px 8px;
            max-width: 98vw;
        }
    }
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
                    <li><a href="item.php" class="active"><i class="fas fa-boxes"></i> Barang</a></li>
                    <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                    <li><a href="pesanan.php"><i class="fas fa-receipt"></i> Pesanan</a></li>
                    <li><a href="pembayaran.php"><i class="fas fa-dollar-sign"></i> Pembayaran</a></li>
                    <li><a href="statistik.php"><i class="fas fa-chart-line"></i> Statistik</a></li>
                    <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h2>Edit Produk</h2>
                <div class="user-info">
                    <span>Halo, Jane Doe</span>
                    <img src="https://via.placeholder.com/40/CCCCCC/FFFFFF?text=JD" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section>
                <form class="edit-produk-card" method="POST" enctype="multipart/form-data">
                    <h3>Edit Produk: <?= htmlspecialchars($product_data['nama']) ?></h3>
                    <label>Nama Produk</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($product_data['nama']) ?>" required>
                    <label>Kategori</label>
                    <select name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php while($k = mysqli_fetch_assoc($kategori)): ?>
                        <option value="<?= $k['id'] ?>" <?= ($k['id'] == $product_data['kategori_id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <label>Gambar Produk Saat Ini</label>
                    <?php if($product_data['gambar']): ?>
                        <img src="../<?= htmlspecialchars($product_data['gambar']) ?>" alt="Gambar Produk" style="max-width: 150px; display: block; margin-bottom: 10px;">
                    <?php else: ?>
                        <p>Tidak ada gambar.</p>
                    <?php endif; ?>
                    <label>Ubah Gambar Produk (opsional)</label>
                    <input type="file" name="gambar" accept="image/*">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3" required><?= htmlspecialchars($product_data['deskripsi']) ?></textarea>
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" value="<?= htmlspecialchars($product_data['harga']) ?>" required>
                    <label>Stok</label>
                    <input type="number" name="stock" value="<?= htmlspecialchars($product_data['stock']) ?>" required min="0">
                    <label>Durasi Maksimal Sewa</label>
                    <input type="number" name="max_duration" value="<?= htmlspecialchars($product_data['max_duration']) ?>" required min="1">
                    <label>Unit Durasi</label>
                    <select name="duration_unit" required>
                        <option value="hari" <?= ($product_data['duration_unit'] == 'hari') ? 'selected' : '' ?>>Hari</option>
                        <option value="minggu" <?= ($product_data['duration_unit'] == 'minggu') ? 'selected' : '' ?>>Minggu</option>
                        <option value="bulan" <?= ($product_data['duration_unit'] == 'bulan') ? 'selected' : '' ?>>Bulan</option>
                    </select>
                    <label>Lokasi</label>
                    <select name="lokasi_id" required>
                        <option value="">Pilih Lokasi</option>
                        <?php mysqli_data_seek($lokasi, 0); while($l = mysqli_fetch_assoc($lokasi)): ?>
                        <option value="<?= $l['id'] ?>" <?= ($l['id'] == $product_data['lokasi_id']) ? 'selected' : '' ?>><?= htmlspecialchars($l['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" name="edit_produk">Update Produk</button>
                    <a href="item.php" class="btn-back">Batal</a>
                </form>
            </section>
        </main>
    </div>
</body>
</html> 