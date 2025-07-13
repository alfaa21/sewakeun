<?php
include 'koneksi.php';
session_start();
// Ambil user id dari session
$user_id = $_SESSION['user_id'] ?? 0;

// Proses kirim pesan user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $produk_id = isset($_GET['produk_id']) ? intval($_GET['produk_id']) : 0;
    if ($produk_id && $user_id) {
        $msg = mysqli_real_escape_string($conn, $_POST['message']);
        mysqli_query($conn, "INSERT INTO chat_messages (produk_id, user_id, sender, message) VALUES ($produk_id, $user_id, 'user', '$msg')");
        header("Location: message.php?produk_id=$produk_id");
        exit;
    }
}

// Ambil semua admin yang pernah dihubungi user (dari produk yang pernah di-chat)
$kontak_admin = [];
if ($user_id) {
    // Ambil semua produk yang pernah di-chat user
    $produk_admin = [];
    $q = mysqli_query($conn, "SELECT cm.produk_id, p.nama AS nama_produk, p.gambar, p.admin_id FROM chat_messages cm JOIN produk p ON cm.produk_id=p.id WHERE cm.user_id=$user_id GROUP BY cm.produk_id ORDER BY MAX(cm.created_at) DESC");
    while ($row = mysqli_fetch_assoc($q)) {
        if ($row['admin_id']) {
            $produk_admin[$row['admin_id']][] = $row;
        }
    }
    // Ambil data admin
    if (!empty($produk_admin)) {
        $admin_ids = implode(',', array_map('intval', array_keys($produk_admin)));
        $admin_q = mysqli_query($conn, "SELECT * FROM users WHERE id IN ($admin_ids) AND role='admin'");
        while ($a = mysqli_fetch_assoc($admin_q)) {
            $kontak_admin[] = [
                'admin' => $a,
                'produk' => $produk_admin[$a['id']] ?? []
            ];
        }
    }
}

// Produk yang dipilih (dari query string atau default produk pertama)
$produk_id = isset($_GET['produk_id']) ? intval($_GET['produk_id']) : ($daftar_chat[0]['produk_id'] ?? 0);

// Ambil detail produk
$produk = null;
$admin_produk = null;
if ($produk_id) {
    $produk_q = mysqli_query($conn, "SELECT * FROM produk WHERE id=$produk_id");
    $produk = mysqli_fetch_assoc($produk_q);
    if ($produk && $produk['admin_id']) {
        $admin_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=" . intval($produk['admin_id']) . " AND role='admin'"));
    }
}

// Ambil riwayat chat
$chat_detail = [];
if ($produk_id && $user_id) {
    $q = mysqli_query($conn, "SELECT * FROM chat_messages WHERE produk_id=$produk_id AND user_id=$user_id ORDER BY created_at ASC");
    while ($row = mysqli_fetch_assoc($q)) $chat_detail[] = $row;
}

// Ambil data admin
$admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE role='admin' LIMIT 1"));

// Cari admin_id dari produk aktif (jika ada)
$admin_id_aktif = 0;
if ($produk_id) {
    $produk_q = mysqli_query($conn, "SELECT admin_id FROM produk WHERE id=$produk_id");
    if ($row = mysqli_fetch_assoc($produk_q)) {
        $admin_id_aktif = (int)$row['admin_id'];
    }
}

