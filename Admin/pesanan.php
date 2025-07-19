<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Handle update status pesanan
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

    $update_query = "UPDATE orders SET status='$new_status' WHERE id=$order_id";
    mysqli_query($conn, $update_query);
    header('Location: pesanan.php');
    exit();
}

// Query data pesanan
$query = mysqli_query($conn, "SELECT t.id_transaksi, u.username AS nama_user, p.nama AS nama_produk, t.tanggal_mulai, t.tanggal_selesai, t.alamat_pengiriman, t.status_transaksi FROM transaksi t JOIN users u ON t.id_user = u.id JOIN produk p ON t.id_produk = p.id ORDER BY t.id_transaksi DESC");

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
    <title>Dashboard Admin - Kelola Pesanan</title>
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
            background-color:rgb(229, 234, 240); /* Dark blue-gray */
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

        .sidebar-nav a:hover{
            background-color:rgba(232, 238, 243, 0.45); /* Slightly lighter dark blue-gray */
        }
        .sidebar-nav a.active {
            background-color: #rgba(214, 224, 233, 0.45);; /* Slightly lighter dark blue-gray */
            color: #ffffff;
        }

        .sidebar-footer {
            margin-top: auto; /* Pushes the logout to the bottom */
            padding-top: 20px;
            border-top: 1px solid rgba(248, 241, 241, 0.69);
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
            display: inline-block;
            min-width: 110px;
            height: 32px;
            line-height: 32px;
            padding: 0 16px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            background: #888;
            text-align: center;
            vertical-align: middle;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .status-selesai { background: #2ecc71; color: #fff; }
        .status-dibatalkan { background: #e74c3c; color: #fff; }
        .status-masa-sewa { background: #f1c40f; color: #222; }
        .status-dikirim { background: #3498db; color: #fff; }
        .status-menunggu-verifikasi-admin { background: #00bcd4; color: #fff; }
        .status-pending-pembayaran { background: #ff9800; color: #fff; }
        .status-telat-pengembalian { background: #e67e22; color: #fff; }
        .status-dikirim-kembali { background: #9b59b6; color: #fff; }

        /* Status colors from previous examples */
        .status-badge.pending { background-color: #f39c12; } /* Orange */
        .status-badge.processed { background-color: #3498db; } /* Blue, for processed */
        .status-badge.shipped { background-color: #17a2b8; } /* Cyan, for shipped */
        .status-badge.completed { background-color: #2ecc71; } /* Green */
        .status-badge.cancelled { background-color: #e74c3c; } /* Red */

        er;
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
/* Button Styling */
        .btn {
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            display: inline-flex;
            align-items: cent
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
                <h2>Pesanan Masuk</h2>
                <div class="user-info">
                    <span>Halo, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                    <img src="../<?= htmlspecialchars($admin_data['foto_profil'] ?? 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section class="orders-management">
                <h3>Daftar Pesanan</h3>
                <div class="filter-section">
                    <div class="search-bar">
                        <input type="text" placeholder="Cari ID Pesanan, Pelanggan..." aria-label="Cari Pesanan">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="filter-dropdown">
                        <select aria-label="Filter Status Pesanan">
                            <option value="">Semua Status</option>
                            <option value="pending_pembayaran">Pending Pembayaran</option>
                            <option value="menunggu_verifikasi_admin">Menunggu Verifikasi Admin</option>
                            <option value="masa_sewa">Masa Sewa</option>
                            <option value="telat_pengembalian">Telat Pengembalian</option>
                            <option value="dikirim_kembali">Dikirim Kembali</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <!-- Removed "Tambah Pesanan Baru" as it's not in scope yet -->
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Nama User</th>
                            <th>Nama Produk</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_transaksi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_user']) ?></td>
                                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal_mulai']))) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal_selesai']))) ?></td>
                                <td><?= htmlspecialchars($row['alamat_pengiriman']) ?></td>
                                <td><span class="status-badge status-<?= str_replace('_', '-', strtolower($row['status_transaksi'])) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['status_transaksi']))) ?></span></td>
                                <td>
                                  <a href="chat_admin.php" title="Pesan ke User" class="text-primary">
                                    <i class="fas fa-comments fa-lg"></i>
                                  </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Belum ada pesanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Modal Detail Pesanan Admin -->
            <div class="modal-bg" id="adminOrderDetailModalBg">
                <div class="modal" id="adminOrderDetailModalContent">
                    <span class="modal-close" onclick="closeAdminOrderDetailModal()">&times;</span>
                    <h3>Detail Pesanan <span id="admin-order-detail-id"></span></h3>
                    <div id="adminOrderDetailContent">
                        <!-- Konten detail pesanan akan dimuat di sini melalui AJAX -->
                    </div>
                </div>
            </div>

            <script>
                function openAdminOrderDetailModal(orderId) {
                    fetch('admin_order_detail_data.php?id=' + orderId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }
                            document.getElementById('admin-order-detail-id').innerText = '#' + data.order.id;
                            let detailHtml = `
                                <p><strong>Pelanggan:</strong> ${data.order.customer_username} (${data.order.customer_email || 'N/A'})</p>
                                <p><strong>Nomor Telepon:</strong> ${data.order.customer_phone || 'N/A'}</p>
                                <p><strong>Tanggal Pesanan:</strong> ${new Date(data.order.order_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                <p><strong>Periode Sewa:</strong> ${new Date(data.order.rental_start_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })} - ${new Date(data.order.rental_end_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                <p><strong>Total Jumlah:</strong> Rp ${parseFloat(data.order.total_amount).toLocaleString('id-ID')}</p>
                                <p><strong>Status:</strong> <span class="status-badge ${data.order.status}">${data.order.status.charAt(0).toUpperCase() + data.order.status.slice(1)}</span></p>
                                <p><strong>Alamat Pengiriman:</strong> ${data.order.shipping_address}</p>
                                <p><strong>Metode Pembayaran:</strong> ${data.order.payment_method}</p>
                                ${data.order.admin_notes ? `<p><strong>Catatan Admin:</strong> ${data.order.admin_notes}</p>` : ''}
                                
                                ${data.order.proof_of_payment ? `
                                    <h4>Bukti Pembayaran:</h4>
                                    <a href="../${data.order.proof_of_payment}" target="_blank"><img src="../${data.order.proof_of_payment}" class="proof-img" alt="Bukti Pembayaran"></a>
                                ` : ''}

                                <h4>Item Pesanan:</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Harga/Unit</th>
                                            <th>Durasi</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.items.map(item => `
                                            <tr>
                                                <td>${item.product_name}</td>
                                                <td>${item.quantity}</td>
                                                <td>Rp ${parseFloat(item.price_per_unit).toLocaleString('id-ID')}/${item.rental_duration_unit}</td>
                                                <td>${item.rental_duration} ${item.rental_duration_unit}</td>
                                                <td>Rp ${parseFloat(item.subtotal).toLocaleString('id-ID')}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            `;
                            document.getElementById('adminOrderDetailContent').innerHTML = detailHtml;
                            document.getElementById('adminOrderDetailModalBg').style.display = 'flex';
                        })
                        .catch(error => console.error('Error fetching order detail:', error));
                }

                function closeAdminOrderDetailModal() {
                    document.getElementById('adminOrderDetailModalBg').style.display = 'none';
                }
            </script>
        </main>
    </div>
</body>
</html>