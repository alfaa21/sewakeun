<?php
require_once 'models/PromoModel.php';

class PromoController {
    private $promoModel;
    
    public function __construct($conn) {
        $this->promoModel = new PromoModel($conn);
    }
    
    /**
     * Tampilkan halaman promo
     */
    public function showPromoPage() {
        $promos = $this->promoModel->getActivePromos();
        
        // Render view
        include 'views/promo/list.php';
    }
    
    /**
     * API untuk validasi kode promo (AJAX)
     */
    public function validatePromoCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $kode_promo = $_POST['kode_promo'] ?? '';
        $user_id = $_SESSION['user_id'] ?? 0;
        $total_amount = (float)($_POST['total_amount'] ?? 0);
        $kategori_id = (int)($_POST['kategori_id'] ?? 0);
        $produk_id = (int)($_POST['produk_id'] ?? 0);
        
        if (empty($kode_promo)) {
            echo json_encode(['valid' => false, 'message' => 'Kode promo tidak boleh kosong']);
            return;
        }
        
        $result = $this->promoModel->validatePromoCode($kode_promo, $user_id, $total_amount, $kategori_id, $produk_id);
        
        if ($result['valid']) {
            $diskon = $this->promoModel->calculateDiscount($result['promo'], $total_amount);
            $result['diskon'] = $diskon;
            $result['total_setelah_diskon'] = $total_amount - $diskon;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * API untuk mendapatkan daftar promo (AJAX)
     */
    public function getPromos() {
        $promos = $this->promoModel->getActivePromos();
        
        header('Content-Type: application/json');
        echo json_encode(['promos' => $promos]);
    }
    
    /**
     * Proses penggunaan promo saat checkout
     */
    public function applyPromoToTransaction($transaksi_id, $promo_id, $user_id, $diskon_didapat) {
        return $this->promoModel->recordPromoUsage($promo_id, $user_id, $transaksi_id, $diskon_didapat);
    }
}
?> 