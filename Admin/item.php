<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Handle tambah produk
if (isset($_POST['tambah_produk'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = intval($_POST['kategori_id']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $lokasi_id = intval($_POST['lokasi_id']);
    $stock = intval($_POST['stock']);
    $max_duration = intval($_POST['max_duration']);
    $duration_unit = mysqli_real_escape_string($conn, $_POST['duration_unit']);

    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != '') {
        $target = '../assets/images/' . basename($_FILES['gambar']['name']);
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $gambar = 'assets/images/' . basename($_FILES['gambar']['name']);
        }
    }
    $query = "INSERT INTO produk (nama, kategori_id, gambar, deskripsi, harga, lokasi_id, stock, max_duration, duration_unit) VALUES ('$nama', $kategori_id, '$gambar', '$deskripsi', $harga, $lokasi_id, $stock, $max_duration, '$duration_unit')";
    mysqli_query($conn, $query);
    header('Location: item.php');
    exit();
}
// Handle hapus produk
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    header('Location: item.php');
    exit();
}
// Handle tambah lokasi
if (isset($_POST['tambah_lokasi'])) {
    $nama_lokasi = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
    if ($nama_lokasi !== '') {
        mysqli_query($conn, "INSERT INTO lokasi (nama) VALUES ('$nama_lokasi')");
    }
    header('Location: item.php');
    exit();
}
// Handle tambah kategori
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    if ($nama_kategori !== '') {
        mysqli_query($conn, "INSERT INTO kategori (nama) VALUES ('$nama_kategori')");
    }
    header('Location: item.php');
    exit();
}
// Handle tambah kurir
if (isset($_POST['tambah_kurir'])) {
    $nama_kurir = mysqli_real_escape_string($conn, $_POST['nama_kurir']);
    $biaya_kurir = intval($_POST['biaya_kurir']);
    if ($nama_kurir !== '' && $biaya_kurir > 0) {
        mysqli_query($conn, "INSERT INTO kurir (nama, biaya) VALUES ('$nama_kurir', $biaya_kurir)");
    }
    header('Location: item.php');
    exit();
}
// Ambil data produk, kategori, dan lokasi
$produk = mysqli_query($conn, "SELECT produk.*, kategori.nama AS kategori, lokasi.nama AS lokasi FROM produk JOIN kategori ON produk.kategori_id = kategori.id JOIN lokasi ON produk.lokasi_id = lokasi.id ORDER BY produk.id DESC");
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$lokasi = mysqli_query($conn, "SELECT * FROM lokasi");

