<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['nama_lengkap']) && isset($_POST['email']) && isset($_POST['no_hp']) && isset($_POST['alamat'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $role = 'user'; // Default role untuk pendaftaran adalah user
    $foto_profil_path = 'assets/images/default_profile.png'; // Default path

    // Validasi password cocok
    if ($password !== $confirm_password) {
        $_SESSION['message'] = 'Konfirmasi password tidak cocok.';
        $_SESSION['message_type'] = 'danger';
        header('Location: register.php');
        exit();
    }

    // Handle foto profil upload
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_size = $_FILES['foto_profil']['size'];
        $file_type = $_FILES['foto_profil']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $max_file_size = 2 * 1024 * 1024; // 2MB

        if (in_array($file_ext, $allowed_extensions) === false) {
            $_SESSION['message'] = 'Ekstensi file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF.';
            $_SESSION['message_type'] = 'danger';
            header('Location: register.php');
            exit();
        }

        if ($file_size > $max_file_size) {
            $_SESSION['message'] = 'Ukuran file terlalu besar, maksimal 2MB.';
            $_SESSION['message_type'] = 'danger';
            header('Location: register.php');
            exit();
        }

        $upload_dir = 'assets/images/profile_pictures/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Buat direktori jika tidak ada
        }

        $new_file_name = uniqid('profile_') . '.' . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp_name, $upload_path)) {
            $foto_profil_path = $upload_path;
        } else {
            $_SESSION['message'] = 'Gagal mengunggah foto profil.';
            $_SESSION['message_type'] = 'danger';
            header('Location: register.php');
            exit();
        }
    }

    // Hash password dengan MD5 (sesuai skema database Anda)
    $hashed_password = md5($password);

    // Periksa apakah username sudah ada
    $check_query = "SELECT id FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['message'] = 'Username sudah terdaftar. Mohon gunakan username lain.';
        $_SESSION['message_type'] = 'danger';
        mysqli_stmt_close($stmt_check);
        header('Location: register.php');
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // Periksa apakah email sudah ada (jika email harus unik)
    $check_email_query = "SELECT id FROM users WHERE email = ?";
    $stmt_check_email = mysqli_prepare($conn, $check_email_query);
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);

    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        $_SESSION['message'] = 'Email sudah terdaftar. Mohon gunakan email lain.';
        $_SESSION['message_type'] = 'danger';
        mysqli_stmt_close($stmt_check_email);
        header('Location: register.php');
        exit();
    }
    mysqli_stmt_close($stmt_check_email);

    // Masukkan data pengguna baru ke database
    $insert_query = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, alamat, foto_profil, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $insert_query);
    
    if ($stmt_insert) {
        mysqli_stmt_bind_param($stmt_insert, "ssssssss", $username, $hashed_password, $nama_lengkap, $email, $no_hp, $alamat, $foto_profil_path, $role);
        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['message'] = 'Registrasi berhasil! Silakan login.';
            $_SESSION['message_type'] = 'success';
            header('Location: register.php?success=1');
            exit();
        } else {
            $_SESSION['message'] = 'Registrasi gagal: ' . mysqli_error($conn) . '.';
            $_SESSION['message_type'] = 'danger';
            header('Location: register.php');
            exit();
        }
        mysqli_stmt_close($stmt_insert);
    } else {
        $_SESSION['message'] = 'Kesalahan database: ' . mysqli_error($conn) . '.';
        $_SESSION['message_type'] = 'danger';
        header('Location: register.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'Mohon lengkapi semua kolom pendaftaran.';
    $_SESSION['message_type'] = 'danger';
    header('Location: register.php');
    exit();
}

?> 