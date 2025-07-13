<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id']) && isset($_FILES['proof_of_payment'])) {
    $transaction_id = intval($_POST['transaction_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the transaction belongs to the user and is pending_pembayaran
    $check_transaction_query = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_transaksi=$transaction_id AND id_user=$user_id AND status_transaksi='pending_pembayaran'");
    $transaction = mysqli_fetch_assoc($check_transaction_query);

    if (!$transaction) {
        $_SESSION['error_message'] = "Transaksi tidak ditemukan atau tidak valid untuk diunggah bukti pembayaran.";
        header('Location: user_dashboard.php');
        exit();
    }

    $target_dir = "assets/proofs/";
    $file_name = "bukti_" . uniqid() . "." . strtolower(pathinfo($_FILES["proof_of_payment"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["proof_of_payment"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION['error_message'] = "File bukan gambar.";
        $uploadOk = 0;
    }

    // Check file size (5MB limit)
    if ($_FILES["proof_of_payment"]["size"] > 5000000) {
        $_SESSION['error_message'] = "Ukuran file terlalu besar (maksimal 5MB).";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $_SESSION['error_message'] = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        header('Location: user_dashboard.php');
        exit();
    } else {
        if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
            // Update transaksi table with proof of payment and status
            $update_transaction_query = "UPDATE transaksi SET bukti_pembayaran=?, status_transaksi='menunggu_verifikasi_admin' WHERE id_transaksi=?";
            $stmt_transaction = mysqli_prepare($conn, $update_transaction_query);
            mysqli_stmt_bind_param($stmt_transaction, "si", $target_file, $transaction_id);
            mysqli_stmt_execute($stmt_transaction);

            $_SESSION['success_message'] = "Bukti pembayaran berhasil diunggah. Transaksi Anda sedang menunggu verifikasi admin.";
            header('Location: user_dashboard.php');
            exit();
        } else {
            $_SESSION['error_message'] = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
            header('Location: user_dashboard.php');
            exit();
        }
    }
} else {
    // If accessing directly via GET or invalid POST, redirect to dashboard
    $_SESSION['error_message'] = "Permintaan tidak valid atau transaksi tidak ditemukan.";
    header('Location: user_dashboard.php');
    exit();
}
?> 