<?php
include 'koneksi.php';
session_start();
if (!function_exists('safe')) {
    function safe($arr, $key, $fallback = '-') {
        return isset($arr[$key]) && $arr[$key] !== null ? htmlspecialchars($arr[$key]) : $fallback;
    }
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user_query = mysqli_query($conn, "SELECT username, email, no_hp, alamat, foto_profil, saldo FROM users WHERE id = '$user_id'") or die(mysqli_error($conn));
$user_data = mysqli_fetch_assoc($user_query);

// Fetch user transactions
$transactions_query = mysqli_query($conn, "SELECT t.*, p.nama AS product_name, p.gambar AS product_image
                                        FROM transaksi t
                                        JOIN produk p ON t.id_produk = p.id
                                        WHERE t.id_user = '$user_id'
                                        ORDER BY t.tanggal_pesan DESC") or die(mysqli_error($conn));

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);

// Ambil data kurir untuk pengembalian
$kurir_query = mysqli_query($conn, "SELECT * FROM kurir");
$kurir_list = [];
while($k = mysqli_fetch_assoc($kurir_query)) {
    $kurir_list[] = $k;
}

// Cek dan update status serta denda keterlambatan otomatis
$today = date('Y-m-d');
$cek_telat = mysqli_query($conn, "SELECT t.id_transaksi, t.tanggal_selesai, t.id_produk, t.denda, t.status_transaksi, p.harga FROM transaksi t JOIN produk p ON t.id_produk=p.id WHERE t.id_user='$user_id' AND t.status_transaksi='masa_sewa'");
while($row = mysqli_fetch_assoc($cek_telat)) {
    if ($today > $row['tanggal_selesai']) {
        $days_late = (strtotime($today) - strtotime($row['tanggal_selesai'])) / 86400;
        $days_late = max(1, intval($days_late));
        $denda = 0.3 * floatval($row['harga']) * $days_late;
        mysqli_query($conn, "UPDATE transaksi SET status_transaksi='telat_pengembalian', denda=$denda WHERE id_transaksi=" . intval($row['id_transaksi']));
    }
}

// PROSES TOP UP SALDO (dan proses lain yang pakai header())
if (isset($_POST['topup_saldo'])) {
    $nominal = floatval($_POST['nominal']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    // Pastikan user masih ada di tabel users
    $cek_user = mysqli_query($conn, "SELECT id FROM users WHERE id = '$user_id'");
    if (mysqli_num_rows($cek_user) == 0) {
        $_SESSION['message'] = 'User tidak ditemukan. Silakan login ulang.';
        $_SESSION['message_type'] = 'danger';
        header('Location: user_dashboard.php');
        exit();
    }
    if ($nominal > 0) {
        // Update saldo user
        $update = mysqli_query($conn, "UPDATE users SET saldo = saldo + $nominal WHERE id = '$user_id'");
        // Catat ke riwayat topup
        mysqli_query($conn, "INSERT INTO riwayat_topup (user_id, nominal, metode, status) VALUES ('$user_id', $nominal, '$metode', 'berhasil')");
        // Catat ke riwayat_saldo
        $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'"))['saldo'];
        mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$user_id', 'topup', $nominal, 'Top up via $metode', $saldo_setelah)");
        $_SESSION['message'] = 'Top up saldo berhasil!';
        $_SESSION['message_type'] = 'success';
        header('Location: user_dashboard.php');
        exit();
    } else {
        $_SESSION['message'] = 'Nominal top up harus lebih dari 0!';
        $_SESSION['message_type'] = 'danger';
        header('Location: user_dashboard.php');
        exit();
    }
}

// PROSES UPDATE PROFILE (dan proses lain yang pakai header())
if (isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_no_hp = $_POST['no_telp'];
    $new_alamat = $_POST['alamat'];

    $update_fields = [];
    if ($new_username != $user_data['username']) {
        $update_fields[] = "username = '" . mysqli_real_escape_string($conn, $new_username) . "'";
    }
    if ($new_email != $user_data['email']) {
        $update_fields[] = "email = '" . mysqli_real_escape_string($conn, $new_email) . "'";
    }
    if ($new_no_hp != $user_data['no_hp']) {
        $update_fields[] = "no_hp = '" . mysqli_real_escape_string($conn, $new_no_hp) . "'";
    }
    if ($new_alamat != $user_data['alamat']) {
        $update_fields[] = "alamat = '" . mysqli_real_escape_string($conn, $new_alamat) . "'";
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "assets/images/profile_pictures/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_file_name = uniqid('profile_') . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if($check === false) {
            $_SESSION['message'] = "File bukan gambar.";
            $_SESSION['message_type'] = "danger";
            $uploadOk = 0;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $_SESSION['message'] = "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
            $_SESSION['message_type'] = "danger";
            $uploadOk = 0;
        }
        if ($uploadOk && move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $update_fields[] = "foto_profil = '" . mysqli_real_escape_string($conn, $target_file) . "'";
            if (!empty($user_data['foto_profil']) && $user_data['foto_profil'] != 'assets/images/default-avatar.png' && file_exists($user_data['foto_profil'])) {
                unlink($user_data['foto_profil']);
            }
        } elseif ($uploadOk) {
            $_SESSION['message'] = "Maaf, terjadi kesalahan saat mengunggah gambar Anda.";
            $_SESSION['message_type'] = "danger";
        }
    }

    if (!empty($update_fields)) {
        $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = '$user_id'";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Profil berhasil diperbarui!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal memperbarui profil: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: user_dashboard.php");
    exit();
}

// Tambahkan di atas/bawah proses lain yang pakai header()
if (isset($_POST['confirm_received']) && isset($_POST['transaksi_id'])) {
    $transaksi_id = intval($_POST['transaksi_id']);
    $user_id = $_SESSION['user_id'];
    // Pastikan transaksi milik user dan status barang_dikemas atau dikirim
    $cek = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_transaksi=$transaksi_id AND id_user=$user_id AND (status_transaksi='barang_dikemas' OR status_transaksi='dikirim')");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE transaksi SET status_transaksi='masa_sewa' WHERE id_transaksi=$transaksi_id");
        $_SESSION['success_message'] = 'Barang diterima. Masa sewa dimulai.';
    }
    header('Location: user_dashboard.php');
    exit();
}

