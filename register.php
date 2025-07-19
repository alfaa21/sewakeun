<?php
include_once 'includes/session_bootstrap.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sewaken</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #3498db 60%, #6dd5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: rgba(255,255,255,0.85);
            border-radius: 1.5rem;
            box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.15);
            max-width: 1000px;
            width: 100%;
            display: flex;
            overflow: hidden;
        }
        .register-illustration {
            background: linear-gradient(135deg, #3498db 60%, #6dd5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1 1 0%;
            min-width: 300px;
            padding: 2rem 1rem;
        }
        .register-illustration img {
            width: 90%;
            max-width: 320px;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.10);
            object-fit: contain;
            background: #fff;
        }
        .register-form-section {
            flex: 1 1 0%;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .register-form-section .card-header {
            background: none;
            color: #3498db;
            text-align: center;
            border: none;
            padding-bottom: 0.5rem;
        }
        .register-form-section .card-body {
            padding: 0;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                max-width: 95vw;
            }
            .register-illustration {
                min-height: 180px;
                padding: 1.5rem 0.5rem;
            }
        }
    </style>
</head>
<body>
<div class="register-container shadow-lg">
    <div class="register-illustration d-none d-md-flex">
        <img src="assets/images/th.jpeg" alt="Sewaken Ilustrasi">
    </div>
    <div class="register-form-section">
        <div class="card-header">
            <h3 class="mb-1 fw-bold">Daftar Akun Baru</h3>
            <p class="mb-3 text-muted">Isi data diri Anda untuk membuat akun</p>
        </div>
        <div class="card-body">
            <?php if ((isset($_GET['success']) && $_GET['success'] == '1') && isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php elseif (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            <form action="proses_register.php" method="POST" enctype="multipart/form-data">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    <label for="confirm_password">Konfirmasi Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" required>
                    <label for="nama_lengkap">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                    <label for="email">Email</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Nomor Telepon" required>
                    <label for="no_hp">Nomor Telepon</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat Lengkap" style="height: 100px" required></textarea>
                    <label for="alamat">Alamat Lengkap</label>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Daftar Sebagai</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ktp_image" class="form-label">Upload Foto KTP</label>
                    <input type="file" class="form-control" id="ktp_image" name="ktp_image" accept="image/*" required>
                    <small class="form-text text-muted">Wajib. Format gambar: JPG, JPEG, PNG, GIF. Max 2MB.</small>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Daftar Akun</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 