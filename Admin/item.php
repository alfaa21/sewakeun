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

// Ambil parameter search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query produk dengan search
$query = "SELECT produk.*, kategori.nama AS kategori, lokasi.nama AS lokasi 
          FROM produk 
          JOIN kategori ON produk.kategori_id = kategori.id 
          JOIN lokasi ON produk.lokasi_id = lokasi.id";

if (!empty($search)) {
    $query .= " WHERE produk.nama LIKE '%$search%' 
                OR produk.deskripsi LIKE '%$search%' 
                OR kategori.nama LIKE '%$search%' 
                OR lokasi.nama LIKE '%$search%'";
}

$query .= " ORDER BY produk.id DESC";
$produk = mysqli_query($conn, $query);

// Ambil data kategori dan lokasi untuk form
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
    <style>
        /* Existing styles... */

        /* Search Form Styling */
        .search-container {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
            margin: 2rem 0;
        }
        
        .search-form {
            display: flex;
            gap: 12px;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: #f8f9fa;
        }
        
        .search-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
            outline: none;
            background: #fff;
        }
        
        .search-btn {
            padding: 12px 24px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .search-btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        .search-btn i {
            font-size: 0.9em;
        }
        
        .reset-btn {
            padding: 12px 24px;
            background: #f8f9fa;
            color: #666;
            border: 1px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .reset-btn:hover {
            background: #e9ecef;
            color: #333;
            transform: translateY(-1px);
        }
        
        .reset-btn i {
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .search-input,
            .search-btn,
            .reset-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Form Group Styling */
        .form-inline-group {
            display: flex;
            gap: 20px;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .form-kategori,
        .form-lokasi,
        .form-kurir {
            flex: 1;
            min-width: 300px;
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
        }

        .form-inline-group h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-inline-group .form-group {
            margin-bottom: 1rem;
        }

        .form-inline-group .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-inline-group .form-input {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-inline-group .form-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
            outline: none;
        }

        .form-inline-group .btn {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-inline-group .btn-success {
            background: #2ecc71;
            color: #fff;
        }

        .form-inline-group .btn-success:hover {
            background: #27ae60;
        }

        .form-inline-group .btn-info {
            background: #3498db;
            color: #fff;
        }

        .form-inline-group .btn-info:hover {
            background: #2980b9;
        }

        .form-inline-group .btn-warning {
            background: #f1c40f;
            color: #fff;
        }

        .form-inline-group .btn-warning:hover {
            background: #f39c12;
        }

        @media (max-width: 992px) {
            .form-inline-group {
                flex-direction: column;
                gap: 1rem;
            }

            .form-kategori,
            .form-lokasi,
            .form-kurir {
                min-width: 100%;
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
                    <li><a href="Transaksi.php"><i class="fas fa-dollar-sign"></i> Transaksi</a></li>
                    <li><a href="chat_admin.php"><i class="fas fa-comments"></i> Chat</a></li>
                    <li><a href="promo.php"><i class="fas fa-tags"></i> Promo</a></li>
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
                <form class="form-produk" method="POST" enctype="multipart/form-data" onsubmit="setNotif('Produk berhasil ditambahkan!')">
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
                        <label for="harga" class="form-label">Harga (Rp) / hari</label>
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
                        <label for="max_duration" class="form-label">Durasi Maksimal Sewa (hari)</label>
                        <input type="number" id="max_duration" name="max_duration" class="form-input" required min="1">
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="duration_unit" name="duration_unit" value="hari">
                    </div>
                    <button type="submit" name="tambah_produk" class="btn btn-primary">Tambah Produk</button>
                </form>

                <!-- Group form kategori, lokasi, kurir sejajar -->
                <div class="form-inline-group">
                    <form class="form-kategori" method="POST" onsubmit="setNotif('Kategori berhasil ditambahkan!')">
                        <h3>Tambah Kategori</h3>
                        <div class="form-group">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" id="nama_kategori" name="nama_kategori" class="form-input" required>
                        </div>
                        <button type="submit" name="tambah_kategori" class="btn btn-success">Tambah Kategori</button>
                    </form>

                    <form class="form-lokasi" method="POST" onsubmit="setNotif('Lokasi berhasil ditambahkan!')">
                        <h3>Tambah Lokasi</h3>
                        <div class="form-group">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                            <input type="text" id="nama_lokasi" name="nama_lokasi" class="form-input" required>
                        </div>
                        <button type="submit" name="tambah_lokasi" class="btn btn-info">Tambah Lokasi</button>
                    </form>

                    <form class="form-kurir" method="POST" onsubmit="setNotif('Kurir berhasil ditambahkan!')">
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

                <!-- Search Form dipindah ke sini -->
                <div class="search-container">
                    <form method="GET" class="search-form">
                        <input type="text" name="search" class="search-input" 
                               placeholder="Cari produk berdasarkan nama, deskripsi, kategori, atau lokasi..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="item.php" class="reset-btn">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </section>

            <section class="section-table">
                <h3>Daftar Produk</h3>
                <table class="produk-table" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="width:60px;">Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th class="desc-col">Deskripsi</th>
                            <th style="width:90px;">Harga</th>
                            <th style="width:60px;">Stok</th>
                            <th style="width:60px;">Durasi</th>
                            <th style="width:60px;">Unit</th>
                            <th>Lokasi</th>
                            <th class="actions" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($produk) > 0): ?>
                            <?php while($p = mysqli_fetch_assoc($produk)): ?>
                                <tr>
                                    <td><?php if($p['gambar']): ?><img src="../<?= htmlspecialchars($p['gambar']) ?>" alt="Gambar" class="table-img"><?php endif; ?></td>
                                    <td><?= htmlspecialchars($p['nama']) ?></td>
                                    <td><?= htmlspecialchars($p['kategori']) ?></td>
                                    <td class="desc-col" title="<?= htmlspecialchars($p['deskripsi']) ?>"><?= htmlspecialchars($p['deskripsi']) ?></td>
                                    <td>Rp <?= number_format($p['harga'],0,',','.') ?></td>
                                    <td class="badge-col"><span class="badge stock"><?= htmlspecialchars($p['stock']) ?></span></td>
                                    <td><?= htmlspecialchars($p['max_duration']) ?></td>
                                    <td class="badge-col"><span class="badge unit"><?= htmlspecialchars($p['duration_unit']) ?></span></td>
                                    <td><?= htmlspecialchars($p['lokasi']) ?></td>
                                    <td class="actions">
                                        <a href="edit_item.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm" onclick="setNotif('Edit produk, simpan perubahan untuk melihat notifikasi!')">Edit</a>
                                        <a href="?hapus=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="setNotif('Produk berhasil dihapus!'); return confirm('Hapus produk ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <?php if (!empty($search)): ?>
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-3x mb-3"></i>
                                            <p>Tidak ada produk yang ditemukan untuk pencarian "<?= htmlspecialchars($search) ?>"</p>
                                            <a href="item.php" class="btn btn-outline-primary">
                                                <i class="fas fa-times"></i> Reset Pencarian
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <p>Belum ada produk yang ditambahkan</p>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <script>
        // Toast notification logic
        function setNotif(msg) {
            localStorage.setItem('toastNotif', msg);
        }
        window.onload = function() {
            var notif = localStorage.getItem('toastNotif');
            if (notif) {
                showToast(notif);
                localStorage.removeItem('toastNotif');
            }
        };
        function showToast(msg) {
            var toast = document.createElement('div');
            toast.innerText = msg;
            toast.style.position = 'fixed';
            toast.style.bottom = '30px';
            toast.style.right = '30px';
            toast.style.background = '#2ecc71';
            toast.style.color = '#fff';
            toast.style.padding = '16px 28px';
            toast.style.borderRadius = '8px';
            toast.style.fontSize = '1.1em';
            toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
            toast.style.zIndex = 9999;
            toast.style.opacity = 1;
            toast.style.transition = 'opacity 0.5s';
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.opacity = 0;
                setTimeout(function() { toast.remove(); }, 500);
            }, 2200);
        }
    </script>
</body>
</html>