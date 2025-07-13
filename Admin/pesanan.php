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

// Ambil data pesanan dengan informasi pelanggan
$query = "SELECT o.*, u.username AS customer_username, u.email AS customer_email, u.phone_number AS customer_phone ";
$query .= "FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $query);

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
                    <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="item.php"><i class="fas fa-boxes"></i> Barang</a></li>
                    <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                    <li><a href="pesanan.php" class="active"><i class="fas fa-receipt"></i> Pesanan</a></li>
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
                <h2>Kelola Pesanan</h2>
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
                            <option value="pending">Menunggu</option>
                            <option value="active">Aktif</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <!-- Removed "Tambah Pesanan Baru" as it's not in scope yet -->
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Pesanan</th>
                            <th>Mulai Sewa</th>
                            <th>Selesai Sewa</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Bukti Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($orders) > 0): ?>
                            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['customer_username']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($order['order_date']))) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($order['rental_start_date']))) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($order['rental_end_date']))) ?></td>
                                <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                <td><span class="status-badge <?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></td>
                                <td>
                                    <?php if ($order['proof_of_payment']): ?>
                                        <a href="../<?= htmlspecialchars($order['proof_of_payment']) ?>" target="_blank" class="btn btn-info" style="padding: 5px 8px; font-size: 0.75em;">Lihat</a>
                                    <?php else: ?>
                                        Belum Ada
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info" onclick="openAdminOrderDetailModal(<?= $order['id'] ?>)"><i class="fas fa-eye"></i> Detail</button>
                                        <?php if ($order['status'] === 'processed'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="new_status" value="shipped">
                                                <button type="submit" name="update_status" class="btn btn-success"><i class="fas fa-truck"></i> Proses Kirim</button>
                                            </form>
                                        <?php elseif ($order['status'] === 'shipped'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="new_status" value="completed">
                                                <button type="submit" name="update_status" class="btn btn-success"><i class="fas fa-check"></i> Selesai</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="new_status" value="cancelled">
                                                <button type="submit" name="update_status" class="btn btn-danger" onclick="return confirm('Batalkan pesanan ini?')"><i class="fas fa-times"></i> Batalkan</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">Belum ada pesanan.</td>
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