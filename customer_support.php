<?php
include_once 'includes/session_bootstrap.php';
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'user') {
    header('Location: login.php');
    exit();
}
include 'includes/_header.php';
?>

<main class="container">
    <section class="hero-bantuan text-center my-4 p-5 rounded" style="background-color: #e9ecef;">
        <h1 class="display-5 fw-bold">Pusat Bantuan</h1>
        <p class="fs-5 text-muted col-md-8 mx-auto">Ada pertanyaan? Cari jawaban di sini atau telusuri topik di bawah.</p>
        <div class="col-lg-6 mx-auto mt-4">
            <div class="input-group input-group-lg">

                
            </div>
        </div>
    </section>
    <section class="py-5">
        <h2 class="text-center mb-4">Telusuri Topik Bantuan</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <a href="#" class="topik-card" style="text-decoration:none;color:inherit;">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body p-4"><i class="fas fa-user-circle icon-lg mb-3" style="font-size:2.5rem;color:#0d6efd;"></i><h5 class="card-title">Akun & Profil</h5><p class="card-text text-muted">Kelola profil, ganti kata sandi, dan atur notifikasi Anda.</p></div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="#" class="topik-card" style="text-decoration:none;color:inherit;">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body p-4"><i class="fas fa-credit-card icon-lg mb-3" style="font-size:2.5rem;color:#0d6efd;"></i><h5 class="card-title">Pembayaran & Tagihan</h5><p class="card-text text-muted">Info metode pembayaran, cara bayar, dan riwayat tagihan.</p></div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4">
                <a href="#" class="topik-card" style="text-decoration:none;color:inherit;">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body p-4"><i class="fas fa-box-open icon-lg mb-3" style="font-size:2.5rem;color:#0d6efd;"></i><h5 class="card-title">Sewa & Pengembalian</h5><p class="card-text text-muted">Pelajari cara menyewa, mengambil, dan mengembalikan barang.</p></div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <section class="py-5">
        <h2 class="text-center mb-4">Pertanyaan Umum (FAQ)</h2>
        <div class="accordion col-lg-8 mx-auto" id="faqAccordion">
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">Bagaimana cara menyewa barang?</button></h2><div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body">Pilih barang yang Anda inginkan, tentukan tanggal sewa pada kalender yang tersedia, lalu klik tombol "Sewa Sekarang". Anda akan diarahkan ke halaman pembayaran untuk menyelesaikan transaksi.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">Apakah saya bisa membatalkan pesanan sewa?</button></h2><div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Ya, Anda bisa membatalkan pesanan hingga 24 jam sebelum waktu sewa dimulai untuk mendapatkan pengembalian dana penuh. Pembatalan setelah itu mungkin akan dikenakan biaya sesuai kebijakan yang berlaku.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">Apa yang harus saya lakukan jika barang yang disewa rusak?</button></h2><div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Harap segera hubungi penyewa melalui fitur chat di halaman pesanan Anda dan laporkan kerusakan tersebut. Jika perlu, tim dukungan pelanggan kami juga siap membantu Anda untuk mediasi.</div></div></div>
            <!-- FAQ tambahan -->
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">Bagaimana cara mengisi saldo?</button></h2><div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Anda dapat mengisi saldo melalui menu Top Up di dashboard dengan berbagai metode pembayaran yang tersedia.</div></div></div>
            
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix">Apa yang terjadi jika saya telat mengembalikan barang?</button></h2><div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Jika Anda telat mengembalikan barang, akan dikenakan denda sebesar 30% dari harga sewa per hari keterlambatan.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven">Bagaimana cara memberikan review produk?</button></h2><div id="collapseSeven" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Setelah masa sewa selesai, Anda dapat memberikan review pada produk melalui dashboard di menu transaksi.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight">Apa saja kurir yang tersedia untuk pengiriman?</button></h2><div id="collapseEight" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Kurir yang tersedia antara lain JNE, J&T, Gojek, Grab, dan kurir internal Sewaken.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine">Bagaimana cara mengubah data profil saya?</button></h2><div id="collapseNine" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Anda dapat mengubah data profil, password, dan foto profil melalui menu Edit Profil di dashboard.</div></div></div>
        </div>
    </section>
    <section class="text-center border-top my-5 py-5">
        <h2 class="fw-bold">Masih Butuh Bantuan?</h2>
        <p class="text-muted">Tim kami siap membantu Anda setiap saat.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
            <a href="chat.php" class="btn btn-primary btn-lg px-4 gap-3"><i class="fas fa-comments me-2"></i>Chat Langsung</a>
            <a href="mailto:support@sewaken.com" class="btn btn-outline-secondary btn-lg px-4"><i class="fas fa-envelope me-2"></i>Kirim Email</a>
        </div>
    </section>
    <section class="py-5">
        <div class="container" style="max-width:900px; margin:0 auto;">
            
            <h4 class="mt-4">Komitmen Kami</h4>
            <p>Di <b>[SEWAKEN]</b>, kami berkomitmen untuk memberikan layanan pelanggan yang responsif, solutif, dan ramah. Jangan ragu untuk menghubungi kami kapan pun Anda membutuhkan bantuan. Terima kasih telah menjadi bagian dari komunitas kami!</p>
            <div style="font-size:0.97em;color:#888;margin-top:2em;">
                <b>Tips:</b> Gunakan ikon untuk setiap metode kontak, selalu cantumkan jam operasional dan estimasi waktu respons (SLA) agar ekspektasi pelanggan terkelola dengan baik.<br>
                <b>Catatan:</b> Ganti [SEWAKEN] dan detail kontak dengan informasi bisnis Anda yang sebenarnya.
            </div>
        </div>
    </section>
</main>

<style>
.hero-bantuan {
    background-color: #e9ecef;
}
.topik-card {
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.topik-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.accordion-button:not(.collapsed) {
    color: #0d6efd;
    background-color: #e7f1ff;
}
.icon-lg {
    font-size: 2.5rem;
    color: #0d6efd;
}
</style>

<?php // Penutup tag body dan html sudah di _header.php ?> 