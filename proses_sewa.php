<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    if (isset($_POST['multi_sewa']) && $_POST['multi_sewa'] == '1') {
        // MULTI SEWA
        $produk_ids = $_POST['produk_id'];
        $quantities = $_POST['quantity'];
        $lama_sewas = $_POST['lama_sewa'];
        $tanggal_mulais = $_POST['tanggal_mulai'];
        $catatans = $_POST['catatan'];
        $alamat_pengiriman = mysqli_real_escape_string($conn, $_POST['alamat_pengiriman']);
        $metode_pengiriman = mysqli_real_escape_string($conn, $_POST['metode_pengiriman'] ?? 'COD'); // Ambil metode pengiriman dari form
        $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? ''); // Ambil dari POST
        $tanggal_pesan = date('Y-m-d H:i:s');
        $status_transaksi = 'menunggu_verifikasi_admin';
        $total_biaya_semua = 0;
        $produk_data_list = [];
        for ($i = 0; $i < count($produk_ids); $i++) {
            $pid = intval($produk_ids[$i]);
            $qty = intval($quantities[$i]);
            $lama = intval($lama_sewas[$i]);
            $tgl_mulai = mysqli_real_escape_string($conn, $tanggal_mulais[$i]);
            $catatan = mysqli_real_escape_string($conn, $catatans[$i]);

            // Ambil detail produk dari database
            $query_produk = mysqli_query($conn, "SELECT nama, harga, stock, max_duration, duration_unit FROM produk WHERE id=$pid");
            $product_data = mysqli_fetch_assoc($query_produk);

            if (!$product_data) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = "Produk ID " . $pid . " tidak ditemukan.";
                header('Location: chart_produk.php');
                exit();
            }

            $product_price = $product_data['harga'];
            $current_stock = $product_data['stock'];
            $product_name = $product_data['nama'];

            // Validasi stok
            if ($current_stock < $qty) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = "Stok produk " . $product_name . " tidak mencukupi untuk jumlah yang diminta.";
                header('Location: chart_produk.php');
                exit();
            }

            // Validasi lama sewa
            if ($lama > $product_data['max_duration']) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = "Lama sewa produk " . $product_name . " melebihi maksimal sewa produk.";
                header('Location: chart_produk.php');
                exit();
            }

            // Hitung biaya kurir jika pakai Jemput Kurir
            $biaya_kurir = 0;
            $nama_kurir = '';
            if ($metode_pengiriman === 'Jemput Kurir' && isset($_POST['kurir_id_' . $pid]) && intval($_POST['kurir_id_' . $pid]) > 0) {
                $kurir_id = intval($_POST['kurir_id_' . $pid]);
                $q_kurir = mysqli_query($conn, "SELECT * FROM kurir WHERE id=$kurir_id");
                if ($row_kurir = mysqli_fetch_assoc($q_kurir)) {
                    $biaya_kurir = intval($row_kurir['biaya']);
                    $nama_kurir = $row_kurir['nama'];
                }
            }

            $subtotal = $product_price * $qty * $lama + $biaya_kurir;
            $total_biaya_semua += $subtotal;

            // Cek saldo user
            $saldo_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id='$user_id'"));
            $saldo_user = $saldo_user ? floatval($saldo_user['saldo']) : 0;
            if ($saldo_user < $subtotal) {
                mysqli_rollback($conn);
                $_SESSION['error_message'] = "Saldo Anda tidak cukup untuk melakukan penyewaan produk " . $product_name . ".";
                header('Location: chart_produk.php');
                exit();
            }

            // Simpan transaksi
            $q = "INSERT INTO transaksi (id_user, id_produk, tanggal_mulai, lama_sewa, total_biaya, alamat_pengiriman, metode_pengiriman, metode_pembayaran, bukti_pembayaran, status_transaksi, catatan, tanggal_pesan) VALUES ($user_id, $pid, '$tgl_mulai', $lama, $subtotal, '$alamat_pengiriman', '$metode_pengiriman', '$metode_pembayaran', '', '$status_transaksi', '$catatan', '$tanggal_pesan')";
            $ins = mysqli_query($conn, $q);
            if (!$ins) { mysqli_rollback($conn); $_SESSION['error_message'] = "Gagal menyimpan transaksi: ".mysqli_error($conn); header('Location: chart_produk.php'); exit(); }
        }

        // Kurangi saldo user
        mysqli_query($conn, "UPDATE users SET saldo = saldo - $total_biaya_semua WHERE id = '$user_id'");
        // Ambil saldo setelah transaksi
        $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'"))['saldo'];
        // Catat ke riwayat_saldo
        mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$user_id', 'sewa', $total_biaya_semua, 'Penyewaan produk multi-item', $saldo_setelah)");
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Penyewaan multi-item berhasil! Total: Rp " . number_format($total_biaya_semua, 0, ',', '.') . ' (Saldo otomatis terpotong)';
        header('Location: user_dashboard.php');
        exit();
    } else {
        // SINGLE SEWA
        $produk_id = intval($_POST['produk_id']);
        $nama_penyewa = mysqli_real_escape_string($conn, $_POST['nama_penyewa']);
        $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
        $quantity = intval($_POST['quantity']);
        $lama_sewa = intval($_POST['lama_sewa']);
        $tanggal_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai']);
        $alamat_pengiriman = mysqli_real_escape_string($conn, $_POST['alamat_pengiriman']);
        $metode_pengiriman = mysqli_real_escape_string($conn, $_POST['metode_pengiriman'] ?? 'COD'); // Ambil metode pengiriman dari form
        $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? ''); // Ambil dari POST
        $kurir_id = isset($_POST['kurir_id']) ? intval($_POST['kurir_id']) : 0;
        $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

        // Ambil detail produk dari database
        $query_produk = mysqli_query($conn, "SELECT nama, harga, stock, max_duration, duration_unit FROM produk WHERE id=$produk_id");
        $product_data = mysqli_fetch_assoc($query_produk);

        if (!$product_data) {
            $_SESSION['error_message'] = "Produk tidak ditemukan.";
            header('Location: index.php');
            exit();
        }

        $product_price = $product_data['harga'];
        $current_stock = $product_data['stock'];
        $product_name = $product_data['nama'];

        // Validasi stok
        if ($current_stock < $quantity) {
            $_SESSION['error_message'] = "Stok produk tidak mencukupi untuk jumlah yang diminta.";
            header('Location: index.php');
            exit();
        }

        // Validasi lama sewa
        if ($lama_sewa > $product_data['max_duration']) {
            $_SESSION['error_message'] = "Lama sewa melebihi maksimal sewa produk.";
            header('Location: sewa_produk.php?id=' . $produk_id);
            exit();
        }

        // Hitung biaya kurir jika pakai Jemput Kurir
        $biaya_kurir = 0;
        $nama_kurir = '';
        if ($metode_pengiriman === 'Jemput Kurir' && $kurir_id > 0) {
            $q_kurir = mysqli_query($conn, "SELECT * FROM kurir WHERE id=$kurir_id");
            if ($row_kurir = mysqli_fetch_assoc($q_kurir)) {
                $biaya_kurir = intval($row_kurir['biaya']);
                $nama_kurir = $row_kurir['nama'];
            }
        }

        $total_amount = $product_price * $quantity * $lama_sewa + $biaya_kurir;

        // Cek saldo user
        $saldo_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id='$user_id'"));
        $saldo_user = $saldo_user ? floatval($saldo_user['saldo']) : 0;
        if ($saldo_user < $total_amount) {
            $_SESSION['error_message'] = "Saldo Anda tidak cukup untuk melakukan penyewaan ini.";
            header('Location: sewa_produk.php?id=' . $produk_id);
            exit();
        }

        mysqli_begin_transaction($conn);

        // Update stok produk
        mysqli_query($conn, "UPDATE produk SET stock = stock - $quantity WHERE id = $produk_id");

        $tanggal_pesan = date('Y-m-d H:i:s');
        $status_transaksi = 'menunggu_verifikasi_admin';
        $catatan_transaksi = $catatan;
        if ($metode_pengiriman === 'Jemput Kurir' && $nama_kurir) {
            $catatan_transaksi = ($catatan ? $catatan.' | ' : '') . 'Kurir: ' . $nama_kurir;
        }

        $query = "INSERT INTO transaksi (id_user, id_produk, tanggal_mulai, lama_sewa, total_biaya, alamat_pengiriman, metode_pengiriman, metode_pembayaran, bukti_pembayaran, status_transaksi, catatan, tanggal_pesan) VALUES ($user_id, $produk_id, '$tanggal_mulai', $lama_sewa, $total_amount, '$alamat_pengiriman', '$metode_pengiriman', '$metode_pembayaran', '', '$status_transaksi', '$catatan_transaksi', '$tanggal_pesan')";
        $insert = mysqli_query($conn, $query);

        if ($insert) {
            // Kurangi saldo user
            mysqli_query($conn, "UPDATE users SET saldo = saldo - $total_amount WHERE id = '$user_id'");
            // Ambil saldo setelah transaksi
            $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'"))['saldo'];
            // Catat ke riwayat_saldo
            mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$user_id', 'sewa', $total_amount, 'Penyewaan produk ID $produk_id', $saldo_setelah)");
            mysqli_commit($conn);
            $_SESSION['success_message'] = "Penyewaan berhasil! Total: Rp " . number_format($total_amount, 0, ',', '.') . ' (Saldo otomatis terpotong)';
            header('Location: user_dashboard.php');
            exit();
        } else {
            mysqli_rollback($conn);
            $_SESSION['error_message'] = "Penyewaan gagal: " . mysqli_error($conn);
            header('Location: sewa_produk.php?id=' . $produk_id);
            exit();
        }
    }
} else {
    // Jika bukan metode POST, redirect ke halaman utama
    header('Location: index.php');
    exit();
}
?> 