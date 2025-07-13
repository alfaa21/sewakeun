<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['level'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password dengan MD5
    $selected_role = $_POST['level'];

    // Query untuk memeriksa kredensial pengguna
    $query = "SELECT id, username, role FROM users WHERE username = ? AND password = ? AND role = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $selected_role);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_role);
            mysqli_stmt_fetch($stmt);
            
            // Login berhasil, atur sesi
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['level'] = $db_role; // Menggunakan 'level' di sesi sesuai permintaan sebelumnya
            
            // Merge session cart ke database jika ada
            if (isset($_SESSION['chart_produk']) && is_array($_SESSION['chart_produk'])) {
                foreach ($_SESSION['chart_produk'] as $item) {
                    $pid = (int)$item['product_id'];
                    $qty = (int)$item['qty'];
                    $duration = isset($item['duration']) ? (int)$item['duration'] : 1;
                    // Cek apakah produk sudah ada di cart_items
                    $cek = mysqli_query($conn, "SELECT id, qty FROM cart_items WHERE user_id=$user_id AND product_id=$pid");
                    if ($row = mysqli_fetch_assoc($cek)) {
                        // Update qty
                        $new_qty = $row['qty'] + $qty;
                        mysqli_query($conn, "UPDATE cart_items SET qty=$new_qty, duration=$duration WHERE id=" . $row['id']);
                    } else {
                        // Insert baru
                        mysqli_query($conn, "INSERT INTO cart_items (user_id, product_id, qty, duration) VALUES ($user_id, $pid, $qty, $duration)");
                    }
                }
                unset($_SESSION['chart_produk']);
            }
            
            // Redirect berdasarkan peran
            if ($db_role == 'admin') {
                header('Location: Admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            // Login gagal
            echo "<script>alert('Login gagal! Username, password, atau peran salah.'); window.location='login.php';</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Kesalahan database: " . mysqli_error($conn) . ".'); window.location='login.php';</script>";
    }
} else {
    echo "<script>alert('Mohon lengkapi semua kolom login.'); window.location='login.php';</script>";
}

// Pastikan tidak ada output lain setelah ini jika terjadi redirect
?> 