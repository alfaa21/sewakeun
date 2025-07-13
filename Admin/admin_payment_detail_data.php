<?php
include '../koneksi.php';

header('Content-Type: application/json');

$payment_id = intval($_GET['id'] ?? 0);

if ($payment_id > 0) {
    // Fetch payment details along with order and customer information
    $payment_query = mysqli_query($conn, "SELECT p.*, o.id AS order_id, o.proof_of_payment, u.username AS customer_username, u.email AS customer_email, u.phone_number AS customer_phone FROM payments p JOIN orders o ON p.order_id = o.id JOIN users u ON o.user_id = u.id WHERE p.id=$payment_id");
    $payment = mysqli_fetch_assoc($payment_query);

    if (!$payment) {
        echo json_encode(['error' => 'Pembayaran tidak ditemukan.']);
        exit();
    }

    echo json_encode([
        'payment' => $payment
    ]);

} else {
    echo json_encode(['error' => 'ID pembayaran tidak valid.']);
}
?> 