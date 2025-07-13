<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Handle update status transaksi
if (isset($_POST['update_payment_status'])) {
    $transaksi_id = intval($_POST['transaksi_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['update_payment_status']);
    // Jika update ke selesai dari dikirim_kembali, proses saldo admin & stok
    if ($new_status === 'selesai') {
        // Ambil data transaksi & produk
        $q = mysqli_query($conn, "SELECT t.*, p.admin_id, p.id AS produk_id FROM transaksi t JOIN produk p ON t.id_produk = p.id WHERE t.id_transaksi = $transaksi_id");
        if ($trx = mysqli_fetch_assoc($q)) {
            $admin_id = $trx['admin_id'];
            $produk_id = $trx['produk_id'];
            $jumlah = 1; // Asumsi 1 produk per transaksi (jika ada kolom quantity, ganti sesuai kolom)
            $total_biaya = floatval($trx['total_biaya']);
            // Tambah saldo admin
            mysqli_query($conn, "UPDATE users SET saldo = saldo + $total_biaya WHERE id = '$admin_id'");
            // Catat ke riwayat_saldo admin
            $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$admin_id'"))['saldo'];
            mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$admin_id', 'sewa', $total_biaya, 'Penerimaan sewa dari user ID {$trx['id_user']}', $saldo_setelah)");
            // Tambah stok produk
            mysqli_query($conn, "UPDATE produk SET stock = stock + $jumlah WHERE id = $produk_id");
        }
    }
    $update_query = "UPDATE transaksi SET status_transaksi='$new_status' WHERE id_transaksi=$transaksi_id";
    mysqli_query($conn, $update_query);
    header('Location: pembayaran.php');
    exit();
}

// Hitung ringkasan transaksi
$total_completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_biaya) AS total FROM transaksi WHERE status_transaksi='selesai'"))['total'] ?? 0;
$count_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM transaksi WHERE status_transaksi='pending_pembayaran'"))['count'] ?? 0;
$count_verification = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM transaksi WHERE status_transaksi='menunggu_verifikasi_admin'"))['count'] ?? 0;
$count_cancelled = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM transaksi WHERE status_transaksi='dibatalkan'"))['count'] ?? 0;

// Ambil data transaksi
$query = "SELECT t.*, u.username AS customer_username, p.nama AS product_name 
          FROM transaksi t 
          JOIN users u ON t.id_user = u.id 
          JOIN produk p ON t.id_produk = p.id 
          ORDER BY t.tanggal_pesan DESC";
$transactions = mysqli_query($conn, $query);

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
    <title>Dashboard Admin - Kelola Pembayaran</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Body and Layout */
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
            gap: 30px;
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

        /* Status colors for payments */
        .status-badge.pending-pembayaran { background-color: #f39c12; } /* Orange */
        .status-badge.menunggu-verifikasi-admin { background-color: #3498db; } /* Blue */
        .status-badge.dikirim { background-color: #2ecc71; } /* Green */
        .status-badge.selesai { background-color: #27ae60; } /* Green */
        .status-badge.dibatalkan { background-color: #e74c3c; } /* Red */
        .status-badge.menunggu-pengembalian { background-color: #9b59b6; } /* Purple */
        .status-badge.telat-pengembalian { background-color: #c0392b; } /* Red */
        .status-badge.denda-dibayar { background-color: #16a085; } /* Teal */
        .status-badge.barang-dikemas {
            background: #ff9800;
            color: #fff;
        }
        .status-badge.masa-sewa {
            background: #673ab7;
            color: #fff;
        }
        .status-badge.dikirim-kembali { background-color: #00bcd4; } /* Teal */

        /* Payment summary cards with colored backgrounds */
        .summary-cards {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        .summary-card {
            flex: 1 1 200px;
            display: flex;
            align-items: center;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(44,62,80,0.10);
            padding: 20px 24px;
            min-width: 220px;
            min-height: 90px;
            transition: box-shadow 0.2s;
            position: relative;
            color: #fff;
        }
        .summary-card .icon {
            font-size: 2.2em;
            margin-right: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.85;
        }
        .summary-card .details {
            flex: 1;
        }
        .summary-card .label {
            font-size: 1em;
            color: #f0f0f0;
            margin-bottom: 4px;
            opacity: 0.95;
        }
        .summary-card .value {
            font-size: 1.3em;
            font-weight: 700;
            color: #fff;
        }
        .summary-card.selesai {
            background: linear-gradient(135deg, #27ae60 80%, #219150 100%);
        }
        .summary-card.selesai .icon { color: #fff; }
        .summary-card.pending {
            background: linear-gradient(135deg, #f39c12 80%, #e67e22 100%);
        }
        .summary-card.pending .icon { color: #fff; }
        .summary-card.verifikasi {
            background: linear-gradient(135deg, #3498db 80%, #2980b9 100%);
        }
        .summary-card.verifikasi .icon { color: #fff; }
        .summary-card.dibatalkan {
            background: linear-gradient(135deg, #e74c3c 80%, #c0392b 100%);
        }
        .summary-card.dibatalkan .icon { color: #fff; }
        @media (max-width: 900px) {
            .summary-cards {
                flex-direction: column;
                gap: 16px;
            }
            .summary-card {
                min-width: 0;
                width: 100%;
            }
        }

        .payment-list table th, .payment-list table td {
            text-align: center;
            vertical-align: middle;
        }
        .status-badge {
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.95em;
            font-weight: 600;
            color: #fff;
            text-align: center;
            display: inline-block;
            min-width: 110px;
        }
        .action-btns {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        .action-btn {
            border: none;
            outline: none;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.95em;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(44,62,80,0.08);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .action-btn.pending { background: #f39c12; }
        .action-btn.verifikasi { background: #3498db; }
        .action-btn.kirim { background: #2ecc71; }
        .action-btn.selesai { background: #27ae60; }
        .action-btn.batal { background: #e74c3c; }
        .action-btn.pengembalian { background: #9b59b6; }
        .action-btn.telat { background: #c0392b; }
        .action-btn.denda { background: #16a085; }
        .action-btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .action-btn i { font-size: 1em; }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar" style="background:#0d6efd!important;color:#fff!important;">
            <div class="sidebar-logo">
                <i class="fas fa-store"></i>
                <h3>Sewaken Admin</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="item.php"><i class="fas fa-box"></i> Barang</a></li>
                    <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                    <li><a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                    <li><a href="pembayaran.php" class="active"><i class="fas fa-money-bill-wave"></i> Pembayaran</a></li>
                    <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h2>Kelola Pembayaran</h2>
                <div class="user-info">
                    <span>Halo, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                    <img src="../<?= htmlspecialchars($admin_data['foto_profil'] ?? 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section class="payment-summary">
                <h3>Ringkasan Pembayaran</h3>
                <div class="summary-cards">
                  <div class="summary-card selesai">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <div class="details">
                      <div class="label">Total Selesai</div>
                      <div class="value">Rp <?php echo number_format($total_completed, 0, ',', '.'); ?></div>
                    </div>
                  </div>
                  <div class="summary-card pending">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="details">
                      <div class="label">Menunggu Pembayaran</div>
                      <div class="value"><?php echo $count_pending; ?> transaksi</div>
                    </div>
                  </div>
                  <div class="summary-card verifikasi">
                    <div class="icon"><i class="fas fa-user-check"></i></div>
                    <div class="details">
                      <div class="label">Menunggu Verifikasi</div>
                      <div class="value"><?php echo $count_verification; ?> transaksi</div>
                    </div>
                  </div>
                  <div class="summary-card dibatalkan">
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                    <div class="details">
                      <div class="label">Dibatalkan</div>
                      <div class="value"><?php echo $count_cancelled; ?> transaksi</div>
                    </div>
                  </div>
                </div>
            </section>

            <section class="payment-list">
                <h3>Daftar Transaksi</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal Pesan</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Total Biaya</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($transactions)): ?>
                        <tr>
                            <td><?= $row['id_transaksi']; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pesan'])); ?></td>
                            <td><?= htmlspecialchars($row['customer_username']); ?></td>
                            <td><?= htmlspecialchars($row['product_name']); ?></td>
                            <td>Rp <?= number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
                            <td>
                                <span class="status-badge <?= str_replace('_', '-', $row['status_transaksi']); ?>">
                                    <?php
                                    $label_status = [
                                        'pending_pembayaran' => 'Pending Pembayaran',
                                        'menunggu_verifikasi_admin' => 'Menunggu Verifikasi',
                                        'barang_dikemas' => 'Barang Dikemas',
                                        'konfirmasi_pembayaran' => 'Konfirmasi Pembayaran',
                                        'pembayaran_berhasil' => 'Pembayaran Berhasil',
                                        'dikirim' => 'Dikirim',
                                        'menunggu_pengembalian' => 'Menunggu Pengembalian',
                                        'telat_pengembalian' => 'Telat Pengembalian',
                                        'denda_dibayar' => 'Denda Dibayar',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                        'dikirim_kembali' => 'Dikirim Kembali',
                                    ];
                                    echo isset($label_status[$row['status_transaksi']]) ? $label_status[$row['status_transaksi']] : htmlspecialchars(ucwords(str_replace('_', ' ', $row['status_transaksi'])));
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['bukti_pembayaran']): ?>
                                    <a href="../<?= htmlspecialchars($row['bukti_pembayaran']); ?>" target="_blank">
                                        <img src="../<?= htmlspecialchars($row['bukti_pembayaran']); ?>" alt="Bukti" style="height:40px;border-radius:4px;">
                                    </a>
                                <?php else: ?>
                                    <span style="color:#aaa;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <?php if ($row['status_transaksi'] == 'pending_pembayaran'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="transaksi_id" value="<?= $row['id_transaksi']; ?>">
                                            <button type="submit" name="update_payment_status" value="menunggu_verifikasi_admin" class="action-btn verifikasi" title="Verifikasi Pembayaran"><i class="fas fa-user-check"></i> Verifikasi</button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="transaksi_id" value="<?= $row['id_transaksi']; ?>">
                                            <button type="submit" name="update_payment_status" value="dibatalkan" class="action-btn batal" title="Batalkan"><i class="fas fa-times"></i> Batalkan</button>
                                        </form>
                                    <?php elseif ($row['status_transaksi'] == 'menunggu_verifikasi_admin'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="transaksi_id" value="<?= $row['id_transaksi']; ?>">
                                            <button type="submit" name="update_payment_status" value="barang_dikemas" class="action-btn verifikasi" title="Konfirmasi Pembayaran"><i class="fas fa-box"></i> Konfirmasi Pembayaran</button>
                                        </form>
                                    <?php elseif ($row['status_transaksi'] == 'barang_dikemas'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="transaksi_id" value="<?= $row['id_transaksi']; ?>">
                                            <button type="submit" name="update_payment_status" value="dikirim" class="action-btn kirim" title="Kirim Barang"><i class="fas fa-truck"></i> Kirim</button>
                                        </form>
                                    <?php elseif ($row['status_transaksi'] == 'masa_sewa'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="transaksi_id" value="<?= $row['id_transaksi']; ?>">
                                            <button type="submit" name="update_payment_status" value="selesai" class="action-btn selesai" title="Terima Barang"><i class="fas fa-check"></i> Terima Barang</button>
                                        </form>
                                    <?php elseif ($row['status_transaksi'] == 'dikirim_kembali'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="transaksi_id" value="<?= $row['id_transaksi']; ?>">
                                            <button type="submit" name="update_payment_status" value="selesai" class="action-btn selesai" title="Terima Barang"><i class="fas fa-check"></i> Terima Barang</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
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