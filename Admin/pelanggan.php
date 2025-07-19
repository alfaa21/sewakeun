<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Handle hapus pelanggan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    // Hapus gambar profil jika ada
    $query_gambar = mysqli_query($conn, "SELECT profile_picture FROM users WHERE id=$id");
    $row_gambar = mysqli_fetch_assoc($query_gambar);
    if ($row_gambar && $row_gambar['profile_picture']) {
        $gambar_path = '../' . $row_gambar['profile_picture'];
        if (file_exists($gambar_path) && is_file($gambar_path)) {
            unlink($gambar_path);
        }
    }
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header('Location: pelanggan.php');
    exit();
}

// Ambil data pelanggan
$pelanggan = mysqli_query($conn, "SELECT * FROM users WHERE role='user' ORDER BY id DESC");

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
    <title>Dashboard Admin - Kelola Pelanggan</title>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
            background-color:rgba(214, 224, 233, 0.45); /* Slightly lighter dark blue-gray */
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
            background-color:rgba(214, 224, 233, 0.45); /* Darker red */
            color: #ffffff;
        }

        .sidebar-footer i {
            margin-right: 10px;
            font-size: 1.1em;
        }
    .customer-management {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 24px 18px 18px 18px;
        margin-bottom: 32px;
    }
    .customer-management h3 {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 18px;
    }
    .customer-management table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        font-size: 1rem;
    }
    .customer-management th, .customer-management td {
        padding: 10px 8px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    .customer-management th {
        background: #f8f9fa;
        font-weight: 700;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .customer-management tr:last-child td {
        border-bottom: none;
    }
    .customer-profile-picture {
        max-width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 50%;
        margin: 0 auto;
        display: block;
        border: 2px solid #eaeaea;
        background: #f8f8f8;
    }
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }
    @media (max-width: 900px) {
        .customer-management table, .customer-management thead, .customer-management tbody, .customer-management th, .customer-management td, .customer-management tr {
            display: block;
        }
        .customer-management th {
            position: static;
        }
        .customer-management tr {
            margin-bottom: 18px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            background: #fff;
        }
        .customer-management td {
            text-align: left;
            padding-left: 40%;
            position: relative;
        }
        .customer-management td:before {
            position: absolute;
            left: 12px;
            top: 10px;
            width: 38%;
            white-space: nowrap;
            font-weight: 600;
            color: #888;
            content: attr(data-label);
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
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="item.php"><i class="fas fa-boxes"></i> Barang</a></li>
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
                <h2>Kelola Pelanggan</h2>
                <div class="user-info">
                    <span>Halo, Jane Doe</span>
                    <img src="../<?= htmlspecialchars($admin_data['foto_profil'] ?? 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section class="customer-management">
                <h3>Daftar Pelanggan</h3>
                <div class="actions">
                    <!-- <a href="#" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah Pelanggan Baru</a> -->
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($pelanggan) > 0): ?>
                            <?php while($p = mysqli_fetch_assoc($pelanggan)): ?>
                                <tr>
                                    <td>
                                        <?php if($p['profile_picture']): ?>
                                            <img src="../<?= htmlspecialchars($p['profile_picture']) ?>" alt="Profile Picture" class="customer-profile-picture">
                                        <?php else: ?>
                                            <img src="../assets/images/account-avatar-profile-user-6-svgrepo-com.svg" alt="Default Avatar" class="customer-profile-picture">
                                        <?php endif; ?>
                                    </td>
                                    <td>#<?= htmlspecialchars($p['id']) ?></td>
                                    <td><?= htmlspecialchars($p['username']) ?></td>
                                    <td><?= htmlspecialchars($p['email']) ?></td>
                                    <td><?= htmlspecialchars($p['no_hp']) ?></td>
                                    <td><?= htmlspecialchars($p['alamat']) ?></td>
                                    <td><?= htmlspecialchars(date('d M Y', strtotime($p['created_at']))) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_pelanggan.php?id=<?= $p['id'] ?>" class="btn btn-success"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="?hapus=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus pelanggan ini?')"><i class="fas fa-trash-alt"></i> Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">Belum ada pelanggan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>