if (isset($_POST['batalkan_transaksi']) && isset($_POST['transaksi_id'])) {
    $transaksi_id = intval($_POST['transaksi_id']);
    $user_id = $_SESSION['user_id'];
    // Ambil transaksi milik user yang belum selesai/dibatalkan
    $cek = mysqli_query($conn, "SELECT id_transaksi, total_biaya, id_produk, jumlah, status_transaksi FROM transaksi WHERE id_transaksi=$transaksi_id AND id_user=$user_id AND status_transaksi NOT IN ('selesai','dibatalkan')");
    if ($row = mysqli_fetch_assoc($cek)) {
        $total_biaya = floatval($row['total_biaya']);
        $id_produk = intval($row['id_produk']);
        $jumlah = intval($row['jumlah']);
        // Update status transaksi
        mysqli_query($conn, "UPDATE transaksi SET status_transaksi='dibatalkan' WHERE id_transaksi=$transaksi_id");
        // Kembalikan saldo user
        mysqli_query($conn, "UPDATE users SET saldo = saldo + $total_biaya WHERE id = '$user_id'");
        // Kembalikan stok produk
        if ($id_produk && $jumlah) {
            mysqli_query($conn, "UPDATE produk SET stock = stock + $jumlah WHERE id = $id_produk");
        }
        // Catat ke riwayat_saldo
        $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'"))['saldo'];
        mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$user_id', 'refund', $total_biaya, 'Refund pembatalan transaksi ID $transaksi_id', $saldo_setelah)");
        $_SESSION['success_message'] = 'Transaksi berhasil dibatalkan, saldo dan stok produk telah dikembalikan.';
    } else {
        $_SESSION['error_message'] = 'Transaksi tidak valid untuk dibatalkan.';
    }
    header('Location: user_dashboard.php');
    exit();
}

if (isset($_POST['return_item']) && isset($_POST['transaksi_id'])) {
    $transaksi_id = intval($_POST['transaksi_id']);
    $user_id = $_SESSION['user_id'];
    $metode_pengembalian = $_POST['metode_pengembalian'] ?? 'COD';
    $kurir_id = isset($_POST['kurir_id']) ? intval($_POST['kurir_id']) : 0;
    $biaya_kurir = 0;
    if ($metode_pengembalian == 'Kurir' && $kurir_id > 0) {
        $q_kurir = mysqli_query($conn, "SELECT * FROM kurir WHERE id=$kurir_id");
        if ($row_kurir = mysqli_fetch_assoc($q_kurir)) {
            $biaya_kurir = intval($row_kurir['biaya']);
        }
        // Cek saldo user
        $saldo_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id='$user_id'"));
        $saldo_user = $saldo_user ? floatval($saldo_user['saldo']) : 0;
        if ($saldo_user < $biaya_kurir) {
            $_SESSION['error_message'] = 'Saldo Anda tidak cukup untuk membayar ongkir pengembalian.';
            header('Location: user_dashboard.php');
            exit();
        }
        // Potong saldo user
        mysqli_query($conn, "UPDATE users SET saldo = saldo - $biaya_kurir WHERE id = '$user_id'");
    }
    // Update status transaksi menjadi dikirim_kembali
    mysqli_query($conn, "UPDATE transaksi SET status_transaksi='dikirim_kembali' WHERE id_transaksi=$transaksi_id");
    $_SESSION['success_message'] = 'Pengembalian barang berhasil diproses.';
    header('Location: user_dashboard.php');
    exit();
}

