<?php
include 'koneksi.php';

header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $query = mysqli_query($conn, "SELECT produk.*, kategori.nama AS kategori, lokasi.nama AS lokasi FROM produk JOIN kategori ON produk.kategori_id = kategori.id JOIN lokasi ON produk.lokasi_id = lokasi.id WHERE produk.id=$id");
    if ($product = mysqli_fetch_assoc($query)) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Produk tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'ID produk tidak valid']);
}
?> 