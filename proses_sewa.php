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
        $metode_pengiriman = mysqli_real_escape_string($conn, $_POST['metode_pengiriman'] ?? 'COD');
        $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? '');
        $tanggal_pesan = date('Y-m-d H:i:s');
        $status_transaksi = 'menunggu_verifikasi_admin';
        $total_biaya_semua = 0;
        $produk_data_list = [];
        // Ambil claimed promo id jika ada
        $claimed_promo_id = isset($_POST['claimed_promo_id']) ? intval($_POST['claimed_promo_id']) : 0;
        $promo = null;
        $diskon_promo = 0;
        if ($claimed_promo_id) {
            $q = mysqli_query($conn, "SELECT cp.*, p.* FROM claimed_promos cp JOIN promos p ON cp.promo_id = p.id WHERE cp.id = $claimed_promo_id AND cp.user_id = $user_id AND cp.status = 'belum_digunakan' AND p.status = 'aktif' AND p.tanggal_mulai <= CURDATE() AND p.tanggal_berakhir >= CURDATE()");
            if ($promo = mysqli_fetch_assoc($q)) {
                // validasi min transaksi setelah total_biaya_semua dihitung
            } else {
                $_SESSION['error_message'] = 'Promo tidak valid atau sudah digunakan.';
                header('Location: chart_produk.php');
                exit();
            }
        }
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

        // Hitung diskon promo jika ada
        if ($promo) {
            if ($total_biaya_semua < $promo['min_transaksi']) {
                $_SESSION['error_message'] = 'Minimum transaksi untuk promo ini adalah Rp ' . number_format($promo['min_transaksi'], 0, ',', '.');
                header('Location: chart_produk.php');
                exit();
            }
            require_once 'models/PromoModel.php';
            $promoModel = new PromoModel($conn);
            $diskon_promo = $promoModel->calculateDiscount($promo, $total_biaya_semua);
            $total_biaya_semua -= $diskon_promo;
        }
        // Kurangi saldo user
        mysqli_query($conn, "UPDATE users SET saldo = saldo - $total_biaya_semua WHERE id = '$user_id'");
        // Ambil saldo setelah transaksi
        $saldo_setelah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'"))['saldo'];
        // Catat ke riwayat_saldo
        mysqli_query($conn, "INSERT INTO riwayat_saldo (user_id, tipe, nominal, keterangan, saldo_setelah) VALUES ('$user_id', 'sewa', $total_biaya_semua, 'Penyewaan produk multi-item', $saldo_setelah)");
        // Catat penggunaan promo jika ada
        if ($promo && $claimed_promo_id) {
            // Ambil transaksi terakhir user (bisa lebih dari satu, catat ke transaksi pertama saja)
            $trx = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_user = '$user_id' ORDER BY id_transaksi DESC LIMIT 1");
            if ($row = mysqli_fetch_assoc($trx)) {
                $promoModel->recordPromoUsage($promo['promo_id'], $user_id, $row['id_transaksi'], $diskon_promo);
            }
            // Update status claimed_promos
            mysqli_query($conn, "UPDATE claimed_promos SET status = 'sudah_digunakan' WHERE id = $claimed_promo_id");
        }
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
        $metode_pengiriman = mysqli_real_escape_string($conn, $_POST['metode_pengiriman'] ?? 'COD');
        $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? '');
        $kurir_id = isset($_POST['kurir_id']) ? intval($_POST['kurir_id']) : 0;
        $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
        $claimed_promo_id = isset($_POST['claimed_promo_id']) ? intval($_POST['claimed_promo_id']) : 0;
        $promo = null;
        $diskon_promo = 0;
        if ($claimed_promo_id) {
            $q = mysqli_query($conn, "SELECT cp.*, p.* FROM claimed_promos cp JOIN promos p ON cp.promo_id = p.id WHERE cp.id = $claimed_promo_id AND cp.user_id = $user_id AND cp.status = 'belum_digunakan' AND p.status = 'aktif' AND p.tanggal_mulai <= CURDATE() AND p.tanggal_berakhir >= CURDATE()");
            if ($promo = mysqli_fetch_assoc($q)) {
                // validasi min transaksi setelah total_amount dihitung
            } else {
                $_SESSION['error_message'] = 'Promo tidak valid atau sudah digunakan.';
                header('Location: sewa_produk.php?id=' . $produk_id);
                exit();
            }
        }
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
        // Hitung diskon promo jika ada
        if ($promo) {
            if ($total_amount < $promo['min_transaksi']) {
                $_SESSION['error_message'] = 'Minimum transaksi untuk promo ini adalah Rp ' . number_format($promo['min_transaksi'], 0, ',', '.');
                header('Location: sewa_produk.php?id=' . $produk_id);
                exit();
            }
            require_once 'models/PromoModel.php';
            $promoModel = new PromoModel($conn);
            $diskon_promo = $promoModel->calculateDiscount($promo, $total_amount);
            $total_amount -= $diskon_promo;
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
            // Catat penggunaan promo jika ada
            if ($promo && $claimed_promo_id) {
                $promoModel->recordPromoUsage($promo['promo_id'], $user_id, mysqli_insert_id($conn), $diskon_promo);
                // Update status claimed_promos
                mysqli_query($conn, "UPDATE claimed_promos SET status = 'sudah_digunakan' WHERE id = $claimed_promo_id");
            }
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