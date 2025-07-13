<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sewaken</title>
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
        .login-container {
            background: rgba(255,255,255,0.85);
            border-radius: 1.5rem;
            box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.15);
            max-width: 900px;
            width: 100%;
            display: flex;
            overflow: hidden;
        }
        .login-illustration {
            background: linear-gradient(135deg, #3498db 60%, #6dd5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1 1 0%;
            min-width: 300px;
            padding: 2rem 1rem;
        }
        .login-illustration img {
            width: 90%;
            max-width: 320px;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.10);
            object-fit: contain;
            background: #fff;
        }
        .login-form-section {
            flex: 1 1 0%;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form-section .card-header {
            background: none;
            color: #3498db;
            text-align: center;
            border: none;
            padding-bottom: 0.5rem;
        }
        .login-form-section .card-body {
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
            .login-container {
                flex-direction: column;
                max-width: 95vw;
            }
            .login-illustration {
                min-height: 180px;
                padding: 1.5rem 0.5rem;
            }
        }
    </style>
</head>
<body>
<?php if (isset($_GET['notif']) && $_GET['notif'] === 'login_sewa'): ?>
    <div class="alert alert-warning text-center" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;width:90%;max-width:500px;">
        Silakan login terlebih dahulu untuk menyewa produk.
    </div>
<?php endif; ?>
<div class="login-container shadow-lg">
    <div class="login-illustration d-none d-md-flex">
        <img src="assets/images/th.jpeg" alt="Sewaken Ilustrasi">
    </div>
    <div class="login-form-section">
        <div class="card-header">
            <h3 class="mb-1 fw-bold">Selamat Datang di Sewaken</h3>
            <p class="mb-3 text-muted">Silakan login untuk melanjutkan</p>
        </div>
        <div class="card-body">
            <form action="proses_login.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <div class="mb-3">
                    <label for="level" class="form-label">Login Sebagai</label>
                    <select class="form-select" id="level" name="level" required>
                        <option value="user">Pengguna</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p class="mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                <a href="#" class="small">Lupa Password?</a>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 