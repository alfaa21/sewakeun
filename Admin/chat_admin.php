<?php
include_once '../includes/session_bootstrap.php';
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../koneksi.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ambil daftar chat unik (per user & produk)
$daftar_chat = mysqli_query($conn, "SELECT cm.produk_id, cm.user_id, u.nama_lengkap, u.username, u.foto_profil, p.nama AS nama_produk, p.gambar FROM chat_messages cm JOIN users u ON cm.user_id=u.id JOIN produk p ON cm.produk_id=p.id GROUP BY cm.produk_id, cm.user_id ORDER BY MAX(cm.created_at) DESC");

// Ambil detail chat jika ada parameter
$produk_id = isset($_GET['produk_id']) ? intval($_GET['produk_id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$chat_detail = [];
$user_info = null;
$produk_info = null;
if ($produk_id && $user_id) {
    $q = mysqli_query($conn, "SELECT cm.*, u.nama_lengkap, u.username, u.foto_profil, p.nama AS nama_produk, p.gambar FROM chat_messages cm JOIN users u ON cm.user_id=u.id JOIN produk p ON cm.produk_id=p.id WHERE cm.produk_id=$produk_id AND cm.user_id=$user_id ORDER BY cm.created_at ASC");
    while ($row = mysqli_fetch_assoc($q)) $chat_detail[] = $row;
    // Info user & produk untuk header
    $user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
    $produk_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id=$produk_id"));
    // Handle balas pesan
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
        $msg = mysqli_real_escape_string($conn, $_POST['message']);
        mysqli_query($conn, "INSERT INTO chat_messages (produk_id, user_id, sender, message) VALUES ($produk_id, $user_id, 'admin', '$msg')");
        header("Location: chat_admin.php?produk_id=$produk_id&user_id=$user_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat User - Admin</title>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #f4f6fa; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .main-content { flex: 1; background: #f4f6fa; }
        .main-header { display: flex; justify-content: space-between; align-items: center; padding: 28px 32px 0 32px; }
        .main-header h2 { font-size: 1.6rem; font-weight: 600; margin: 0; }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #eaeaea; }
        /* Sidebar improvements */
        .sidebar { width: 260px; background: #232b3e; color: #ecf0f1; display: flex; flex-direction: column; justify-content: space-between; min-height: 100vh; box-shadow: 2px 0 12px rgba(44,62,80,0.07); }
        .sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 24px 18px 12px 18px; font-size: 1.3rem; font-weight: 700; letter-spacing: 1px; border-bottom: 1px solid #2c3e50; }
        .sidebar-logo i { font-size: 1.6rem; color: #a6ff00; }
        .sidebar-nav ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav li { margin-bottom: 6px; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; color: #ecf0f1; text-decoration: none; padding: 10px 18px; border-radius: 8px; transition: background 0.18s, color 0.18s; font-size: 1.05rem; }
        .sidebar-nav a.active, .sidebar-nav a:hover { background: #a6ff00; color: #232b3e; font-weight: 600; }
        .sidebar-footer { padding: 18px; border-top: 1px solid #2c3e50; }
        .sidebar-footer a { color: #ecf0f1; text-decoration: none; font-weight: 500; transition: color 0.18s; }
        .sidebar-footer a:hover { color: #a6ff00; }
        /* Card Chat improvements */
        .card-chat { background: #f7fafd; border-radius: 16px; box-shadow: 0 4px 24px rgba(44,62,80,0.07); padding: 0; margin: 32px; min-height: 70vh; display: flex; }
        /* Chat List improvements */
        .chat-list { min-width: 320px; max-width: 340px; border-right: 1px solid #e0e4ea; background: #232b3e; color: #fff; padding: 0; display: flex; flex-direction: column; }
        .chat-list-search { padding: 18px 18px 8px 18px; }
        .chat-list-search input { width: 100%; border-radius: 8px; border: none; padding: 10px 14px; font-size: 1rem; background: #1a2233; color: #fff; outline: none; margin-bottom: 8px; }
        .chat-list h5 { font-size: 1.1rem; font-weight: 700; margin: 0 0 12px 0; letter-spacing: 0.5px; padding: 0 18px; }
        .chat-list .list-group { border-radius: 0; padding: 0 0 18px 0; }
        .chat-list .list-group-item { border: none; border-radius: 0; margin-bottom: 0; transition: background 0.2s, color 0.2s; display: flex; align-items: center; gap: 14px; padding: 14px 18px; cursor: pointer; background: #232b3e; color: #fff; border-bottom: 1px solid #232b3e; }
        .chat-list .list-group-item.active, .chat-list .list-group-item:hover { background: #1a2233; color: #a6ff00; font-weight: 600; }
        .chat-list .avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #a6ff00; }
        .chat-list .chat-list-info { flex: 1; display: flex; flex-direction: column; }
        .chat-list .chat-list-name { font-size: 1.08rem; font-weight: 600; color: #fff; }
        .chat-list .chat-list-product { font-size: 0.95rem; color: #a6ff00; font-weight: 500; }
        .chat-list .chat-list-status { font-size: 0.92rem; color: #4cff4c; font-weight: 500; display: flex; align-items: center; gap: 4px; }
        .chat-list .chat-list-status-dot { width: 8px; height: 8px; background: #4cff4c; border-radius: 50%; display: inline-block; }
        /* Chat Area improvements */
        .chat-area { flex: 1; display: flex; flex-direction: column; background: #f7fafd; }
        .chat-area-header { display: flex; align-items: center; gap: 18px; background: #fff; border-bottom: 1px solid #e0e4ea; padding: 18px 28px; }
        .chat-area-header-img { width: 60px; height: 60px; border-radius: 12px; object-fit: cover; border: 2px solid #e0e4ea; }
        .chat-area-header-info { display: flex; flex-direction: column; }
        .chat-area-header-title { font-size: 1.25rem; font-weight: 700; color: #232b3e; }
        .chat-area-header-id { font-size: 1.02rem; color: #888; font-weight: 500; }
        .chat-box { flex: 1; display: flex; flex-direction: column; gap: 1.2rem; overflow-y: auto; margin: 0; padding: 32px 0 32px 0; background: #f7fafd; }
        .chat-bubble { border-radius: 22px; padding: 18px 26px; max-width: 420px; font-size: 1.08rem; box-shadow: 0 2px 12px rgba(44,62,80,0.07); word-break: break-word; position: relative; }
        .bubble-user { background: #a6ff00; color: #232b3e; align-self: flex-end; border-bottom-right-radius: 8px; margin-right: 32px; }
        .bubble-admin { background: #e3f2fd; color: #232b3e; align-self: flex-start; border-bottom-left-radius: 8px; margin-left: 32px; }
        .chat-bubble .bubble-meta { font-size: 0.98rem; color: #888; margin-top: 8px; font-weight: 500; }
        /* Chat Form improvements */
        .chat-form-wrap { background: #fff; border-top: 1px solid #e0e4ea; padding: 18px 28px; }
        .chat-form { display: flex; gap: 10px; align-items: center; }
        .chat-form textarea { resize: none; min-height: 44px; max-height: 90px; border-radius: 16px; border: 1px solid #e0e0e0; padding: 12px 18px; font-size: 1.08rem; flex: 1; background: #f7fafd; }
        .chat-form button { border-radius: 50%; width: 44px; height: 44px; background: #1976d2; color: #fff; border: none; font-size: 1.3rem; display: flex; align-items: center; justify-content: center; transition: background 0.18s; }
        .chat-form button:hover { background: #0056b3; }
        /* Responsive improvements */
        @media (max-width: 1100px) {
            .sidebar { width: 60px; }
            .sidebar-logo h3, .sidebar-nav a span, .sidebar-footer a span { display: none; }
            .sidebar-logo { justify-content: center; }
            .sidebar-nav a { justify-content: center; padding: 10px 0; }
            .chat-list { min-width: 80px; max-width: 100px; }
        }
        @media (max-width: 900px) {
            .dashboard-wrapper { flex-direction: column; }
            .card-chat { flex-direction: column; margin: 12px; padding: 0; }
            .chat-list { max-width: 100vw; min-width: 0; border-right: none; border-bottom: 1px solid #e0e4ea; }
            .chat-area { padding-left: 0; }
        }
        @media (max-width: 600px) {
            .main-header { padding: 18px 8px 0 8px; }
            .card-chat { margin: 2px; padding: 0; }
            .chat-form-wrap { padding: 8px 4px; }
            .chat-area-header { padding: 10px 8px; }
        }
        .chat-bubble.bubble-admin-right {
            background: #a6ff00;
            color: #232b3e;
            align-self: flex-end;
            border-bottom-right-radius: 8px;
            margin-right: 32px;
            border-bottom-left-radius: 22px;
        }
        .chat-bubble.bubble-user-left {
            background: #e3f2fd;
            color: #232b3e;
            align-self: flex-start;
            border-bottom-left-radius: 8px;
            margin-left: 32px;
            border-bottom-right-radius: 22px;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar" style="background:#2c3e50;color:#ecf0f1;">
        <div class="sidebar-logo">
            <i class="fas fa-box-open"></i>
            <h3>Sewakeun Admin</h3>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="item.php"><i class="fas fa-boxes"></i> Barang</a></li>
                <li><a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a></li>
                <li><a href="pesanan.php"><i class="fas fa-receipt"></i> Pesanan</a></li>
                <li><a href="pembayaran.php"><i class="fas fa-dollar-sign"></i> Pembayaran</a></li>
                <li><a href="chat_admin.php" class="active"><i class="fas fa-comments"></i> Chat</a></li>
                <li><a href="setting.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>
    <main class="main-content">
        <header class="main-header">
            <h2>Chat User</h2>
        </header>
        <div class="card-chat">
            <div class="chat-list">
                <div class="chat-list-search">
                    <input type="text" placeholder="Search..." id="chatSearch" onkeyup="filterChatList()">
                </div>
                <h5>Chat User</h5>
                <ul class="list-group" id="chatList">
                    <?php if($daftar_chat && mysqli_num_rows($daftar_chat)>0): while($c = mysqli_fetch_assoc($daftar_chat)): ?>
                    <a href="chat_admin.php?produk_id=<?= $c['produk_id'] ?>&user_id=<?= $c['user_id'] ?>" class="list-group-item list-group-item-action<?= ($produk_id==$c['produk_id']&&$user_id==$c['user_id'])?' active':'' ?>">
                        <img src="<?= htmlspecialchars($c['foto_profil'] ? '../'.$c['foto_profil'] : '../assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" class="avatar" alt="User">
                        <div class="chat-list-info">
                            <span class="chat-list-name"><?= htmlspecialchars($c['nama_lengkap'] ?: $c['username']) ?></span>
                            <span class="chat-list-product">
                                <img src="<?= htmlspecialchars($c['gambar'] ? '../'.$c['gambar'] : '../assets/images/default-produk.png') ?>" alt="Produk" style="width:20px;height:20px;object-fit:cover;border-radius:4px;margin-right:4px;vertical-align:middle;">
                                <?= htmlspecialchars($c['nama_produk']) ?>
                            </span>
                            <span class="chat-list-status"><span class="chat-list-status-dot"></span>online</span>
                        </div>
                    </a>
                    <?php endwhile; else: ?>
                    <li class="list-group-item">Belum ada chat masuk.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="chat-area">
                <?php if($produk_id && $user_id && $user_info && $produk_info): ?>
                    <div class="chat-area-header">
                        <img src="<?= htmlspecialchars($produk_info['gambar'] ? '../'.$produk_info['gambar'] : '../assets/images/default-produk.png') ?>" class="chat-area-header-img" alt="Produk">
                        <div class="chat-area-header-info">
                            <span class="chat-area-header-title">Chat Produk: <?= htmlspecialchars($produk_info['nama']) ?></span>
                            <span class="chat-area-header-id">ID Produk: <?= $produk_info['id'] ?></span>
                        </div>
                    </div>
                    <div class="chat-box mb-3">
                        <?php foreach($chat_detail as $msg): ?>
                            <div class="chat-bubble <?= $msg['sender']=='admin'?'bubble-admin-right':'bubble-user-left' ?>">
                                <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                <div class="bubble-meta">
                                    <?php if($msg['sender']=='admin'): ?>
                                        Admin
                                    <?php else: ?>
                                        <?= htmlspecialchars($user_info['nama_lengkap'] ?: $user_info['username']) ?>
                                    <?php endif; ?>
                                    &bull; <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="chat-form-wrap">
                        <form method="post" class="chat-form">
                            <textarea name="message" class="form-control" placeholder="Ketik balasan Anda..." required></textarea>
                            <button class="btn btn-primary" title="Kirim"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" style="margin:32px;">Pilih chat user untuk membalas pesan.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<script>
function filterChatList() {
    var input = document.getElementById('chatSearch');
    var filter = input.value.toLowerCase();
    var ul = document.getElementById('chatList');
    var items = ul.getElementsByTagName('a');
    for (var i = 0; i < items.length; i++) {
        var name = items[i].querySelector('.chat-list-name').textContent.toLowerCase();
        var product = items[i].querySelector('.chat-list-product').textContent.toLowerCase();
        if (name.indexOf(filter) > -1 || product.indexOf(filter) > -1) {
            items[i].style.display = '';
        } else {
            items[i].style.display = 'none';
        }
    }
}
</script> 