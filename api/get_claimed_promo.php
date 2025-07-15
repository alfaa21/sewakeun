<?php
include_once '../includes/session_bootstrap.php';
include '../koneksi.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id || !isset($_SESSION['user_id'])) {
    echo json_encode(null); exit();
}
$user_id = $_SESSION['user_id'];
$q = mysqli_query($conn, "SELECT p.* FROM claimed_promos cp JOIN promos p ON cp.promo_id = p.id WHERE cp.id = $id AND cp.user_id = $user_id");
if ($promo = mysqli_fetch_assoc($q)) {
    echo json_encode($promo); exit();
}
echo json_encode(null); 