if (isset($_POST['submit_review']) && isset($_POST['review_product_id']) && isset($_POST['transaksi_id'])) {
    $product_id = intval($_POST['review_product_id']);
    $user_id = $_SESSION['user_id'];
    $transaksi_id = intval($_POST['transaksi_id']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    // Cegah review ganda per transaksi
    $cek = mysqli_query($conn, "SELECT id FROM reviews WHERE user_id=$user_id AND product_id=$product_id AND id_transaksi=$transaksi_id");
    if (mysqli_num_rows($cek) == 0 && $rating >= 1 && $rating <= 5) {
        mysqli_query($conn, "INSERT INTO reviews (product_id, user_id, id_transaksi, rating, comment) VALUES ($product_id, $user_id, $transaksi_id, $rating, '$comment')");
        $_SESSION['success_message'] = 'Review berhasil dikirim!';
    } else {
        $_SESSION['error_message'] = 'Anda sudah pernah mereview produk ini untuk transaksi ini atau rating tidak valid.';
    }
    header('Location: user_dashboard.php');
    exit();
}


if (isset($_POST['bayar_denda']) && isset($_POST['transaksi_id_denda'])) {
    $transaksi_id = intval($_POST['transaksi_id_denda']);
    $user_id = $_SESSION['user_id'];
    $q = mysqli_query($conn, "SELECT t.denda, t.id_produk, p.admin_id FROM transaksi t JOIN produk p ON t.id_produk=p.id WHERE t.id_transaksi=$transaksi_id AND t.id_user=$user_id AND t.status_transaksi='telat_pengembalian'");
    $trx = mysqli_fetch_assoc($q);
    if ($trx) {
        $denda = floatval($trx['denda']);
        $admin_id = intval($trx['admin_id']);
        $saldo_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id='$user_id'"));
        $saldo_user = $saldo_user ? floatval($saldo_user['saldo']) : 0;
        if ($saldo_user >= $denda) {
          
            mysqli_query($conn, "UPDATE users SET saldo = saldo - $denda WHERE id = '$user_id'");
           
            mysqli_query($conn, "UPDATE transaksi SET status_transaksi='denda_dibayar' WHERE id_transaksi=$transaksi_id");
           
            $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'"))['saldo'];
            mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$user_id', 'denda', $denda, 'Pembayaran denda keterlambatan transaksi ID $transaksi_id', $saldo_setelah)");
            $_SESSION['success_message'] = 'Denda berhasil dibayar!';
        } else {
            $_SESSION['error_message'] = 'Saldo Anda tidak cukup untuk membayar denda. Silakan top up saldo.';
        }
    }
    header('Location: user_dashboard.php');
    exit();
}


if (isset($_POST['return_item_telat']) && isset($_POST['transaksi_id'])) {
    $transaksi_id = intval($_POST['transaksi_id']);
    $trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT denda, id_user FROM transaksi WHERE id_transaksi=$transaksi_id"));
    $denda = $trx ? floatval($trx['denda']) : 0;
    $user_id = $trx ? intval($trx['id_user']) : 0;
    // Potong saldo user
    mysqli_query($conn, "UPDATE users SET saldo = saldo - $denda WHERE id = '$user_id'");
    // Update status transaksi
    mysqli_query($conn, "UPDATE transaksi SET status_transaksi='dikirim_kembali' WHERE id_transaksi=$transaksi_id");
    $_SESSION['success_message'] = 'Pengembalian barang dan pembayaran denda berhasil diproses.';
    header('Location: user_dashboard.php');
    exit();
}

// Setelah semua proses di atas, baru include header dan tampilkan HTML
include 'includes/_header.php';

// Notifikasi upload bukti pembayaran
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'.htmlspecialchars($_SESSION['success_message']).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.htmlspecialchars($_SESSION['error_message']).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['error_message']);
}
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
                <?php endif; ?>

<?php
// Ambil data untuk summary dan chart
// Total saldo sudah ada di $user_data['saldo']
$total_topup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM riwayat_saldo WHERE user_id='$user_id' AND tipe='topup'"))['total'] ?? 0;
$total_pemakaian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM riwayat_saldo WHERE user_id='$user_id' AND tipe IN ('sewa','denda')"))['total'] ?? 0;
$total_sewa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE id_user='$user_id'"))['total'] ?? 0;

// Data chart saldo (histori)
$saldo_chart_labels = [];
$saldo_chart_data = [];
$saldo_query = mysqli_query($conn, "SELECT tanggal, saldo_setelah FROM riwayat_saldo WHERE user_id='$user_id' ORDER BY tanggal ASC");
while($s = mysqli_fetch_assoc($saldo_query)) {
    $saldo_chart_labels[] = date('d M', strtotime($s['tanggal']));
    $saldo_chart_data[] = (float)$s['saldo_setelah'];
}

// Data chart penyewaan per bulan (6 bulan terakhir)
$sewa_chart_labels = [];
$sewa_chart_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_name = date('M Y', strtotime("-$i months"));
    $count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE id_user='$user_id' AND DATE_FORMAT(tanggal_pesan, '%Y-%m') = '$month'"))['total'] ?? 0;
    $sewa_chart_labels[] = $month_name;
    $sewa_chart_data[] = (int)$count;
}

