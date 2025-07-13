<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';

// Ambil data admin untuk foto profil
$admin_data = null;
if (isset($_SESSION['user_id'])) {
    $admin_id = $_SESSION['user_id'];
    $admin_query = mysqli_query($conn, "SELECT * FROM users WHERE id=$admin_id AND role='admin'");
    if ($admin_query) {
        $admin_data = mysqli_fetch_assoc($admin_query);
    }
}

// Data untuk Info Cards
$total_produk_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk");
$total_produk = $total_produk_query ? mysqli_fetch_assoc($total_produk_query)['total'] ?? 0 : 0;

$pesanan_aktif_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status_transaksi IN ('dikirim', 'menunggu_pengembalian')");
$pesanan_aktif = $pesanan_aktif_query ? mysqli_fetch_assoc($pesanan_aktif_query)['total'] ?? 0 : 0;

// Pelanggan Baru Bulan Ini
$current_month = date('Y-m');
$pelanggan_baru_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'");
$pelanggan_baru = $pelanggan_baru_query ? mysqli_fetch_assoc($pelanggan_baru_query)['total'] ?? 0 : 0;

// Pendapatan Bulan Ini
$pendapatan_bulan_ini_query = mysqli_query($conn, "SELECT SUM(total_biaya) AS total FROM transaksi WHERE status_transaksi='selesai' AND DATE_FORMAT(tanggal_pesan, '%Y-%m') = '$current_month'");
$pendapatan_bulan_ini = $pendapatan_bulan_ini_query ? mysqli_fetch_assoc($pendapatan_bulan_ini_query)['total'] ?? 0 : 0;

// Data untuk Bagan: Pendapatan Bulanan
$monthly_revenue_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    $revenue_query = mysqli_query($conn, "SELECT SUM(total_biaya) AS total FROM transaksi WHERE status_transaksi='selesai' AND DATE_FORMAT(tanggal_pesan, '%Y-%m') = '$month'");
    $revenue = $revenue_query ? mysqli_fetch_assoc($revenue_query)['total'] ?? 0 : 0;
    $monthly_revenue_data['labels'][] = $month_name;
    $monthly_revenue_data['data'][] = round($revenue / 1000000, 2); // Dalam juta Rp
}

// Data untuk Bagan: Pesanan Berdasarkan Status
$order_status_data = [
    'Menunggu Pembayaran' => 0,
    'Menunggu Verifikasi Admin' => 0,
    'Dikirim' => 0,
    'Menunggu Pengembalian' => 0,
    'Selesai' => 0,
    'Dibatalkan' => 0,
    'Telat Pengembalian' => 0,
    'Denda Dibayar' => 0
];
$status_query = mysqli_query($conn, "SELECT status_transaksi, COUNT(*) AS count FROM transaksi GROUP BY status_transaksi");
if ($status_query) {
    while ($row = mysqli_fetch_assoc($status_query)) {
        $status_key = ucwords(str_replace('_', ' ', $row['status_transaksi']));
        if (isset($order_status_data[$status_key])) {
            $order_status_data[$status_key] = $row['count'];
        }
    }
}
$order_status_labels = array_keys($order_status_data);
$order_status_values = array_values($order_status_data);

