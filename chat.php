<?php
include_once 'includes/session_bootstrap.php';
include 'koneksi.php';

$api_key = "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";
if (!isset($_SESSION['chat_history'])) $_SESSION['chat_history'] = [];
$user_msg = $_POST['msg'] ?? '';
$bot_reply = '';

if (isset($_POST['clear'])) {
    $_SESSION['chat_history'] = [];
    header('Location: chat.php');
    exit();
}

// Ambil semua produk dari database
$produk_list = [];
$produk_query = mysqli_query($conn, "SELECT p.*, k.nama AS kategori, l.nama AS lokasi FROM produk p JOIN kategori k ON p.kategori_id = k.id JOIN lokasi l ON p.lokasi_id = l.id ORDER BY p.id DESC");
while($p = mysqli_fetch_assoc($produk_query)) {
    // Ambil rating untuk setiap produk
    $product_id = $p['id'];
    $review_stat = mysqli_query($conn, "SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM reviews WHERE product_id=$product_id");
    $stat = mysqli_fetch_assoc($review_stat);
    $total_review = (int)($stat['total'] ?? 0);
    $avg_rating = round($stat['avg_rating'] ?? 0, 1);
    
    // Tambahkan info rating ke array produk
    $p['total_review'] = $total_review;
    $p['avg_rating'] = $avg_rating;
    $produk_list[] = $p;
}
$produk_context = "";
foreach ($produk_list as $p) {
    $rating_info = "";
    if ($p['total_review'] > 0) {
        $rating_info = ", rating: {$p['avg_rating']}/5 ({$p['total_review']} ulasan)";
    } else {
        $rating_info = ", belum ada ulasan";
    }
    $produk_context .= "- {$p['nama']} (Rp " . number_format($p['harga'],0,',','.') . ", kategori: {$p['kategori']}, lokasi: {$p['lokasi']}, stok: {$p['stock']}{$rating_info}): {$p['deskripsi']}\n";
}

// Ambil kategori
$kategori_list = [];
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
while($k = mysqli_fetch_assoc($kategori_query)) {
    $kategori_list[] = $k['nama'];
}
$kategori_context = "Kategori tersedia: ".implode(', ', $kategori_list);

// Ambil lokasi
$lokasi_list = [];
$lokasi_query = mysqli_query($conn, "SELECT * FROM lokasi");
while($l = mysqli_fetch_assoc($lokasi_query)) {
    $lokasi_list[] = $l['nama'];
}
$lokasi_context = "Lokasi tersedia: ".implode(', ', $lokasi_list);

// Ambil settings
$settings = [];
$settings_query = mysqli_query($conn, "SELECT setting_name, setting_value FROM settings");
while($s = mysqli_fetch_assoc($settings_query)) {
    $settings[$s['setting_name']] = $s['setting_value'];
}
$settings_context = "";
foreach ($settings as $k => $v) {
    $settings_context .= "$k: $v\n";
}

// Info user jika login
$user_context = "";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = mysqli_query($conn, "SELECT username, email, no_hp, alamat, saldo FROM users WHERE id = '$user_id'");
    if ($user = mysqli_fetch_assoc($user_query)) {
        $user_context = "Info user login: username: {$user['username']}, email: {$user['email']}, no_hp: {$user['no_hp']}, alamat: {$user['alamat']}, saldo: Rp ".number_format($user['saldo'],0,',','.');
    }
}

$system_context = "Kamu adalah asisten chatbot untuk marketplace sewa barang. Jawab semua pertanyaan user HANYA dalam bahasa Indonesia, jangan gunakan bahasa lain.
".
    "Berikut adalah data penting dari database:\n".
    "$settings_context\n".
    "$kategori_context\n".
    "$lokasi_context\n".
    "Daftar produk:\n$produk_context\n".
    ($user_context ? "$user_context\n" : "").
    "Jawab pertanyaan user berdasarkan data di atas. Jika user bertanya tentang produk, lokasi, kategori, saldo, atau info lain, gunakan data ini. Jika user bertanya selain itu, jawab seperti asisten Sewaken biasa.";