// Ambil data admin untuk foto profil
$admin_data = null;
if (isset($_SESSION['user_id'])) {
    $admin_id = $_SESSION['user_id'];
    $admin_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$admin_id AND role='admin'");
    if ($admin_query) {
        $admin_data = mysqli_fetch_assoc($admin_query);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kelola Barang</title>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #333;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #2c3e50; /* Dark blue-gray */
            color: #ecf0f1; /* Light gray for text */
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar-logo i {
            font-size: 2.5em;
            color: #3498db; /* Blue for icon */
        }

        .sidebar-logo h3 {
            margin: 0;
            font-size: 1.4em;
            font-weight: 600;
            color: #ecf0f1;
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1; /* Allows nav to take available space */
        }

        .sidebar-nav li {
            margin-bottom: 10px;
        }

        .sidebar-nav a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar-nav a i {
            margin-right: 10px;
            font-size: 1.1em;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: #34495e; /* Slightly lighter dark blue-gray */
            color: #ffffff;
        }

        .sidebar-footer {
            margin-top: auto; /* Pushes the logout to the bottom */
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer a {
            color: #e74c3c; /* Red for logout */
            text-decoration: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar-footer a:hover {
            background-color: #c0392b; /* Darker red */
            color: #ffffff;
        }

        .sidebar-footer i {
            margin-right: 10px;
            font-size: 1.1em;
        }

        /* Main Content Styling */
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: #f4f7f6;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .main-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.8em;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info span {
            font-weight: 500;
            color: #555;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3498db;
        }

        /* Section Styling */
        section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        section h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 1.5em;
            font-weight: 600;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.95em;
        }

        table thead th {
            background-color: #f8f8f8;
            color: #555;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #ddd;
        }

        table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #444;
        }

        table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
            color: #fff;
            text-align: center;
            display: inline-block;
        }

        /* Status colors from previous examples */
        .status-badge.pending { background-color: #f39c12; } /* Orange */
        .status-badge.processed { background-color: #3498db; } /* Blue, for processed */
        .status-badge.shipped { background-color: #17a2b8; } /* Cyan, for shipped */
        .status-badge.completed { background-color: #2ecc71; } /* Green */
        .status-badge.cancelled { background-color: #e74c3c; } /* Red */

        /* Button Styling */
        .btn {
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #3498db; /* Blue */
        }

        .btn-primary:hover {
            background-color: #2980b9; /* Darker blue */
        }

        .btn-info {
            background-color: #17a2b8; /* Cyan for View Details */
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .btn-success {
            background-color: #2ecc71; /* Green */
        }

        .btn-success:hover {
            background-color: #27ae60; /* Darker green */
        }

        .btn-danger {
            background-color: #e74c3c; /* Red */
        }

        .btn-danger:hover {
            background-color: #c0392b; /* Darker red */
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons .btn {
            padding: 8px 12px;
            font-size: 0.85em;
        }

        /* Search Bar Specific Styles */
        .orders-management .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap; /* Allows wrapping on smaller screens */
        }

        .orders-management .search-bar {
            flex-grow: 1; /* Allows search bar to take available space */
            max-width: 300px; /* Limit search bar width */
            position: relative;
        }

        .orders-management .search-bar input {
            width: 100%;
            padding: 10px 15px;
            padding-right: 40px; /* Space for icon */
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .orders-management .search-bar i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .orders-management .filter-dropdown select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            background-color: #fff;
            cursor: pointer;
        }

        /* Modal Styling */
        .modal-bg {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }

        .modal {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 700px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
            animation-name: animatetop;
            animation-duration: 0.4s
        }

        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {top:-300px; opacity:0} 
            to {top:0; opacity:1}
        }

        @keyframes animatetop {
            from {top:-300px; opacity:0}
            to {top:0; opacity:1}
        }

        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 15px;
            top: 10px;
        }

        .modal-close:hover,
        .modal-close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal h3 {
            margin-top: 0; 
            color: #2c3e50;
        }

        .modal p strong { color: #555; }
        .modal p { margin-bottom: 10px; }

        .modal table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .modal table th,
        .modal table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .modal table th {
            background-color: #f2f2f2;
        }

        .modal img.proof-img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .form-inline-group {
            display: flex;
            gap: 24px;
            margin-top: 32px;
            flex-wrap: wrap;
        }
        .form-inline-group form {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 18px 18px 10px 18px;
            min-width: 260px;
            flex: 1 1 260px;
            max-width: 340px;
            margin-bottom: 12px;
        }
        .form-inline-group h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 12px;
        }
        @media (max-width: 900px) {
            .form-inline-group {
                flex-direction: column;
                gap: 12px;
            }
            .form-inline-group form {
                max-width: 100%;
            }
        }

</style>
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
                    
                    <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h2>Kelola Produk</h2>
                <div class="user-info">
                    <span>Halo, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                    <img src="../<?= htmlspecialchars($admin_data['foto_profil'] ?? 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section class="section-form">
                <form class="form-produk" method="POST" enctype="multipart/form-data">
                    <h3>Tambah Produk</h3>
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Produk</label>
                        <input type="text" id="nama" name="nama" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <select id="kategori_id" name="kategori_id" class="form-input" required>
                            <option value="">Pilih Kategori</option>
                            <?php 
                            // refresh data kategori untuk form produk
                            $kategori2 = mysqli_query($conn, "SELECT * FROM kategori");
                            while($k = mysqli_fetch_assoc($kategori2)): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <input type="file" id="gambar" name="gambar" class="form-input" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="form-input" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="harga" class="form-label">Harga (Rp)</label>
                        <input type="number" id="harga" name="harga" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="lokasi_id" class="form-label">Lokasi</label>
                        <select id="lokasi_id" name="lokasi_id" class="form-input" required>
                            <option value="">Pilih Lokasi</option>
                            <?php mysqli_data_seek($lokasi, 0); while($l = mysqli_fetch_assoc($lokasi)): ?>
                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" id="stock" name="stock" class="form-input" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="max_duration" class="form-label">Durasi Maksimal Sewa</label>
                        <input type="number" id="max_duration" name="max_duration" class="form-input" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="duration_unit" class="form-label">Unit Durasi</label>
                        <select id="duration_unit" name="duration_unit" class="form-input" required>
                            <option value="hari">Hari</option>
                            <option value="minggu">Minggu</option>
                            <option value="bulan">Bulan</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_produk" class="btn btn-primary">Tambah Produk</button>
                </form>
                <!-- Group form kategori, lokasi, kurir sejajar -->
                <div class="form-inline-group">
                    <form class="form-kategori" method="POST">
                        <h3>Tambah Kategori</h3>
                        <div class="form-group">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" id="nama_kategori" name="nama_kategori" class="form-input" required>
                        </div>
                        <button type="submit" name="tambah_kategori" class="btn btn-success">Tambah Kategori</button>
                    </form>
                    <form class="form-lokasi" method="POST">
                        <h3>Tambah Lokasi</h3>
                        <div class="form-group">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                            <input type="text" id="nama_lokasi" name="nama_lokasi" class="form-input" required>
                        </div>
                        <button type="submit" name="tambah_lokasi" class="btn btn-info">Tambah Lokasi</button>
                    </form>
                    <form class="form-kurir" method="POST">
                        <h3>Tambah Kurir</h3>
                        <div class="form-group">
                            <label for="nama_kurir" class="form-label">Nama Kurir</label>
                            <input type="text" id="nama_kurir" name="nama_kurir" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="biaya_kurir" class="form-label">Biaya Kurir (Rp)</label>
                            <input type="number" id="biaya_kurir" name="biaya_kurir" class="form-input" min="1" required>
                        </div>
                        <button type="submit" name="tambah_kurir" class="btn btn-warning">Tambah Kurir</button>
                    </form>
                </div>
            </section>

            <section class="section-table">
                <h3>Daftar Produk</h3>
                <table class="produk-table" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Durasi Maks</th>
                            <th>Unit</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = mysqli_fetch_assoc($produk)): ?>
                        <tr>
                            <td><?php if($p['gambar']): ?><img src="../<?= htmlspecialchars($p['gambar']) ?>" alt="Gambar" class="table-img"><?php endif; ?></td>
                            <td><?= htmlspecialchars($p['nama']) ?></td>
                            <td><?= htmlspecialchars($p['kategori']) ?></td>
                            <td><?= htmlspecialchars($p['deskripsi']) ?></td>
                            <td>Rp <?= number_format($p['harga'],0,',','.') ?></td>
                            <td><?= htmlspecialchars($p['stock']) ?></td>
                            <td><?= htmlspecialchars($p['max_duration']) ?></td>
                            <td><?= htmlspecialchars($p['duration_unit']) ?></td>
                            <td><?= htmlspecialchars($p['lokasi']) ?></td>
                            <td>
                                <a href="edit_item.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?hapus=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">Hapus</a>
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