<?php
include_once __DIR__ . '/session_bootstrap.php';
// include '../koneksi.php'; // koneksi.php akan di-include di setiap halaman PHP yang memanggil header ini
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewaken</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom User Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/images/th.jpeg" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
                <span class="fw-bold text-white">Sewaken</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Semua Kategori
                        </a>
                        <?php
                        // Ambil kategori dari database
                        $kategori_list_header = [];
                        $conn_kat = mysqli_connect('localhost', 'root', '', 'db_sewaken');
                        if ($conn_kat) {
                            $kat_q = mysqli_query($conn_kat, "SELECT nama FROM kategori ORDER BY nama ASC");
                            while ($row = mysqli_fetch_assoc($kat_q)) {
                                $kategori_list_header[] = $row['nama'];
                            }
                            mysqli_close($conn_kat);
                        }
                        ?>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <?php foreach($kategori_list_header as $kat): ?>
                                <li><a class="dropdown-item" href="#"><?= htmlspecialchars($kat) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat.php">Rekomendasi AI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customer_support.php">Dukungan Pelanggan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="message.php">Pesan</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="chart_produk.php" class="btn btn-outline-primary me-2 d-flex align-items-center">
                        <i class="fas fa-shopping-cart me-1"></i> Keranjang
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                        // Ambil foto profil user dari database
                        $foto_profil_user = '';
                        $user_id_header = $_SESSION['user_id'];
                        $conn_header = mysqli_connect('localhost', 'root', '', 'db_sewaken');
                        if ($conn_header) {
                            $q_header = mysqli_query($conn_header, "SELECT foto_profil FROM users WHERE id='$user_id_header'");
                            if ($row_header = mysqli_fetch_assoc($q_header)) {
                                $foto_profil_user = $row_header['foto_profil'];
                            }
                            mysqli_close($conn_header);
                        }
                        ?>
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle d-flex align-items-center" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($foto_profil_user): ?>
                                    <img src="<?= htmlspecialchars($foto_profil_user) ?>" alt="Avatar" style="width:24px;height:24px;border-radius:50%;object-fit:cover;margin-right:6px;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle me-1"></i>
                                <?php endif; ?>
                                <span class="text-white"><?= htmlspecialchars($_SESSION['username']) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeInDown" aria-labelledby="dropdownMenuLink">
                                <li><a class="dropdown-item" href="user_dashboard.php">Dashboard Saya</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-success me-2">Login</a>
                        <a href="register.php" class="btn btn-info">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mt-4"> 
    </main>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>