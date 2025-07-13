<?php
include '../koneksi.php';

header('Content-Type: application/json');

$order_id = intval($_GET['id'] ?? 0);

if ($order_id > 0) {
    // Fetch order details along with customer information
    $order_query = mysqli_query($conn, "SELECT o.*, u.username AS customer_username, u.email AS customer_email, u.phone_number AS customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id=$order_id");
    $order = mysqli_fetch_assoc($order_query);

    if (!$order) {
        echo json_encode(['error' => 'Pesanan tidak ditemukan.']);
        exit();
    }

    // Fetch order items for this order
    $items_query = mysqli_query($conn, "SELECT oi.*, p.nama AS product_name FROM order_items oi JOIN produk p ON oi.product_id = p.id WHERE oi.order_id=$order_id");
    $items = [];
    while ($item = mysqli_fetch_assoc($items_query)) {
        $items[] = $item;
    }

    echo json_encode([
        'order' => $order,
        'items' => $items
    ]);

} else {
    echo json_encode(['error' => 'ID pesanan tidak valid.']);
}
?> 