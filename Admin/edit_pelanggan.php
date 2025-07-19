<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

$id = intval($_GET['id'] ?? 0);
$customer_data = null;

if ($id > 0) {
    $query_customer = mysqli_query($conn, "SELECT * FROM users WHERE id=$id AND role='user'");
    $customer_data = mysqli_fetch_assoc($query_customer);
    if (!$customer_data) {
        header('Location: pelanggan.php');
        exit();
    }
} else {
    header('Location: pelanggan.php');
    exit();
}

// Handle update pelanggan
if (isset($_POST['edit_pelanggan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $foto_profil_path = $customer_data['foto_profil']; // Gambar yang sudah ada
    
    // Direktori upload untuk foto profil
    $upload_dir = '../assets/images/profile_pictures/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Buat direktori jika tidak ada
    }

    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['name'] != '') {
        $file_tmp_name = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $max_file_size = 2 * 1024 * 1024; // 2MB

        if (in_array($file_ext, $allowed_extensions) === false) {
            // Handle error, mungkin redirect dengan pesan
            $_SESSION['message'] = 'Ekstensi file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF.';
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_pelanggan.php?id=' . $id);
            exit();
        }

        if ($file_size > $max_file_size) {
            $_SESSION['message'] = 'Ukuran file terlalu besar, maksimal 2MB.';
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_pelanggan.php?id=' . $id);
            exit();
        }
        
        $new_file_name = uniqid('profile_') . '.' . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp_name, $upload_path)) {
            // Hapus gambar lama jika bukan default dan ada
            if ($customer_data['foto_profil'] && $customer_data['foto_profil'] != 'assets/images/default_profile.png' && file_exists('../' . $customer_data['foto_profil'])) {
                unlink('../' . $customer_data['foto_profil']);
            }
            $foto_profil_path = 'assets/images/profile_pictures/' . $new_file_name; // Simpan path relatif ke database
        } else {
            $_SESSION['message'] = 'Gagal mengunggah foto profil.';
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_pelanggan.php?id=' . $id);
            exit();
        }
    }

    $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', email='$email', no_hp='$no_hp', alamat='$alamat', foto_profil='$foto_profil_path' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Data pelanggan berhasil diperbarui.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Gagal memperbarui data pelanggan: ' . mysqli_error($conn) . '.';
        $_SESSION['message_type'] = 'danger';
    }
    header('Location: pelanggan.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Edit Pelanggan</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .customer-profile-picture-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px auto;
            display: block;
            border: 3px solid #007bff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-floating label {
            color: #6c757d;
        }
        .form-produk .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }
        .form-produk .btn-back:hover {
            background-color: #5a6268;
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
                    <li><a href="item.php"><i class="fas fa-boxes"></i> Barang</a></li>
                    <li><a href="pelanggan.php" class="active"><i class="fas fa-users"></i> Pelanggan</a></li>
                    <li><a href="pesanan.php"><i class="fas fa-receipt"></i> Pesanan</a></li>
                    <li><a href="Transaksi.php"><i class="fas fa-dollar-sign"></i> Transaksi</a></li>
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
                <h2>Edit Pelanggan</h2>
                <div class="user-info">
                    <span>Halo, Admin</span>
                    <img src="../assets/images/default_profile.png" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section>
                <form class="form-produk" method="POST" enctype="multipart/form-data">
                    <h3>Edit Pelanggan: <?= htmlspecialchars($customer_data['username']) ?></h3>

                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php 
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        ?>
                    <?php endif; ?>

                    <div class="mb-3 text-center">
                        <label for="foto_profil" class="form-label d-block mb-2">Gambar Profil Saat Ini</label>
                        <?php if($customer_data['foto_profil'] && file_exists('../' . $customer_data['foto_profil'])): ?>
                            <img src="../<?= htmlspecialchars($customer_data['foto_profil']) ?>" alt="Profile Picture" class="customer-profile-picture-preview">
                        <?php else: ?>
                            <img src="../assets/images/default_profile.png" alt="Default Profile Picture" class="customer-profile-picture-preview">
                        <?php endif; ?>
                        <input type="file" class="form-control mt-2 mx-auto" id="foto_profil" name="foto_profil" accept="image/*" style="max-width: 300px;">
                        <small class="form-text text-muted">Opsional. Format gambar: JPG, JPEG, PNG, GIF. Max 2MB.</small>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= htmlspecialchars($customer_data['username']) ?>" required>
                        <label for="username">Nama Pengguna</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" value="<?= htmlspecialchars($customer_data['nama_lengkap']) ?>" required>
                        <label for="nama_lengkap">Nama Lengkap</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($customer_data['email']) ?>" required>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Nomor Telepon" value="<?= htmlspecialchars($customer_data['no_hp']) ?>">
                        <label for="no_hp">No. Telepon</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat" style="height: 100px" required><?= htmlspecialchars($customer_data['alamat']) ?></textarea>
                        <label for="alamat">Alamat</label>
                    </div>
                    
                    <div class="d-flex justify-content-between gap-2 mt-4">
                        <button type="submit" name="edit_pelanggan" class="btn btn-primary flex-fill">Update Pelanggan</button>
                        <a href="pelanggan.php" class="btn-back flex-fill">Batal</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html> 