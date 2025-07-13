<?php
include_once '../includes/session_bootstrap.php';
include '../koneksi.php';

// Fetch current admin user data
$admin_data = null;
if (isset($_SESSION['user_id'])) {
    $admin_id = $_SESSION['user_id'];
    $admin_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$admin_id AND role='admin'");
    if ($admin_query) {
        $admin_data = mysqli_fetch_assoc($admin_query);
    }
}

// Fetch settings
$settings = [];
$settings_query = mysqli_query($conn, "SELECT * FROM settings");
if ($settings_query) {
    while($row = mysqli_fetch_assoc($settings_query)) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
}

// Handle update general settings
if (isset($_POST['update_general_settings'])) {
    $appName = mysqli_real_escape_string($conn, $_POST['appName']);
    $adminEmail = mysqli_real_escape_string($conn, $_POST['adminEmail']);

    // Update or insert appName
    mysqli_query($conn, "INSERT INTO settings (setting_name, setting_value) VALUES ('app_name', '$appName') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    // Update or insert adminEmail
    mysqli_query($conn, "INSERT INTO settings (setting_name, setting_value) VALUES ('email_support', '$adminEmail') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    $_SESSION['success_message'] = "Pengaturan umum berhasil diperbarui.";
    header('Location: setting.php');
    exit();
}

// Handle update notification settings
if (isset($_POST['update_notification_settings'])) {
    $emailNotif = isset($_POST['emailNotif']) ? 1 : 0;
    $smsNotif = isset($_POST['smsNotif']) ? 1 : 0;
    $notifEmailRecipient = mysqli_real_escape_string($conn, $_POST['notifEmailRecipient']);

    mysqli_query($conn, "INSERT INTO settings (setting_name, setting_value) VALUES ('email_notif', '$emailNotif') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    mysqli_query($conn, "INSERT INTO settings (setting_name, setting_value) VALUES ('sms_notif', '$smsNotif') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    mysqli_query($conn, "INSERT INTO settings (setting_name, setting_value) VALUES ('notif_email_recipient', '$notifEmailRecipient') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    $_SESSION['success_message'] = "Pengaturan notifikasi berhasil diperbarui.";
    header('Location: setting.php');
    exit();
}

// Handle change admin password
if (isset($_POST['change_admin_password'])) {
    $currentPassword = mysqli_real_escape_string($conn, $_POST['currentPassword']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['newPassword']);
    $confirmNewPassword = mysqli_real_escape_string($conn, $_POST['confirmNewPassword']);

    if (!password_verify($currentPassword, $admin_data['password'])) {
        $_SESSION['error_message'] = "Kata sandi saat ini salah.";
    } elseif ($newPassword !== $confirmNewPassword) {
        $_SESSION['error_message'] = "Konfirmasi kata sandi baru tidak cocok.";
    } else {
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed_password' WHERE id=$admin_id");
        $_SESSION['success_message'] = "Kata sandi berhasil diubah.";
    }
    header('Location: setting.php');
    exit();
}

// Handle update admin profile
if (isset($_POST['update_admin_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $foto_profil_path = $admin_data['foto_profil'];
    $upload_dir = '../assets/images/profile_pictures/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['name'] != '') {
        $file_tmp_name = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $max_file_size = 2 * 1024 * 1024; // 2MB
        if (in_array($file_ext, $allowed_extensions) && $_FILES['foto_profil']['size'] <= $max_file_size) {
            $new_file_name = uniqid('profile_') . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                $foto_profil_path = 'assets/images/profile_pictures/' . $new_file_name;
                // Hapus foto lama jika ada
                if (!empty($admin_data['foto_profil']) && file_exists('../' . $admin_data['foto_profil'])) {
                    unlink('../' . $admin_data['foto_profil']);
                }
            }
        }
    }
    $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', email='$email', no_hp='$no_hp', alamat='$alamat', foto_profil='$foto_profil_path' WHERE id=$admin_id AND role='admin'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = 'Profil admin berhasil diperbarui.';
    } else {
        $_SESSION['error_message'] = 'Gagal memperbarui profil admin: ' . mysqli_error($conn) . '.';
    }
    header('Location: setting.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaturan</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="pembayaran.php"><i class="fas fa-dollar-sign"></i> Pembayaran</a></li> 
                    <li><a href="setting.php" class="active"><i class="fas fa-cog"></i> Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h2>Pengaturan Sistem</h2>
                <div class="user-info">
                    <span>Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <img src="https://via.placeholder.com/40/CCCCCC/FFFFFF?text=AD" alt="User Avatar" class="user-avatar">
                </div>
            </header>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert success">
                    <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert error">
                    <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <section class="settings-management">
                <h3>Informasi Umum</h3>
                <form class="settings-form" method="POST">
                    <div class="form-group">
                        <label for="appName">Nama Aplikasi:</label>
                        <input type="text" id="appName" name="appName" value="<?= htmlspecialchars($settings['app_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="appVersion">Versi Aplikasi:</label>
                        <input type="text" id="appVersion" name="appVersion" value="<?= htmlspecialchars($settings['appVersion'] ?? '1.0.0') ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="adminEmail">Email Admin Utama:</label>
                        <input type="email" id="adminEmail" name="adminEmail" value="<?= htmlspecialchars($settings['email_support'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_general_settings" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            <section class="settings-management">
                <h3>Pengaturan Notifikasi</h3>
                <form class="settings-form" method="POST">
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="emailNotif" name="emailNotif" <?= (isset($settings['email_notif']) && $settings['email_notif'] == 1) ? 'checked' : '' ?>>
                        <label for="emailNotif">Kirim notifikasi email untuk pesanan baru</label>
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="smsNotif" name="smsNotif" <?= (isset($settings['sms_notif']) && $settings['sms_notif'] == 1) ? 'checked' : '' ?>>
                        <label for="smsNotif">Kirim notifikasi SMS untuk pengingat pengembalian</label>
                    </div>
                    <div class="form-group">
                        <label for="notifEmailRecipient">Penerima Email Notifikasi:</label>
                        <input type="email" id="notifEmailRecipient" name="notifEmailRecipient" placeholder="contoh@email.com" value="<?= htmlspecialchars($settings['notif_email_recipient'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_notification_settings" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            <section class="settings-management">
                <h3>Manajemen Akun Admin</h3>
                <form class="settings-form" method="POST" enctype="multipart/form-data">
                    <div class="form-group" style="text-align:center;">
                        <img src="../<?= htmlspecialchars($admin_data['foto_profil'] ?? 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" alt="Foto Profil Admin" style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin-bottom:10px;">
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($admin_data['username'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap:</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($admin_data['nama_lengkap'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin_data['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. Telepon:</label>
                        <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($admin_data['no_hp'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <textarea id="alamat" name="alamat" rows="2" style="width:100%;padding:8px;"><?= htmlspecialchars($admin_data['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="foto_profil">Foto Profil:</label>
                        <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_admin_profile" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </section>
            <!-- Form ubah password tetap di bawahnya -->
            <section class="settings-management">
                <h3>Manajemen Akun Admin</h3>
                <form class="settings-form" method="POST">
                    <div class="form-group">
                        <label for="currentPassword">Kata Sandi Saat Ini:</label>
                        <input type="password" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Kata Sandi Baru:</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewPassword">Konfirmasi Kata Sandi Baru:</label>
                        <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="change_admin_password" class="btn btn-primary"><i class="fas fa-key"></i> Ubah Kata Sandi</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>