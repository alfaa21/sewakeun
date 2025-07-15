<?php
include_once '../includes/session_bootstrap.php';
include '../koneksi.php';

// Load PromoModel
require_once '../models/PromoModel.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$kode_promo = $_POST['kode_promo'] ?? '';
$total_amount = (float)($_POST['total_amount'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

if (empty($kode_promo)) {
    echo json_encode(['valid' => false, 'message' => 'Kode promo tidak boleh kosong']);
    exit();
}

if ($total_amount <= 0) {
    echo json_encode(['valid' => false, 'message' => 'Total transaksi tidak valid']);
    exit();
}

$promoModel = new PromoModel($conn);
$result = $promoModel->validatePromoCode($kode_promo, $user_id, $total_amount);

if ($result['valid']) {
    $diskon = $promoModel->calculateDiscount($result['promo'], $total_amount);
    $result['diskon'] = $diskon;
    $result['total_setelah_diskon'] = $total_amount - $diskon;
}

echo json_encode($result);
?> 