<?php
class PromoModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Ambil semua promo yang aktif
     */
    public function getActivePromos() {
        $query = "SELECT p.*, k.nama AS nama_kategori, pr.nama AS nama_produk 
                  FROM promos p 
                  LEFT JOIN kategori k ON p.kategori_id = k.id 
                  LEFT JOIN produk pr ON p.produk_id = pr.id 
                  WHERE p.status = 'aktif' 
                  AND p.tanggal_mulai <= CURDATE() 
                  AND p.tanggal_berakhir >= CURDATE()
                  AND (p.limit_penggunaan IS NULL OR p.penggunaan_sekarang < p.limit_penggunaan)
                  ORDER BY p.created_at DESC";
        
        $result = mysqli_query($this->conn, $query);
        $promos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $promos[] = $row;
        }
        return $promos;
    }
    
    /**
     * Validasi kode promo
     */
    public function validatePromoCode($kode_promo, $user_id, $total_amount, $kategori_id = null, $produk_id = null) {
        $kode_promo = mysqli_real_escape_string($this->conn, $kode_promo);
        $user_id = (int)$user_id;
        
        $query = "SELECT * FROM promos 
                  WHERE kode_promo = '$kode_promo' 
                  AND status = 'aktif'
                  AND tanggal_mulai <= CURDATE() 
                  AND tanggal_berakhir >= CURDATE()
                  AND (limit_penggunaan IS NULL OR penggunaan_sekarang < limit_penggunaan)";
        
        $result = mysqli_query($this->conn, $query);
        if (!$result || mysqli_num_rows($result) == 0) {
            return ['valid' => false, 'message' => 'Kode promo tidak valid atau sudah berakhir'];
        }
        
        $promo = mysqli_fetch_assoc($result);
        
        // Validasi minimum transaksi
        if ($total_amount < $promo['min_transaksi']) {
            return ['valid' => false, 'message' => 'Minimum transaksi Rp ' . number_format($promo['min_transaksi'], 0, ',', '.')];
        }
        
        // Validasi kategori/produk
        if ($promo['kategori_id'] && $promo['kategori_id'] != $kategori_id) {
            return ['valid' => false, 'message' => 'Promo hanya berlaku untuk kategori tertentu'];
        }
        
        if ($promo['produk_id'] && $promo['produk_id'] != $produk_id) {
            return ['valid' => false, 'message' => 'Promo hanya berlaku untuk produk tertentu'];
        }
        
        // Cek apakah user sudah menggunakan promo ini
        $usage_query = "SELECT COUNT(*) as used FROM promo_usage 
                       WHERE promo_id = {$promo['id']} AND user_id = $user_id";
        $usage_result = mysqli_query($this->conn, $usage_query);
        $usage = mysqli_fetch_assoc($usage_result);
        
        if ($usage['used'] > 0) {
            return ['valid' => false, 'message' => 'Anda sudah menggunakan promo ini sebelumnya'];
        }
        
        return ['valid' => true, 'promo' => $promo];
    }
    
    /**
     * Hitung diskon berdasarkan promo
     */
    public function calculateDiscount($promo, $total_amount) {
        $diskon = 0;
        
        switch ($promo['tipe']) {
            case 'percentage':
                $diskon = $total_amount * ($promo['nilai'] / 100);
                if ($promo['max_diskon']) {
                    $diskon = min($diskon, $promo['max_diskon']);
                }
                break;
                
            case 'fixed':
                $diskon = $promo['nilai'];
                break;
                
            case 'free_shipping':
                // Implementasi untuk free shipping
                $diskon = 0; // Akan dihitung berdasarkan biaya kurir
                break;
        }
        
        return $diskon;
    }
    
    /**
     * Catat penggunaan promo
     */
    public function recordPromoUsage($promo_id, $user_id, $transaksi_id, $diskon_didapat) {
        $promo_id = (int)$promo_id;
        $user_id = (int)$user_id;
        $transaksi_id = (int)$transaksi_id;
        $diskon_didapat = (float)$diskon_didapat;
        
        // Insert ke promo_usage
        $query = "INSERT INTO promo_usage (promo_id, user_id, transaksi_id, diskon_didapat) 
                  VALUES ($promo_id, $user_id, $transaksi_id, $diskon_didapat)";
        mysqli_query($this->conn, $query);
        
        // Update penggunaan_sekarang di tabel promos
        $update_query = "UPDATE promos SET penggunaan_sekarang = penggunaan_sekarang + 1 
                        WHERE id = $promo_id";
        mysqli_query($this->conn, $update_query);
    }
}
?> 