// Data chart pemasukan & pengeluaran per bulan (6 bulan terakhir)
$pemasukan_chart_labels = [];
$pemasukan_chart_topup = [];
$pemasukan_chart_pengeluaran = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_name = date('M Y', strtotime("-$i months"));
    // Top up
    $topup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM riwayat_saldo WHERE user_id='$user_id' AND tipe='topup' AND DATE_FORMAT(tanggal, '%Y-%m') = '$month'"))['total'] ?? 0;
    // Pengeluaran (sewa+denda)
    $pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM riwayat_saldo WHERE user_id='$user_id' AND tipe IN ('sewa','denda') AND DATE_FORMAT(tanggal, '%Y-%m') = '$month'"))['total'] ?? 0;
    $pemasukan_chart_labels[] = $month_name;
    $pemasukan_chart_topup[] = (float)$topup;
    $pemasukan_chart_pengeluaran[] = (float)$pengeluaran;
}
?>
<!-- Tambahkan CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="container mt-4">
    <div class="row mb-4">
        <!-- Summary Cards -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="fas fa-wallet fa-2x text-primary"></i></div>
                    <h6 class="text-muted">Saldo Saat Ini</h6>
                    <h3 class="fw-bold text-primary">Rp <?= number_format($user_data['saldo'] ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="fas fa-box-open fa-2x text-success"></i></div>
                    <h6 class="text-muted">Total Penyewaan</h6>
                    <h3 class="fw-bold text-success"><?= $total_sewa ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="fas fa-arrow-up fa-2x text-info"></i></div>
                    <h6 class="text-muted">Total Top Up</h6>
                    <h3 class="fw-bold text-info">Rp <?= number_format($total_topup,0,',','.') ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">Histori Saldo</div>
                <div class="card-body">
                    <canvas id="saldoChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">Penyewaan per Bulan</div>
                <div class="card-body">
                    <canvas id="sewaChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-info text-white">Pemasukan & Pengeluaran per Bulan</div>
                <div class="card-body">
                    <canvas id="pemasukanChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2"> <!-- Diubah dari col-md-3 -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="<?= htmlspecialchars($user_data['foto_profil'] ?? 'assets/images/default-avatar.png') ?>" class="rounded-circle mb-3" alt="User Avatar" style="width: 100px; height: 100px; object-fit: cover;">
                    <h5><?= safe($user_data, 'username') ?></h5>
                    <p class="text-muted"><?= safe($user_data, 'email') ?></p>
                    <a href="#" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</a>
                </div>
            </div>
            <div class="list-group shadow-sm">
                <a href="#overview" class="list-group-item list-group-item-action active" data-bs-toggle="tab" data-bs-target="#overview" role="tab">Overview</a>
                <a href="#my-transactions" class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#my-transactions" role="tab">Transaksi Saya</a>
                <a href="#topup-history" class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#topup-history" role="tab">Riwayat Top Up</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10"> <!-- Diubah dari col-md-9 -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">Selamat Datang, <?= safe($user_data, 'username') ?>!</div>
                        <div class="card-body">
                            <p>Ini adalah dashboard Anda. Di sini Anda bisa melihat ringkasan aktivitas penyewaan Anda, mengelola transaksi, dan mengunggah bukti pembayaran.</p>
                            <h5 class="mt-4">Informasi Profil Anda:</h5>
                            <p><strong>Username:</strong> <?= safe($user_data, 'username') ?></p>
                            <p><strong>Email:</strong> <?= safe($user_data, 'email') ?></p>
                            <p><strong>No. Telepon:</strong> <?= safe($user_data, 'no_hp') ?? '-' ?></p>
                            <p><strong>Alamat:</strong> <?= safe($user_data, 'alamat') ?? '-' ?></p>
                        </div>
                    </div>

                    <!-- Card Saldo -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span>Saldo Anda</span>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#topupModal">Top Up Saldo</button>
                        </div>
                        <div class="card-body">
                            <h3 class="text-primary">Rp <?= number_format($user_data['saldo'] ?? 0, 0, ',', '.') ?></h3>
                            <?php
                            // Ringkasan saldo
                            $total_topup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM riwayat_saldo WHERE user_id='$user_id' AND tipe='topup'"))['total'] ?? 0;
                            $total_pemakaian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) AS total FROM riwayat_saldo WHERE user_id='$user_id' AND tipe IN ('sewa','denda')"))['total'] ?? 0;
                            ?>
                            <div class="mt-2">
                                <span class="badge bg-success">Total Top Up: Rp <?= number_format($total_topup,0,',','.') ?></span>
                                <span class="badge bg-danger">Total Pemakaian: Rp <?= number_format($total_pemakaian,0,',','.') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Saldo -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">Riwayat Saldo</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Nominal</th>
                                            <th>Keterangan</th>
                                            <th>Saldo Setelah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $saldo_query = mysqli_query($conn, "SELECT * FROM riwayat_saldo WHERE user_id='$user_id' ORDER BY tanggal DESC");
                                        while($s = mysqli_fetch_assoc($saldo_query)):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('d M Y H:i', strtotime($s['tanggal']))) ?></td>
                                            <td><span class="badge bg-<?= $s['tipe']=='topup'?'success':($s['tipe']=='sewa'?'primary':($s['tipe']=='denda'?'danger':'secondary')) ?>"><?= htmlspecialchars(ucfirst($s['tipe'])) ?></span></td>
                                            <td>Rp <?= number_format($s['nominal'],0,',','.') ?></td>
                                            <td><?= htmlspecialchars($s['keterangan']) ?></td>
                                            <td>Rp <?= number_format($s['saldo_setelah'],0,',','.') ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="my-transactions" role="tabpanel">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">Transaksi Saya</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Transaksi</th>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Lama Sewa</th>
                                            <th>Total Biaya</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                            <th>Pesan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $modal_id = 1; ?>
                                        <?php if (mysqli_num_rows($transactions_query) > 0): ?>
                                            <?php while($t = mysqli_fetch_assoc($transactions_query)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($t['id_transaksi']) ?></td>
                                                <td>
                                                    <img src="<?= htmlspecialchars($t['product_image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                                    <?= htmlspecialchars($t['product_name']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($t['jumlah'] ?? 1) ?></td>
                                                <td><?= htmlspecialchars(date('d M Y', strtotime($t['tanggal_mulai']))) ?></td>
                                                <td><?= htmlspecialchars($t['lama_sewa']) ?> hari</td>
                                                <td>Rp <?= number_format($t['total_biaya'],0,',','.') ?></td>
                                                <td>
                                                    <?php
                                                        $status_class = '';
                                                        switch ($t['status_transaksi']) {
                                                            case 'pending_pembayaran':
                                                                $status_class = 'bg-warning text-dark';
                                                                break;
                                                            case 'menunggu_verifikasi_admin':
                                                                $status_class = 'bg-info';
                                                                break;
                                                            case 'konfirmasi_pembayaran':
                                                                $status_class = 'bg-primary';
                                                                break;
                                                            case 'pembayaran_berhasil':
                                                                $status_class = 'bg-success';
                                                                break;
                                                            case 'dikirim':
                                                                $status_class = 'bg-primary';
                                                                break;
                                                            case 'menunggu_pengembalian':
                                                                $status_class = 'bg-secondary';
                                                                break;
                                                            case 'selesai':
                                                            case 'denda_dibayar':
                                                                $status_class = 'bg-success';
                                                                break;
                                                            case 'dibatalkan':
                                                            case 'telat_pengembalian':
                                                                $status_class = 'bg-danger';
                                                                break;
                                                            case 'masa_sewa':
                                                                $status_class = 'bg-warning';
                                                                break;
                                                            case 'dikirim_kembali':
                                                                $status_class = 'bg-info';
                                                                break;
                                                            default:
                                                                $status_class = 'bg-light text-dark';
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge rounded-pill <?= $status_class ?>">
                                                        <?php
                                                        $label_status = [
                                                            'pending_pembayaran' => 'Pending Pembayaran',
                                                            'menunggu_verifikasi_admin' => 'Menunggu Verifikasi',
                                                            'konfirmasi_pembayaran' => 'Konfirmasi Pembayaran',
                                                            'pembayaran_berhasil' => 'Pembayaran Berhasil',
                                                            'dikirim' => 'Dikirim',
                                                            'menunggu_pengembalian' => 'Menunggu Pengembalian',
                                                            'telat_pengembalian' => 'Telat Pengembalian',
                                                            'denda_dibayar' => 'Denda Dibayar',
                                                            'selesai' => 'Selesai',
                                                            'dibatalkan' => 'Dibatalkan',
                                                            'masa_sewa' => 'Masa Sewa',
                                                            'dikirim_kembali' => 'Dikirim Kembali',
                                                        ];
                                                        echo isset($label_status[$t['status_transaksi']]) ? $label_status[$t['status_transaksi']] : htmlspecialchars(ucwords(str_replace('_', ' ', $t['status_transaksi'])));
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($t['status_transaksi'] == 'menunggu_verifikasi_admin'): ?>
                                                        <form method="post" style="display:inline;">
                                                            <input type="hidden" name="transaksi_id" value="<?= $t['id_transaksi'] ?>">
                                                            <button type="submit" name="batalkan_transaksi" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan transaksi ini?')">Batalkan</button>
                                                        </form>
                                                    <?php elseif ($t['status_transaksi'] == 'konfirmasi_pembayaran'): ?>
                                                        <button class="btn btn-sm btn-primary" disabled>Konfirmasi Pembayaran</button>
                                                    <?php elseif ($t['status_transaksi'] == 'pembayaran_berhasil'): ?>
                                                        <button class="btn btn-sm btn-success" disabled>Pembayaran Berhasil</button>
                                                    <?php elseif ($t['status_transaksi'] == 'barang_dikemas' || $t['status_transaksi'] == 'dikirim'): ?>
                                                        <form method="post" style="display:inline;">
                                                            <input type="hidden" name="transaksi_id" value="<?= $t['id_transaksi'] ?>">
                                                            <button type="submit" name="confirm_received" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi barang sudah diterima?')">Terima Barang</button>
                                                        </form>
                                                    <?php elseif ($t['status_transaksi'] == 'masa_sewa'): ?>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalReturn<?= $t['id_transaksi'] ?>">Kembalikan Barang</button>
                                                        <!-- Modal Pengembalian Barang -->
                                                        <div class="modal fade" id="modalReturn<?= $t['id_transaksi'] ?>" tabindex="-1" aria-labelledby="modalReturnLabel<?= $t['id_transaksi'] ?>" aria-hidden="true">
                                                          <div class="modal-dialog">
                                                            <div class="modal-content">
                                                              <form method="post">
                                                                <div class="modal-header">
                                                                  <h5 class="modal-title" id="modalReturnLabel<?= $t['id_transaksi'] ?>">Pengembalian Barang</h5>
                                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                  <input type="hidden" name="transaksi_id" value="<?= $t['id_transaksi'] ?>">
                                                                  <div class="mb-3">
                                                                    <label class="form-label">Metode Pengembalian</label><br>
                                                                    <div class="form-check form-check-inline">
                                                                      <input class="form-check-input metode-pengembalian" type="radio" name="metode_pengembalian" id="return_cod<?= $t['id_transaksi'] ?>" value="COD" checked>
                                                                      <label class="form-check-label" for="return_cod<?= $t['id_transaksi'] ?>">COD (Serahkan langsung)</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                      <input class="form-check-input metode-pengembalian" type="radio" name="metode_pengembalian" id="return_kurir<?= $t['id_transaksi'] ?>" value="Kurir">
                                                                      <label class="form-check-label" for="return_kurir<?= $t['id_transaksi'] ?>">Kurir</label>
                                                                    </div>
                                                                  </div>
                                                                  <div class="mb-3" id="groupKurir<?= $t['id_transaksi'] ?>" style="display:none;">
                                                                    <label for="kurir_id<?= $t['id_transaksi'] ?>" class="form-label">Pilih Kurir</label>
                                                                    <select class="form-select" name="kurir_id" id="kurir_id<?= $t['id_transaksi'] ?>">
                                                                      <option value="">Pilih Kurir</option>
                                                                      <?php foreach($kurir_list as $kurir): ?>
                                                                        <option value="<?= $kurir['id'] ?>" data-biaya="<?= $kurir['biaya'] ?>"><?= htmlspecialchars($kurir['nama']) ?> (Rp <?= number_format($kurir['biaya'],0,',','.') ?>)</option>
                                                                      <?php endforeach; ?>
                                                                    </select>
                                                                  </div>
                                                                  <div class="mb-3" id="biayaKurirInfo<?= $t['id_transaksi'] ?>" style="display:none;">
                                                                    <label class="form-label">Biaya Ongkir</label>
                                                                    <div class="alert alert-info">Rp <span id="biayaKurir<?= $t['id_transaksi'] ?>">0</span></div>
                                                                  </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                  <button type="submit" name="return_item" class="btn btn-warning">Kembalikan</button>
                                                                </div>
                                                              </form>
                                                            </div>
                                                          </div>
                                                        </div>
                                                        <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                          var metodeRadios = document.querySelectorAll('input[name="metode_pengembalian"]');
                                                          var groupKurir = document.getElementById('groupKurir<?= $t['id_transaksi'] ?>');
                                                          var biayaKurirInfo = document.getElementById('biayaKurirInfo<?= $t['id_transaksi'] ?>');
                                                          var selectKurir = document.getElementById('kurir_id<?= $t['id_transaksi'] ?>');
                                                          var biayaKurirSpan = document.getElementById('biayaKurir<?= $t['id_transaksi'] ?>');
                                                          function updateKurirDisplay() {
                                                            if(document.getElementById('return_kurir<?= $t['id_transaksi'] ?>').checked) {
                                                              groupKurir.style.display = 'block';
                                                              biayaKurirInfo.style.display = 'block';
                                                            } else {
                                                              groupKurir.style.display = 'none';
                                                              biayaKurirInfo.style.display = 'none';
                                                            }
                                                          }
                                                          metodeRadios.forEach(function(radio) {
                                                            radio.addEventListener('change', updateKurirDisplay);
                                                          });
                                                          selectKurir.addEventListener('change', function() {
                                                            var biaya = selectKurir.options[selectKurir.selectedIndex].getAttribute('data-biaya') || 0;
                                                            biayaKurirSpan.textContent = biaya.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                                          });
                                                          updateKurirDisplay();
                                                        });
                                                        </script>
                                                    <?php elseif ($t['status_transaksi'] == 'telat_pengembalian'): ?>
                                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalTelat<?= $t['id_transaksi'] ?>">Kembalikan Barang</button>
                                                        <div class="modal fade" id="modalTelat<?= $t['id_transaksi'] ?>" tabindex="-1" aria-labelledby="modalTelatLabel<?= $t['id_transaksi'] ?>" aria-hidden="true">
                                                          <div class="modal-dialog">
                                                            <div class="modal-content">
                                                              <form method="post">
                                                                <div class="modal-header">
                                                                  <h5 class="modal-title" id="modalTelatLabel<?= $t['id_transaksi'] ?>">Pengembalian Barang (Telat)</h5>
                                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                  <input type="hidden" name="transaksi_id" value="<?= $t['id_transaksi'] ?>">
                                                                  <div class="alert alert-danger">
                                                                    <strong>Denda Keterlambatan:</strong><br>
                                                                    Anda terlambat mengembalikan barang.<br>
                                                                    Denda keterlambatan: <b>Rp <?= number_format($t['denda'],0,',','.') ?></b>
                                                                  </div>
                                                                  <div class="mb-3">
                                                                    <label class="form-label">Metode Pengembalian</label><br>
                                                                    <div class="form-check form-check-inline">
                                                                      <input class="form-check-input" type="radio" name="metode_pengembalian" id="telat_cod<?= $t['id_transaksi'] ?>" value="COD" checked>
                                                                      <label class="form-check-label" for="telat_cod<?= $t['id_transaksi'] ?>">COD (Serahkan langsung)</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                      <input class="form-check-input" type="radio" name="metode_pengembalian" id="telat_kurir<?= $t['id_transaksi'] ?>" value="Kurir">
                                                                      <label class="form-check-label" for="telat_kurir<?= $t['id_transaksi'] ?>">Kurir</label>
                                                                    </div>
                                                                  </div>
                                                                  <div class="mb-3" id="groupKurirTelat<?= $t['id_transaksi'] ?>" style="display:none;">
                                                                    <label for="kurir_id_telat<?= $t['id_transaksi'] ?>" class="form-label">Pilih Kurir</label>
                                                                    <select class="form-select" name="kurir_id" id="kurir_id_telat<?= $t['id_transaksi'] ?>">
                                                                      <option value="">Pilih Kurir</option>
                                                                      <?php foreach($kurir_list as $kurir): ?>
                                                                        <option value="<?= $kurir['id'] ?>" data-biaya="<?= $kurir['biaya'] ?>"><?= htmlspecialchars($kurir['nama']) ?> (Rp <?= number_format($kurir['biaya'],0,',','.') ?>)</option>
                                                                      <?php endforeach; ?>
                                                                    </select>
                                                                  </div>
                                                                  <div class="mb-3" id="biayaKurirInfoTelat<?= $t['id_transaksi'] ?>" style="display:none;">
                                                                    <label class="form-label">Biaya Ongkir</label>
                                                                    <div class="alert alert-info">Rp <span id="biayaKurirTelat<?= $t['id_transaksi'] ?>">0</span></div>
                                                                  </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                  <button type="submit" name="return_item_telat" class="btn btn-warning">Kembalikan & Bayar Denda</button>
                                                                </div>
                                                              </form>
                                                            </div>
                                                          </div>
                                                        </div>
                                                        <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                          var metodeRadios = document.querySelectorAll('input[name=\"metode_pengembalian\"]');
                                                          var groupKurir = document.getElementById('groupKurirTelat<?= $t['id_transaksi'] ?>');
                                                          var biayaKurirInfo = document.getElementById('biayaKurirInfoTelat<?= $t['id_transaksi'] ?>');
                                                          var selectKurir = document.getElementById('kurir_id_telat<?= $t['id_transaksi'] ?>');
                                                          var biayaKurirSpan = document.getElementById('biayaKurirTelat<?= $t['id_transaksi'] ?>');
                                                          function updateKurirDisplay() {
                                                            if(document.getElementById('telat_kurir<?= $t['id_transaksi'] ?>').checked) {
                                                              groupKurir.style.display = 'block';
                                                              biayaKurirInfo.style.display = 'block';
                                                            } else {
                                                              groupKurir.style.display = 'none';
                                                              biayaKurirInfo.style.display = 'none';
                                                            }
                                                          }
                                                          metodeRadios.forEach(function(radio) {
                                                            radio.addEventListener('change', updateKurirDisplay);
                                                          });
                                                          selectKurir.addEventListener('change', function() {
                                                            var biaya = selectKurir.options[selectKurir.selectedIndex].getAttribute('data-biaya') || 0;
                                                            biayaKurirSpan.textContent = biaya.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                                          });
                                                          updateKurirDisplay();
                                                        });
                                                        </script>
                                                    <?php elseif ($t['status_transaksi'] == 'telat_pengembalian'): ?>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalBayarDenda<?= $modal_id ?>">Bayar Denda</button>
                                                        <!-- Modal Bayar Denda -->
                                                        <div class="modal fade" id="modalBayarDenda<?= $modal_id ?>" tabindex="-1" aria-labelledby="modalBayarDendaLabel<?= $modal_id ?>" aria-hidden="true">
                                                          <div class="modal-dialog">
                                                            <div class="modal-content">
                                                              <form method="post">
                                                                <div class="modal-header">
                                                                  <h5 class="modal-title" id="modalBayarDendaLabel<?= $modal_id ?>">Pembayaran Denda Keterlambatan</h5>
                                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                  <input type="hidden" name="transaksi_id_denda" value="<?= $t['id_transaksi'] ?>">
                                                                  <div class="mb-3">
                                                                    <label class="form-label">Nominal Denda</label>
                                                                    <div class="alert alert-danger">Rp <?= number_format($t['denda'],0,',','.') ?></div>
                                                                  </div>
                                                                  <div class="mb-3">
                                                                    <label class="form-label">Saldo Anda</label>
                                                                    <div class="alert alert-info">Rp <?= number_format($user_data['saldo'],0,',','.') ?></div>
                                                                  </div>
                                                                  <div class="mb-2 text-muted small">Pastikan saldo Anda cukup untuk membayar denda. Jika tidak, silakan top up saldo terlebih dahulu.</div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                  <button type="submit" name="bayar_denda" class="btn btn-danger">Bayar Denda</button>
                                                                </div>
                                                              </form>
                                                            </div>
                                                          </div>
                                                        </div>
                                                        <?php $modal_id++; ?>
                                                    <?php elseif ($t['status_transaksi'] == 'masa_sewa'): ?>
                                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalReturn<?= $t['id_transaksi'] ?>">Kembalikan Barang</button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($t['status_transaksi'] == 'selesai'): ?>
                                                        <?php if (!sudah_review($conn, $user_id, $t['id_produk'], $t['id_transaksi'])): ?>
                                                            <button class="btn btn-sm btn-info mt-1" data-bs-toggle="modal" data-bs-target="#modalReview<?= $t['id_transaksi'] ?>">Review Produk</button>
                                                            <!-- Modal Review Produk -->
                                                            <div class="modal fade" id="modalReview<?= $t['id_transaksi'] ?>" tabindex="-1" aria-labelledby="modalReviewLabel<?= $t['id_transaksi'] ?>" aria-hidden="true">
                                                              <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                  <form method="post">
                                                                    <div class="modal-header">
                                                                      <h5 class="modal-title" id="modalReviewLabel<?= $t['id_transaksi'] ?>">Review Produk</h5>
                                                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                      <input type="hidden" name="review_product_id" value="<?= $t['id_produk'] ?>">
                                                                      <input type="hidden" name="transaksi_id" value="<?= $t['id_transaksi'] ?>">
                                                                      <div class="mb-3">
                                                                        <label class="form-label">Rating</label><br>
                                                                        <div class="star-rating">
                                                                            <input type="hidden" name="rating" id="rating_value<?= $t['id_transaksi'] ?>" value="5">
                                                                            <i class="fas fa-star star-5" data-rating="5"></i>
                                                                            <i class="fas fa-star star-4" data-rating="4"></i>
                                                                            <i class="fas fa-star star-3" data-rating="3"></i>
                                                                            <i class="fas fa-star star-2" data-rating="2"></i>
                                                                            <i class="fas fa-star star-1" data-rating="1"></i>
                                                                        </div>
                                                                      </div>
                                                                      <div class="mb-3">
                                                                        <label class="form-label">Komentar</label>
                                                                        <textarea name="comment" class="form-control" rows="3" placeholder="Tulis ulasan Anda..." required></textarea>
                                                                      </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                      <button type="submit" name="submit_review" class="btn btn-primary">Kirim Review</button>
                                                                    </div>
                                                                  </form>
                                                                </div>
                                                              </div>
                                                            </div>
                                                            <style>
                                                                .star-rating {
                                                                    direction: rtl;
                                                                    display: inline-block;
                                                                    padding: 20px;
                                                                }
                                                                .star-rating i {
                                                                    font-size: 25px;
                                                                    color: #ddd;
                                                                    cursor: pointer;
                                                                    transition: color 0.2s;
                                                                    margin-left: 5px;
                                                                }
                                                                .star-rating i:hover,
                                                                .star-rating i:hover ~ i {
                                                                    color: #ffd700;
                                                                }
                                                                .star-rating i.active {
                                                                    color: #ffd700;
                                                                }
                                                            </style>
                                                            <script>
                                                                document.addEventListener('DOMContentLoaded', function() {
                                                                    const starContainer = document.querySelector('.star-rating');
                                                                    const stars = starContainer.querySelectorAll('i');
                                                                    const ratingInput = document.getElementById('rating_value<?= $t['id_transaksi'] ?>');

                                                                    // Set initial rating
                                                                    setRating(5);

                                                                    stars.forEach(star => {
                                                                        star.addEventListener('click', (e) => {
                                                                            const rating = e.target.getAttribute('data-rating');
                                                                            setRating(rating);
                                                                            ratingInput.value = rating;
                                                                        });
                                                                    });

                                                                    function setRating(rating) {
                                                                        stars.forEach(star => {
                                                                            const starRating = star.getAttribute('data-rating');
                                                                            if (starRating <= rating) {
                                                                                star.classList.add('active');
                                                                            } else {
                                                                                star.classList.remove('active');
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            </script>
                                                        </td>
                                                    <?php else: ?>
                                                            <span class="badge bg-success mt-1">Sudah Direview</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <td class="text-center">
                                                    <a href="message.php?transaksi_id=<?= $t['id_transaksi'] ?>&produk_id=<?= $t['id_produk'] ?>" class="btn btn-link p-0" title="Tanya Admin">
                                                        <i class="fas fa-comments fa-lg"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Anda belum memiliki transaksi.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="topup-history" role="tabpanel">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">Riwayat Top Up Saldo</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
                                            <th>Metode</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $topup_query = mysqli_query($conn, "SELECT * FROM riwayat_topup WHERE user_id='$user_id' ORDER BY tanggal DESC");
                                        while($t = mysqli_fetch_assoc($topup_query)):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('d M Y H:i', strtotime($t['tanggal']))) ?></td>
                                            <td>Rp <?= number_format($t['nominal'],0,',','.') ?></td>
                                            <td><?= htmlspecialchars($t['metode']) ?></td>
                                            <td><span class="badge bg-<?= $t['status']=='berhasil'?'success':($t['status']=='pending'?'warning':'danger') ?>"><?= htmlspecialchars($t['status']) ?></span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="user_dashboard.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <img src="<?= htmlspecialchars($user_data['foto_profil'] ?? 'assets/images/default-avatar.png') ?>" class="rounded-circle mb-2" alt="User Avatar" style="width: 80px; height: 80px; object-fit: cover;">
                            <input type="file" name="profile_picture" class="form-control form-control-sm mt-2" accept="image/*">
                            <small class="text-muted">Unggah gambar profil baru (opsional)</small>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= safe($user_data, 'username') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= safe($user_data, 'email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?= safe($user_data, 'no_hp') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= safe($user_data, 'alamat') ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                                    </form>
            </div>
        </div>
    </div>

    <!-- Modal Top Up Saldo -->
    <div class="modal fade" id="topupModal" tabindex="-1" aria-labelledby="topupModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title" id="topupModalLabel">Top Up Saldo</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="nominal" class="form-label">Nominal Top Up</label>
                <input type="number" class="form-control" id="nominal" name="nominal" min="1000" required>
              </div>
              <div class="mb-3">
                <label for="metode" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="metode" name="metode" required>
                  <option value="Transfer Bank">Transfer Bank</option>
                  <option value="ShopeePay">ShopeePay</option>
                  <option value="GoPay">GoPay</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="topup_saldo" class="btn btn-success">Top Up</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<script>
    // JavaScript for tab switching (Using Bootstrap's native behavior)
    // No custom JS needed for showTab as Bootstrap handles it via data-bs-toggle="tab"

    // Initial tab load - Bootstrap tabs generally handle this automatically based on 'active' class.
    // document.addEventListener('DOMContentLoaded', () => {
    //     const triggerTabList = document.querySelectorAll('#myTab button')
    //     triggerTabList.forEach(triggerEl => {
    //         const tabTrigger = new bootstrap.Tab(triggerEl)

    //         triggerEl.addEventListener('click', event => {
    //             event.preventDefault()
    //             tabTrigger.show()
    //         })
    //     })
    // });

    // JavaScript for Edit Profile Modal (Using Bootstrap's native behavior)
    // No custom JS needed for openEditProfileModal or closeEditProfileModal as Bootstrap handles it via data-bs-toggle="modal"

    // Remove custom modal closing logic as Bootstrap handles it
    // window.addEventListener('click', (event) => {
    //     const modal = document.getElementById('editProfileModal');
    //     if (event.target == modal) {
    //         const bootstrapModal = bootstrap.Modal.getInstance(modal);
    //         if (bootstrapModal) {
    //             bootstrapModal.hide();
    //         }
    //     }
    // });
</script>

<?php // The main and body/html tags are closed by includes/_header.php ?>

<script>
// Chart Saldo
const saldoCtx = document.getElementById('saldoChart').getContext('2d');
new Chart(saldoCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($saldo_chart_labels) ?>,
        datasets: [{
            label: 'Saldo',
            data: <?= json_encode($saldo_chart_data) ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
// Chart Penyewaan
const sewaCtx = document.getElementById('sewaChart').getContext('2d');
new Chart(sewaCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($sewa_chart_labels) ?>,
        datasets: [{
            label: 'Jumlah Penyewaan',
            data: <?= json_encode($sewa_chart_data) ?>,
            backgroundColor: '#28a745',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: { beginAtZero: true, stepSize: 1 }
        }
    }
});
// Chart Pemasukan & Pengeluaran
const pemasukanCtx = document.getElementById('pemasukanChart').getContext('2d');
new Chart(pemasukanCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($pemasukan_chart_labels) ?>,
        datasets: [
            {
                label: 'Top Up',
                data: <?= json_encode($pemasukan_chart_topup) ?>,
                backgroundColor: '#17a2b8',
                borderRadius: 8
            },
            {
                label: 'Pengeluaran',
                data: <?= json_encode($pemasukan_chart_pengeluaran) ?>,
                backgroundColor: '#dc3545',
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script> 

<?php
// Cek review produk oleh user per transaksi
function sudah_review($conn, $user_id, $product_id, $transaksi_id) {
    $cek = mysqli_query($conn, "SELECT id FROM reviews WHERE user_id=$user_id AND product_id=$product_id AND id_transaksi=$transaksi_id");
    return mysqli_num_rows($cek) > 0;
} 