// Data untuk Bagan: Barang Paling Populer
$popular_items_data = [
    'labels' => [],
    'data' => []
];
$popular_query = mysqli_query($conn, "SELECT p.nama AS product_name, COUNT(t.id_produk) AS total_rented 
                                        FROM transaksi t 
                                        JOIN produk p ON t.id_produk = p.id 
                                        GROUP BY p.id 
                                        ORDER BY total_rented DESC LIMIT 5");
if ($popular_query) {
    while ($row = mysqli_fetch_assoc($popular_query)) {
        $popular_items_data['labels'][] = $row['product_name'];
        $popular_items_data['data'][] = $row['total_rented'];
    }
}

// Data untuk Bagan: Pendaftaran Pelanggan Baru
$new_customers_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    $new_customers_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user' AND DATE_FORMAT(created_at, '%Y-%m') = '$month'");
    $new_customers = $new_customers_query ? mysqli_fetch_assoc($new_customers_query)['total'] ?? 0 : 0;
    $new_customers_data['labels'][] = $month_name;
    $new_customers_data['data'][] = $new_customers;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sewa Barang</title>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
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
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="item.php"><i class="fas fa-boxes"></i> Barang</a></li>
                    <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                    <li><a href="pesanan.php"><i class="fas fa-receipt"></i> Pesanan</a></li>
                    <li><a href="pembayaran.php"><i class="fas fa-dollar-sign"></i> Pembayaran</a></li>
                    <li><a href="chat_admin.php"><i class="fas fa-comments"></i> Chat</a></li>
                    <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h2>Selamat Datang, Admin!</h2>
                <div class="user-info">
                    <span>Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <img src="../<?= htmlspecialchars($admin_data['foto_profil'] ?? 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" alt="User Avatar" class="user-avatar">
                </div>
            </header>

            <section class="info-cards">
                <div class="card">
                    <div class="card-icon blue"><i class="fas fa-boxes"></i></div>
                    <div class="card-details">
                        <span class="card-label">Total Barang</span>
                        <span class="card-value"><?= $total_produk ?></span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon green"><i class="fas fa-handshake"></i></div>
                    <div class="card-details">
                        <span class="card-label">Pesanan Aktif</span>
                        <span class="card-value"><?= $pesanan_aktif ?></span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon orange"><i class="fas fa-users"></i></div>
                    <div class="card-details">
                        <span class="card-label">Pelanggan Baru</span>
                        <span class="card-value"><?= $pelanggan_baru ?></span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon red"><i class="fas fa-dollar-sign"></i></div>
                    <div class="card-details">
                        <span class="card-label">Pendapatan Bulan Ini</span>
                        <span class="card-value">Rp <?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></span>
                    </div>
                </div>
            </section>

            <section class="charts-section">
                <h3>Analisis Data</h3>
                <div class="charts-grid">
                    <div class="chart-container">
                        <h4>Pendapatan Bulanan</h4>
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h4>Pesanan Berdasarkan Status</h4>
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h4>Barang Paling Populer</h4>
                        <canvas id="popularItemsChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h4>Pendaftaran Pelanggan Baru</h4>
                        <canvas id="newCustomersChart"></canvas>
                    </div>
                </div>
            </section>
            
            <section class="recent-orders"> 
                <h3>Pesanan Terbaru</h3> 
                <table> 
                    <thead> 
                        <tr> 
                            <th>ID Pesanan</th> 
                            <th>Barang</th> 
                            <th>Pelanggan</th> 
                            <th>Tanggal Sewa</th> 
                            <th>Status</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <tr> 
                            <td>#P001</td> 
                            <td>Kamera Sony A7III</td> 
                            <td>Rina Susanti</td> 
                            <td>22 Jun 2025</td> 
                            <td><span class="status-badge pending">Menunggu</span></td> 
                        </tr> 
                        <tr> 
                            <td>#P002</td> 
                            <td>Drone DJI Mini 3</td> 
                            <td>Budi Cahyono</td> 
                            <td>21 Jun 2025</td> 
                            <td><span class="status-badge active">Aktif</span></td> 
                        </tr> 
                        <tr> 
                            <td>#P003</td> 
                            <td>Proyektor Epson EB-X400</td> 
                            <td>Citra Kirana</td> 
                            <td>20 Jun 2025</td> 
                            <td><span class="status-badge completed">Selesai</span></td> 
                        </tr> 
                        <tr> 
                            <td>#P004</td> 
                            <td>Speaker Portable JBL</td> 
                            <td>Denny Firmansyah</td> 
                            <td>19 Jun 2025</td> 
                            <td><span class="status-badge active">Aktif</span></td> 
                        </tr> 
                        <tr> 
                            <td>#P005</td> 
                            <td>Lensa Canon EF 50mm</td> 
                            <td>Eka Nurul</td> 
                            <td>18 Jun 2025</td> 
                            <td><span class="status-badge completed">Selesai</span></td> 
                        </tr> 
                    </tbody> 
                </table> 
            </section> 
        </main> 
    </div> 

    <script>
        // Data for the charts (replace with dynamic data from your backend)

        // Monthly Revenue Chart
        const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthly_revenue_data['labels']) ?>,
                datasets: [{
                    label: 'Pendapatan (Juta Rp)',
                    data: <?= json_encode($monthly_revenue_data['data']) ?>,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting agar Chart.js tidak memaksakan rasio aspek
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Pendapatan (Juta Rp)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw + 'jt';
                            }
                        }
                    }
                }
            }
        });

        // Order Status Chart (Doughnut Chart)
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($order_status_labels) ?>,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: <?= json_encode($order_status_values) ?>,
                    backgroundColor: [
                        '#3498db', // Blue for Aktif
                        '#f39c12', // Orange for Menunggu Pembayaran
                        '#2ecc71', // Green for Selesai
                        '#e74c3c',  // Red for Dibatalkan
                        '#9b59b6', // Purple for Menunggu Verifikasi Admin
                        '#1abc9c', // Teal for Dikirim
                        '#f1c40f', // Yellow for Menunggu Pengembalian
                        '#c0392b'  // Dark Red for Telat Pengembalian
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Pesanan Berdasarkan Status'
                    }
                }
            }
        });

        // Popular Items Chart (Bar Chart)
        const popularItemsCtx = document.getElementById('popularItemsChart').getContext('2d');
        const popularItemsChart = new Chart(popularItemsCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($popular_items_data['labels']) ?>,
                datasets: [{
                    label: 'Jumlah Disewa',
                    data: <?= json_encode($popular_items_data['data']) ?>,
                    backgroundColor: '#9b59b6',
                    borderColor: '#8e44ad',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Membuat bar horizontal
                responsive: true,
                maintainAspectRatio: false, // Penting
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Disewa'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Barang'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Barang Paling Populer'
                    }
                }
            }
        });

        // New Customers Chart (Bar Chart)
        const newCustomersCtx = document.getElementById('newCustomersChart').getContext('2d');
        const newCustomersChart = new Chart(newCustomersCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($new_customers_data['labels']) ?>,
                datasets: [{
                    label: 'Jumlah Pelanggan Baru',
                    data: <?= json_encode($new_customers_data['data']) ?>,
                    backgroundColor: '#2ecc71',
                    borderColor: '#27ae60',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pelanggan'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Pendaftaran Pelanggan Baru'
                    }
                }
            }
        });
    </script>
</body>
</html>