include 'includes/_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Mengenai Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        body { background-color: #f0f2f5; margin: 0; padding: 0; height: 100vh; }
        .chat-app-container { width: 100vw; height: calc(100vh - 56px); min-height: calc(100vh - 56px); min-width: 100vw; margin: 0 !important; border-radius: 0; box-shadow: none; overflow: hidden; display: flex; background: #fff; position: fixed; top: 56px; left: 0; z-index: 1; }
        .chat-sidebar { width: 320px; background: #323a45; color: #fff; padding: 0; display: flex; flex-direction: column; height: 100vh; }
        .chat-sidebar .search-box { padding: 24px 20px 14px 20px; }
        .chat-sidebar input { background: #23272f; color: #fff; border: none; border-radius: 10px; font-size: 1.1em; padding: 12px 16px; }
        .chat-sidebar input:focus { background: #23272f; color: #fff; }
        .chat-sidebar .contacts-list { flex: 1; overflow-y: auto; padding: 0 0 14px 0; }
        .chat-sidebar .contact-item { display: flex; align-items: center; gap: 18px; padding: 18px 24px; cursor: pointer; border-bottom: 1px solid #23272f; transition: background 0.2s; text-decoration: none; color: inherit; }
        .chat-sidebar .contact-item.active, .chat-sidebar .contact-item:hover { background: #23272f; }
        .chat-sidebar .contact-avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; }
        .chat-sidebar .contact-info { flex: 1; }
        .chat-sidebar .contact-name { font-weight: 500; font-size: 1.18em; }
        .chat-sidebar .contact-status { font-size: 1em; color: #b2becd; }
        .chat-sidebar .status-dot { width: 13px; height: 13px; border-radius: 50%; display: inline-block; margin-right: 6px; }
        .chat-sidebar .online { background: #4cd137; }
        .chat-sidebar .offline { background: #e84118; }
        .chat-sidebar .away { background: #fbc531; }
        .chat-main { flex: 1; display: flex; flex-direction: column; background: #f4f6fa; height: 100vh; }
        .chat-header { background: #fff; border-bottom: 1px solid #eaeaea; padding: 28px 40px; display: flex; align-items: center; gap: 22px; }
        .chat-header .header-avatar { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; }
        .chat-header .header-info { flex: 1; }
        .chat-header .header-title { font-weight: 600; font-size: 1.25em; }
        .chat-header .header-desc { color: #888; font-size: 1.08em; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 48px 50px 38px 40px; display: flex; flex-direction: column; gap: 38px; background: #f4f6fa; }
        .bubble-row { display: flex; align-items: flex-end; gap: 14px; }
        .bubble-row.sent { justify-content: flex-end; }
        .bubble-row.received { justify-content: flex-start; }
        .message-bubble { padding: 18px 26px; border-radius: 22px; max-width: 75%; font-size: 1.13em; position: relative; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
        .message-bubble.sent { background: #a3e18f; color: #222; border-bottom-right-radius: 6px; }
        .message-bubble.received { background: #b7d6f8; color: #222; border-bottom-left-radius: 6px; }
        .message-meta { font-size: 0.93em; color: #888; margin-top: 7px; text-align: right; }
        .chat-input-area { background: #fff; border-top: 1px solid #eaeaea; padding: 24px 40px; position: sticky; bottom: 0; z-index: 2; }
        .chat-input-box { display: flex; gap: 16px; }
        .chat-input-box input { flex: 1; border-radius: 10px; font-size: 1.1em; border: 1px solid #eaeaea; padding: 14px 18px; }
        .chat-input-box button { border-radius: 10px; font-size: 1.1em; padding: 0 22px; }
        @media (max-width: 900px) {
            .chat-app-container { flex-direction: column; min-width: 0; width: 100vw; height: 100vh; }
            .chat-sidebar { width: 100%; min-width: 0; border-right: none; border-bottom: 1px solid #23272f; height: auto; }
            .chat-main { padding: 0; height: auto; }
        }
        /* Pastikan dropdown user tidak terpotong di kanan */
        ul.dropdown-menu[aria-labelledby="dropdownMenuLink"] {
            right: 0 !important;
            left: auto !important;
            min-width: 180px;
            transform: translateX(-10px);
        }
        #produkSearch::placeholder {
            color: #aaa !important;
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="chat-app-container">
    <div class="chat-sidebar">
        <div class="search-box">
            <input type="text" class="form-control" placeholder="Search..." id="produkSearch" onkeyup="filterProdukList()">
        </div>
        <div class="contacts-list" id="contactsList">
            <!-- Tahap 1: List admin -->
            <div id="adminList">
                <?php if (empty($kontak_admin)): ?>
                    <div class="text-center text-muted mt-5">Belum ada kontak admin</div>
                <?php else: foreach($kontak_admin as $idx => $adm): $a = $adm['admin']; ?>
                    <div class="contact-item" data-admin="<?= $a['id'] ?>" onclick="showProdukList(<?= $a['id'] ?>)">
                        <img src="<?= htmlspecialchars($a['foto_profil'] ?: 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" class="contact-avatar" alt="Admin">
                        <div class="contact-info">
                            <div class="contact-name"><?= htmlspecialchars($a['nama_lengkap'] ?: $a['username']) ?> <span class="badge bg-primary ms-1">Admin</span></div>
                            <div class="contact-status"><span class="status-dot online"></span>online</div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <!-- Tahap 2: List produk per admin -->
            <?php foreach($kontak_admin as $adm): $a = $adm['admin']; $produk_list = $adm['produk']; ?>
            <div class="produk-list-admin" id="produkListAdmin<?= $a['id'] ?>" style="display:none;">
                <button class="btn btn-sm btn-secondary mb-2" onclick="backToAdminList()"><i class="fas fa-arrow-left"></i> Kembali</button>
                <div class="contact-item" style="background:#23272f;cursor:default;">
                    <img src="<?= htmlspecialchars($a['foto_profil'] ?: 'assets/images/account-avatar-profile-user-6-svgrepo-com.svg') ?>" class="contact-avatar" alt="Admin">
                    <div class="contact-info">
                        <div class="contact-name"><?= htmlspecialchars($a['nama_lengkap'] ?: $a['username']) ?> <span class="badge bg-primary ms-1">Admin</span></div>
                        <div class="contact-status"><span class="status-dot online"></span>online</div>
                    </div>
                </div>
                <?php foreach($produk_list as $p): ?>
                <a href="message.php?produk_id=<?= $p['produk_id'] ?>" class="contact-item<?= ($produk_id==$p['produk_id'])?' active':'' ?>">
                    <img src="<?= htmlspecialchars($p['gambar']) ?>" class="contact-avatar" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                    <div class="contact-info">
                        <div class="contact-name"><?= htmlspecialchars($p['nama_produk']) ?></div>
                        <div class="contact-status"><span class="status-dot online"></span>produk</div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="chat-main">
        <div class="chat-header">
            <?php if($produk): ?>
                <img src="<?= htmlspecialchars($produk['gambar']) ?>" class="header-avatar" alt="<?= htmlspecialchars($produk['nama']) ?>">
                <div class="header-info">
                    <div class="header-title">Chat Produk: <?= htmlspecialchars($produk['nama']) ?></div>
                    <div class="header-desc">ID Produk: <?= htmlspecialchars($produk['id']) ?></div>
                </div>
            <?php else: ?>
                <div class="header-title">Chat Produk</div>
            <?php endif; ?>
        </div>
        <div class="chat-messages">
            <?php if (empty($chat_detail)): ?>
                <div class="text-center text-muted mt-5">Belum ada pesan</div>
            <?php else: foreach($chat_detail as $msg): ?>
                <div class="bubble-row <?= $msg['sender']=='admin'?'received':'sent' ?>">
                    <div class="message-bubble <?= $msg['sender']=='admin'?'received':'sent' ?>">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        <div class="message-meta">
                            <?php if($msg['sender']=='user'): ?>
                                <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Anda') ?>
                            <?php else: ?>
                                Admin
                            <?php endif; ?>
                            &bull; <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
        <div class="chat-input-area">
            <form class="chat-input-box" method="post" autocomplete="off">
                <input type="text" name="message" class="form-control" placeholder="Ketik balasan Anda..." required>
                <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showProdukList(adminId) {
    document.getElementById('adminList').style.display = 'none';
    var lists = document.querySelectorAll('.produk-list-admin');
    lists.forEach(function(el){ el.style.display = 'none'; });
    var produkList = document.getElementById('produkListAdmin'+adminId);
    if (produkList) produkList.style.display = 'block';
    // Kosongkan search saat ganti kontak
    var searchInput = document.getElementById('produkSearch');
    setTimeout(function() {
        searchInput.value = '';
        searchInput.placeholder = 'Search...';
    }, 100);
}
function backToAdminList() {
    document.getElementById('adminList').style.display = 'block';
    var lists = document.querySelectorAll('.produk-list-admin');
    lists.forEach(function(el){ el.style.display = 'none'; });
    var searchInput = document.getElementById('produkSearch');
    setTimeout(function() {
        searchInput.value = '';
        searchInput.placeholder = 'Search...';
    }, 100);
}
// Otomatis buka produk list jika ada admin aktif
window.addEventListener('DOMContentLoaded', function() {
    var adminIdAktif = <?= $admin_id_aktif ?>;
    if (adminIdAktif) {
        showProdukList(adminIdAktif);
    }
});
// Fungsi search produk hanya aktif saat list produk tampil
function filterProdukList() {
    var input = document.getElementById('produkSearch');
    var filter = input.value.toLowerCase();
    var produkLists = document.querySelectorAll('.produk-list-admin');
    produkLists.forEach(function(list) {
        if (list.style.display !== 'none') {
            var items = list.querySelectorAll('a.contact-item');
            items.forEach(function(item) {
                var nama = item.querySelector('.contact-name').textContent.toLowerCase();
                if (nama.indexOf(filter) > -1) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var btn = document.getElementById('dropdownMenuLink');
  if (btn) btn.addEventListener('click', function() {
    setTimeout(function() {
      var dd = document.querySelector('ul.dropdown-menu[aria-labelledby="dropdownMenuLink"]');
      if (dd) dd.style.display = 'block';
    }, 100);
  });
});
</script>
</body>
</html> 