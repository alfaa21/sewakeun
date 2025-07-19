<?php
include_once 'includes/session_bootstrap.php';
ob_start(); // Start output buffering
include 'koneksi.php';
include 'includes/_header.php'; // Include the new header

$cart_items = [];
if (isset($_SESSION['user_id'])) {
    // User login, ambil dari database
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT product_id, qty, duration FROM cart_items WHERE user_id=$user_id");
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = [
            'product_id' => $row['product_id'],
            'qty' => $row['qty'],
            'duration' => $row['duration']
        ];
    }
} else {
    // Belum login, ambil dari session
    $cart_items = isset($_SESSION['chart_produk']) ? $_SESSION['chart_produk'] : [];
}
$produk_di_chart = [];
$total_harga_semua = 0;

if (!empty($cart_items)) {
    $product_ids = array_map(function($item) { return (int)$item['product_id']; }, $cart_items);
    $id_list = implode(',', array_unique($product_ids));
    
    // Ambil detail produk dari database
    // Perbaiki query agar tidak ambigu
    $query_produk = mysqli_query($conn, "SELECT p.id, p.nama, p.harga, p.gambar, p.max_duration, p.duration_unit, l.nama AS nama_lokasi FROM produk p JOIN lokasi l ON p.lokasi_id = l.id WHERE p.id IN ($id_list)") or die(mysqli_error($conn));
    $produk_db = [];
    while($row = mysqli_fetch_assoc($query_produk)) {
        $produk_db[$row['id']] = $row;
    }

    foreach ($cart_items as $item) {
        $product_id = (int)$item['product_id'];
        $qty = (int)$item['qty'];
        $duration = (int)$item['duration'];

        if (isset($produk_db[$product_id])) {
            $p = $produk_db[$product_id];
            $harga_per_unit_per_duration = $p['harga']; // Asumsi harga sudah per unit dan per durasi_unit

            // Hitung subtotal berdasarkan kuantitas dan durasi
            $subtotal_item = $harga_per_unit_per_duration * $qty * $duration;

            $produk_di_chart[] = [
                'id' => $p['id'],
                'nama' => $p['nama'],
                'gambar' => $p['gambar'],
                'harga' => $p['harga'],
                'qty' => $qty,
                'duration' => $duration,
                'duration_unit' => $p['duration_unit'],
                'max_duration' => $p['max_duration'],
                'nama_lokasi' => $p['nama_lokasi'],
                'subtotal' => $subtotal_item
            ];
            $total_harga_semua += $subtotal_item;
        }
    }
}

if (isset($_POST['hapus_produk_id'])) {
    $hapus_produk_id = (int)$_POST['hapus_produk_id'];
    if (isset($_SESSION['user_id'])) {
        // User login, hapus dari database
        $user_id = $_SESSION['user_id'];
        mysqli_query($conn, "DELETE FROM cart_items WHERE user_id=$user_id AND product_id=$hapus_produk_id");
    } else {
        // Belum login, hapus dari session
        $key_to_delete = -1;
        foreach ($_SESSION['chart_produk'] as $key => $item) {
            if ($item['product_id'] === $hapus_produk_id) {
                $key_to_delete = $key;
                break;
            }
        }
        if ($key_to_delete !== -1) {
            unset($_SESSION['chart_produk'][$key_to_delete]);
            $_SESSION['chart_produk'] = array_values($_SESSION['chart_produk']); // Re-index array
        }
    }
    header('Location: chart_produk.php');
    exit();
}

// Handler AJAX add to cart dari produk_detail.php
if (isset($_POST['add_to_cart_ajax'])) {
    $id = (int)$_POST['add_to_cart_ajax'];
    $found = false;
    if (isset($_SESSION['user_id'])) {
        // User login, simpan ke database
        $user_id = $_SESSION['user_id'];
        $cek = mysqli_query($conn, "SELECT id, qty FROM cart_items WHERE user_id=$user_id AND product_id=$id");
        if ($row = mysqli_fetch_assoc($cek)) {
            $new_qty = $row['qty'] + 1;
            mysqli_query($conn, "UPDATE cart_items SET qty=$new_qty WHERE id=" . $row['id']);
        } else {
            mysqli_query($conn, "INSERT INTO cart_items (user_id, product_id, qty) VALUES ($user_id, $id, 1)");
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
        exit();
    } else {
        // Belum login, simpan di session
        if (!isset($_SESSION['chart_produk'])) {
            $_SESSION['chart_produk'] = [];
        }
        foreach ($_SESSION['chart_produk'] as &$item) {
            if ($item['product_id'] === $id) {
                $item['qty'] += 1;
                $found = true;
                break;
            }
        }
        unset($item);
        if (!$found) {
            $_SESSION['chart_produk'][] = [
                'product_id' => $id,
                'qty' => 1,
                'duration' => 1
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
        exit();
    }
}


$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<main class="container mt-4">
    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0">Keranjang Belanja</h2>
        </div>
        <div class="card-body">
            <?php if(empty($produk_di_chart)): ?>
                <div class="alert alert-info text-center" role="alert">
                    Keranjang Anda kosong.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Produk</th>
                                <th scope="col">Harga / Unit</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Maksimal Durasi</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($produk_di_chart as $p): ?>
                                <tr>
                                    <td>
                                        <a href="produk_detail.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($p['gambar']) ?>" class="img-thumbnail me-3" alt="<?= htmlspecialchars($p['nama']) ?>" style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($p['nama']) ?></h6>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td>Rp <?= number_format($p['harga'],0,',','.') ?> / <?= htmlspecialchars($p['duration_unit']) ?></td>
                                    <td><?= htmlspecialchars($p['qty']) ?></td>
                                    <td><?= htmlspecialchars($p['max_duration']) ?> <?= htmlspecialchars($p['duration_unit']) ?></td>
                                    <td><?= htmlspecialchars($p['nama_lokasi']) ?></td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="hapus_produk_id" value="<?= $p['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Hapus total harga dan tombol sewa sekarang -->
                <div class="d-flex justify-content-end mt-3">
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">&larr; Lanjut Belanja</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php // The main and body/html tags are closed by includes/_header.php ?> 