if ($user_msg) {
$data = [
    "messages" => [
        ["role" => "system", "content" => $system_context],
        ["role" => "user", "content" => $user_msg]
    ],
    "model" => "llama3-8b-8192",
    "temperature" => 0.7,
    "max_tokens" => 300
];
$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);
$result = json_decode($response, true);
if (isset($result['choices'][0]['message']['content'])) {
    $bot_reply = $result['choices'][0]['message']['content'];
} elseif (isset($result['error']['message'])) {
    $bot_reply = 'Maaf, terjadi kesalahan: ' . htmlspecialchars($result['error']['message']);
} else {
    $bot_reply = 'Maaf, terjadi kesalahan. (Debug: ' . htmlspecialchars($response) . ')';
}
    // Simpan ke history
    $_SESSION['chat_history'][] = ['user' => $user_msg, 'bot' => $bot_reply];
}

include 'includes/_header.php';
?>

<main class="container mt-4 mb-4">
    <div class="card shadow-sm chat-card">
        <div class="card-header ai-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="#" onclick="window.history.back()" class="ai-header-back rounded-circle d-flex align-items-center justify-content-center me-2"><i class="fas fa-arrow-left"></i></a>
                <span class="ai-header-title">AI Recommendation Chat</span>
            </div>
            <form method="post" class="d-inline-flex ms-auto" onsubmit="return confirm('Hapus semua riwayat obrolan?');">
                <input type="hidden" name="clear" value="1">
                <button type="submit" class="btn btn-link p-0 border-0">
                    <i class="fas fa-trash ai-header-btn" style="cursor: pointer"></i>
                </button>
            </form>
        </div>
        <div class="card-body chat-messages-container">
            <div class="d-flex flex-column chat-messages-inner">
                <?php 
                // Ambil daftar produk untuk pencocokan nama
                $produk_nama_to_gambar = [];
                foreach ($produk_list as $p) {
                    $produk_nama_to_gambar[strtolower($p['nama'])] = $p;
                }
                // Ambil id produk juga untuk link detail/sewa/keranjang
                $produk_nama_to_id = [];
                $produk_id_map = [];
                $produk_query_id = mysqli_query($conn, "SELECT id, nama FROM produk");
                while($row = mysqli_fetch_assoc($produk_query_id)) {
                    $produk_nama_to_id[strtolower($row['nama'])] = $row['id'];
                    $produk_id_map[$row['id']] = $row['nama'];
                }
                ?>
                <?php foreach ($_SESSION['chat_history'] as $chat): ?>
                    <!-- Bubble user -->
                    <div class="d-flex justify-content-end mb-2">
                        <div class="card p-2 shadow-sm chat-bubble bg-primary text-white">
                            <?= nl2br(htmlspecialchars($chat['user'])) ?>
                        </div>
                    </div>
                    <!-- Bubble bot -->
                    <div class="d-flex justify-content-start mb-2">
                        <div class="card p-2 shadow-sm chat-bubble bg-light">
                            <?= nl2br(htmlspecialchars($chat['bot'])) ?>
                        </div>
                    </div>
                    <?php
                    // Cari nama produk yang disebutkan di balasan bot (maksimal 3)
                    $produk_ditampilkan = [];
                    $bot_text = strtolower($chat['bot']);
                    // Deteksi jika AI menyatakan tidak punya produk yang diminta user
                    $tidak_ada_produk = false;
                    $frasa_tidak_ada = [
                        'tidak memiliki',
                        'tidak punya',
                        'tidak tersedia',
                        'tidak ada produk',
                        'tidak menemukan',
                        'maaf',
                        'tidak dapat menemukan',
                        'tidak ditemukan',
                        'tidak menjual',
                        'tidak menawarkan',
                        'tidak menyediakan',
                        'tidak ditemukan produk',
                        'tidak ada barang',
                        'tidak ada yang cocok',
                        'tidak ada hasil',
                        'tidak ada di database',
                        'tidak ada di sini',
                        'tidak ada di sewaken',
                        'tidak ada di toko',
                        'tidak ada di katalog',
                        'tidak ada di daftar',
                        'tidak ada di list',
                        'tidak ada di koleksi',
                        'tidak ada di inventaris',
                        'tidak ada di stock',
                        'tidak ada di stok',
                        'tidak ada di gudang',
                        'tidak ada di persediaan',
                        'tidak ada di penawaran',
                        'tidak ada di produk',
                        'tidak ada di barang',
                        'tidak ada di item',
                        'tidak ada di hasil',
                        'tidak ada di pencarian',
                        'tidak ada di permintaan',
                        'tidak ada di permohonan',
                        'tidak ada di permintaan anda',
                        'tidak ada di permintaan user',
                        'tidak ada di permintaan pelanggan',
                        'tidak ada di permintaan customer',
                        'tidak ada di permintaan konsumen',
                        'tidak ada di permintaan pembeli',
                        'tidak ada di permintaan penyewa',
                        'tidak ada di permintaan peminjam',
                        'tidak ada di permintaan pengguna',
                        'tidak ada di permintaan visitor',
                        'tidak ada di permintaan pengunjung',
                        'tidak ada di permintaan tamu',
                        'tidak ada di permintaan klien',
                        'tidak ada di permintaan client',
                        'tidak ada di permintaan user',
                        'tidak ada di permintaan customer',
                        'tidak ada di permintaan konsumen',
                        'tidak ada di permintaan pembeli',
                        'tidak ada di permintaan penyewa',
                        'tidak ada di permintaan peminjam',
                        'tidak ada di permintaan pengguna',
                        'tidak ada di permintaan visitor',
                        'tidak ada di permintaan pengunjung',
                        'tidak ada di permintaan tamu',
                        'tidak ada di permintaan klien',
                        'tidak ada di permintaan client',
                    ];
                    foreach ($frasa_tidak_ada as $frasa) {
                        if (strpos($bot_text, $frasa) !== false) {
                            $tidak_ada_produk = true;
                            break;
                        }
                    }
                    if (!$tidak_ada_produk) {
                        foreach ($produk_nama_to_gambar as $nama => $p) {
                            if (count($produk_ditampilkan) >= 4) break;
                            // Cek apakah nama produk disebut di balasan bot (pencocokan sederhana)
                            if (strpos($bot_text, strtolower($nama)) !== false) {
                                $produk_ditampilkan[] = $p + ['id' => $produk_nama_to_id[$nama] ?? null];
                            }
                        }
                    }
                    ?>
                    <?php if (!empty($produk_ditampilkan)): ?>
                        <div class="row mb-3 justify-content-start g-4 ai-rekom-row">
                            <?php foreach ($produk_ditampilkan as $p): ?>
                            <div class="col-12 col-sm-6 col-md-3 d-flex">
                                <div class="ai-rekom-card flex-fill d-flex flex-column h-100">
                                    <div class="ai-rekom-img-wrap">
                                        <img src="<?= htmlspecialchars($p['gambar']) ?>" class="ai-rekom-img" alt="<?= htmlspecialchars($p['nama']) ?>">
                                    </div>
                                    <div class="ai-rekom-body flex-grow-1 d-flex flex-column">
                                        <div class="ai-rekom-title" title="<?= htmlspecialchars($p['nama']) ?>">
                                            <?= htmlspecialchars($p['nama']) ?>
                                        </div>
                                        <div class="ai-rekom-harga">Rp <?= number_format($p['harga'],0,',','.') ?></div>
                                        <div class="ai-rekom-rating">
                                            <?php
                                            $product_id = $p['id'];
                                            $review_stat = mysqli_query($conn, "SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM reviews WHERE product_id=$product_id");
                                            $stat = mysqli_fetch_assoc($review_stat);
                                            $total_review = (int)($stat['total'] ?? 0);
                                            $avg_rating = round($stat['avg_rating'] ?? 0, 1);
                                            if ($total_review > 0) {
                                                $full_star = floor($avg_rating);
                                                $half_star = ($avg_rating - $full_star) >= 0.5 ? 1 : 0;
                                                $empty_star = 5 - $full_star - $half_star;
                                                for ($i = 0; $i < $full_star; $i++) echo '<i class="fas fa-star text-warning"></i>';
                                                if ($half_star) echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                for ($i = $full_star + $half_star; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
                                                echo " <span class=\"text-muted small\">($total_review Ulasan)</span>";
                                            } else {
                                                for ($i = 0; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
                                                echo ' <span class="text-muted small">0 Ulasan</span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="ai-rekom-lokasi"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($p['lokasi']) ?></div>
                                        <div class="mt-auto d-flex flex-column gap-2">
                                            <a href="produk_detail.php?id=<?= urlencode($p['id']) ?>&from=ai" class="ai-rekom-btn btn btn-outline-primary btn-sm w-100">Lihat Detail</a>
                                            <a href="sewa_produk.php?id=<?= urlencode($p['id']) ?>" class="ai-rekom-btn btn btn-success btn-sm w-100">Sewa</a>
                                            <form method="post" action="chart_produk.php" class="d-inline">
                                                <input type="hidden" name="add_to_chart" value="<?= htmlspecialchars($p['id']) ?>">
                                                <button type="submit" class="ai-rekom-btn btn btn-primary btn-sm w-100">Tambah ke Keranjang</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif (trim($chat['bot']) !== '' && $chat['user'] !== '' && $chat['bot'] !== 'Maaf, terjadi kesalahan.'): ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-footer bg-light">
            <form method="post" class="input-group">
                <input type="text" name="msg" class="form-control" placeholder="Ketik pesan Anda..." required>
                <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</main>

<style>
.ai-rekom-row { 
    margin-top: 10px;
    margin-left: -8px;
    margin-right: -8px;
}
.ai-rekom-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px 0 rgba(60,72,88,0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s;
    border: none;
    min-width: 0;
    margin: 0 4px;
}
.ai-rekom-card:hover {
    box-shadow: 0 6px 32px 0 rgba(60,72,88,0.18);
}
.ai-rekom-img-wrap {
    width: 100%;
    aspect-ratio: 1/1;
    background: #f6f8fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-bottom: 1px solid #f0f0f0;
}
.ai-rekom-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.25s;
}
.ai-rekom-card:hover .ai-rekom-img {
    transform: scale(1.07);
}
.ai-rekom-body {
    padding: 0.8rem 0.9rem 0.9rem 0.9rem;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.ai-rekom-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.3rem;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ai-rekom-harga {
    color: #1976d2;
    font-weight: 500;
    font-size: 0.92rem;
    margin-bottom: 0.2rem;
}
.ai-rekom-rating {
    font-size: 0.85rem;
    margin-bottom: 0.3rem;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ai-rekom-rating .text-warning {
    color: #ffc107 !important;
}
.ai-rekom-rating .small {
    font-size: 0.8rem;
    opacity: 0.8;
}
.ai-rekom-lokasi {
    color: #888;
    font-size: 0.85rem;
    margin-bottom: 0.7rem;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ai-rekom-btn {
    border-radius: 10px !important;
    font-size: 0.85rem !important;
    font-weight: 500;
    box-shadow: none !important;
    transition: background 0.18s, color 0.18s;
    padding: 0.35rem 0.7rem !important;
}
@media (max-width: 900px) {
    .ai-rekom-row { gap: 0.8rem !important; }
    .col-12 { padding: 0 8px; }
}
.ai-header {
    background: linear-gradient(90deg, #1976d2 0%, #2196f3 100%);
    color: #fff !important;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    min-height: 60px;
    padding: 0.7rem 1.5rem 0.7rem 1.2rem;
    box-shadow: 0 2px 8px 0 rgba(60,72,88,0.07);
}
.ai-header-back {
    width: 42px;
    height: 42px;
    background: #fff;
    color: #1976d2;
    font-size: 1.3rem;
    border: none;
    box-shadow: 0 1px 4px rgba(60,72,88,0.10);
    transition: background 0.18s, color 0.18s;
    text-decoration: none;
}
.ai-header-back:hover {
    background: #e3f2fd;
    color: #1565c0;
}
.ai-header-title {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #fff;
}
.ai-header-btn {
    background: #fff;
    color: #1976d2;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.01rem;
    padding: 0.5rem 1.2rem;
    box-shadow: 0 1px 4px rgba(60,72,88,0.10);
    transition: background 0.18s, color 0.18s;
    margin-left: 1rem;
}
.ai-header-btn:hover {
    background: #e3f2fd;
    color: #1565c0;
}
@media (max-width: 600px) {
    .ai-header { flex-direction: column; align-items: flex-start !important; padding: 0.7rem 0.7rem 0.7rem 0.7rem; }
    .ai-header-title { font-size: 1.05rem; }
    .ai-header-btn { width: 100%; margin-left: 0; margin-top: 0.7rem; }
}
</style>

<?php // The main and body/html tags are closed by includes/_header.php ?>

