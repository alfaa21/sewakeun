<?php
require_once '../models/PromoModel.php';

class PromoModelTest {
    private $promoModel;
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->promoModel = new PromoModel($conn);
    }
    
    /**
     * Test validasi promo yang valid
     */
    public function testValidPromoCode() {
        // Insert test promo
        $test_promo = [
            'nama' => 'Test Promo 10%',
            'kode_promo' => 'TEST10',
            'tipe' => 'percentage',
            'nilai' => 10,
            'min_transaksi' => 100000,
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_berakhir' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'aktif'
        ];
        
        $this->insertTestPromo($test_promo);
        
        // Test validation
        $result = $this->promoModel->validatePromoCode('TEST10', 1, 150000);
        
        $this->assert($result['valid'], 'Promo valid seharusnya true');
        $this->assert($result['promo']['kode_promo'] === 'TEST10', 'Kode promo harus sesuai');
        
        // Cleanup
        $this->cleanupTestPromo('TEST10');
        
        echo "✓ Test valid promo code passed\n";
    }
    
    /**
     * Test validasi promo yang expired
     */
    public function testExpiredPromoCode() {
        // Insert expired promo
        $expired_promo = [
            'nama' => 'Expired Promo',
            'kode_promo' => 'EXPIRED',
            'tipe' => 'percentage',
            'nilai' => 10,
            'min_transaksi' => 100000,
            'tanggal_mulai' => date('Y-m-d', strtotime('-10 days')),
            'tanggal_berakhir' => date('Y-m-d', strtotime('-1 day')),
            'status' => 'aktif'
        ];
        
        $this->insertTestPromo($expired_promo);
        
        // Test validation
        $result = $this->promoModel->validatePromoCode('EXPIRED', 1, 150000);
        
        $this->assert(!$result['valid'], 'Expired promo seharusnya false');
        
        // Cleanup
        $this->cleanupTestPromo('EXPIRED');
        
        echo "✓ Test expired promo code passed\n";
    }
    
    /**
     * Test perhitungan diskon persentase
     */
    public function testPercentageDiscount() {
        $promo = [
            'tipe' => 'percentage',
            'nilai' => 20,
            'max_diskon' => 50000
        ];
        
        $total_amount = 300000;
        $expected_discount = 50000; // 20% dari 300k = 60k, tapi max 50k
        
        $actual_discount = $this->promoModel->calculateDiscount($promo, $total_amount);
        
        $this->assert($actual_discount === $expected_discount, 'Diskon persentase harus sesuai');
        
        echo "✓ Test percentage discount calculation passed\n";
    }
    
    /**
     * Test perhitungan diskon fixed
     */
    public function testFixedDiscount() {
        $promo = [
            'tipe' => 'fixed',
            'nilai' => 25000
        ];
        
        $total_amount = 100000;
        $expected_discount = 25000;
        
        $actual_discount = $this->promoModel->calculateDiscount($promo, $total_amount);
        
        $this->assert($actual_discount === $expected_discount, 'Diskon fixed harus sesuai');
        
        echo "✓ Test fixed discount calculation passed\n";
    }
    
    /**
     * Test minimum transaksi
     */
    public function testMinimumTransaction() {
        // Insert promo dengan minimum transaksi
        $min_promo = [
            'nama' => 'Min Transaction Promo',
            'kode_promo' => 'MIN100K',
            'tipe' => 'percentage',
            'nilai' => 10,
            'min_transaksi' => 100000,
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_berakhir' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'aktif'
        ];
        
        $this->insertTestPromo($min_promo);
        
        // Test dengan transaksi di bawah minimum
        $result = $this->promoModel->validatePromoCode('MIN100K', 1, 50000);
        
        $this->assert(!$result['valid'], 'Transaksi di bawah minimum seharusnya false');
        
        // Test dengan transaksi di atas minimum
        $result = $this->promoModel->validatePromoCode('MIN100K', 1, 150000);
        
        $this->assert($result['valid'], 'Transaksi di atas minimum seharusnya true');
        
        // Cleanup
        $this->cleanupTestPromo('MIN100K');
        
        echo "✓ Test minimum transaction validation passed\n";
    }
    
    /**
     * Helper function untuk insert test promo
     */
    private function insertTestPromo($promo) {
        $query = "INSERT INTO promos (nama, deskripsi, tipe, nilai, kode_promo, min_transaksi, tanggal_mulai, tanggal_berakhir, status) 
                  VALUES ('{$promo['nama']}', 'Test promo', '{$promo['tipe']}', {$promo['nilai']}, '{$promo['kode_promo']}', {$promo['min_transaksi']}, '{$promo['tanggal_mulai']}', '{$promo['tanggal_berakhir']}', '{$promo['status']}')";
        
        mysqli_query($this->conn, $query);
    }
    
    /**
     * Helper function untuk cleanup test promo
     */
    private function cleanupTestPromo($kode_promo) {
        $query = "DELETE FROM promos WHERE kode_promo = '$kode_promo'";
        mysqli_query($this->conn, $query);
    }
    
    /**
     * Assertion helper
     */
    private function assert($condition, $message) {
        if (!$condition) {
            throw new Exception("Assertion failed: $message");
        }
    }
    
    /**
     * Run all tests
     */
    public function runAllTests() {
        echo "Running PromoModel tests...\n";
        echo "================================\n";
        
        try {
            $this->testValidPromoCode();
            $this->testExpiredPromoCode();
            $this->testPercentageDiscount();
            $this->testFixedDiscount();
            $this->testMinimumTransaction();
            
            echo "================================\n";
            echo "All tests passed! ✓\n";
        } catch (Exception $e) {
            echo "Test failed: " . $e->getMessage() . "\n";
        }
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    include '../koneksi.php';
    $test = new PromoModelTest($conn);
    $test->runAllTests();
}
?> 