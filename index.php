<?php

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<?php if ($flash): ?>
  <div class="container">
    <div class="card" style="margin-top:16px; border-color:<?= $flash['type']==='ok' ? '#c6f6d5' : '#ffd0d0' ?>; background:<?= $flash['type']==='ok' ? '#e6ffed' : '#ffe9e9' ?>; color:<?= $flash['type']==='ok' ? '#046b1b' : '#b00020' ?>;">
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  </div>
<?php endif; 

// Chỉ cho phép vào nếu đã đăng nhập
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: /user/login.php?return=' . urlencode('/index.php'));
  exit;
}
require_once __DIR__ . '/includes/db.php';

$me = [
  'id'       => (int)$_SESSION['user_id'],
  'username' => $_SESSION['username'] ?? 'Bạn',
  'role'     => (int)($_SESSION['role'] ?? 2),
];

$posts = [];
try {
  $st = $pdo->query("SELECT id, title, content, published_at FROM posts ORDER BY published_at DESC LIMIT 15");
  $posts = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $posts = [];
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bảng tin</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Top bar */
    .topbar{position:sticky;top:0;background:#fff;border-bottom:1px solid var(--header-line);z-index:60}
    .topbar .row{display:grid;grid-template-columns:240px 1fr 240px;align-items:center;gap:12px;padding:10px 0}
    .searchbox{display:flex;align-items:center;gap:8px;background:#f3f6fb;border:1px solid var(--card-border);border-radius:999px;padding:8px 12px}
    .searchbox input{border:0;background:transparent;padding:0;width:100%}
    .iconbar{display:flex;gap:14px;justify-content:flex-end}
    .iconbtn{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:#f3f6fb;border:1px solid var(--card-border)}

    /* Layout: 2 cột (Sidebar trái + Feed) */
    .layout-feed{display:grid;grid-template-columns: 300px minmax(0,640px);gap:20px;margin:18px auto}
    @media (max-width:992px){ .layout-feed{grid-template-columns: 1fr} }

    /* Sidebar trái: chức năng bài post */
    .shortcut a{display:flex;gap:10px;align-items:center;padding:10px;border-radius:12px;color:var(--text)}
    .shortcut a:hover{background:#f3f8ff}

    /* Composer + bài viết */
    .avatar{width:40px;height:40px;border-radius:50%;background:#dce9fb;flex:0 0 40px}
    .pill{display:inline-flex;gap:8px;align-items:center;padding:8px 12px;border-radius:999px;background:#f3f6fb;border:1px solid var(--card-border);font-size:.9rem}
    .feed{display:flex;flex-direction:column;gap:14px}
    .post{background:#fff;border:1px solid var(--card-border);border-radius:16px;box-shadow:var(--shadow);overflow:hidden}
    .post .head{display:flex;gap:10px;align-items:center;padding:12px}
    .post .name{font-weight:600}
    .post .time{font-size:.85rem;color:var(--muted)}
    .post .body{padding:0 12px 12px}
    .post .actions{display:flex;gap:8px;padding:8px 12px;border-top:1px solid #eef5ff}
  </style>
</head>
<body>

  <!-- TOP BAR -->
  <div class="topbar">
    <div class="container row">
      <div class="logo" style="display:flex;align-items:center;gap:10px">
        <i class="fa-brands fa-facebook" style="color:var(--primary);font-size:1.4rem"></i>
        <strong>MyBook</strong>
      </div>
      <div class="searchbox">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" placeholder="Tìm kiếm trên MyBook...">
      </div>
      <div class="iconbar">
        <a class="iconbtn" href="/"><i class="fa-solid fa-house"></i></a>
        <a class="iconbtn" href="/posts/index.php"><i class="fa-solid fa-newspaper"></i></a>
        <a class="iconbtn" href="/user/index.php"><i class="fa-regular fa-user"></i></a>
        <a class="iconbtn" href="/user/logout.php" title="Đăng xuất"><i class="fa-solid fa-right-from-bracket"></i></a>
      </div>
    </div>
  </div>

  <!-- LAYOUT: Sidebar trái + Feed giữa (KHÔNG còn cột phải) -->
  <div class="container layout-feed">
    <!-- LEFT: Chức năng bài viết -->
    <aside>
      <div class="card">
        <h3 class="mb-2">Bài viết</h3>
        <nav class="shortcut">
          <!-- Sidebar /index.php -->
          <a href="/posts/new.php"><i class="fa-solid fa-pen-to-square"></i> Tạo bài viết</a>
          <a href="/posts/index.php"><i class="fa-solid fa-newspaper"></i> Tất cả bài viết</a>
          <a href="/posts/index.php?mine=1"><i class="fa-solid fa-user"></i> Bài viết của tôi</a>
          <a href="/posts/index.php?status=draft"><i class="fa-regular fa-file-lines"></i> Bản nháp</a>
          <a href="/categories/index.php"><i class="fa-solid fa-folder-tree"></i> Thể loại</a>
          <a href="/tags/index.php"><i class="fa-solid fa-tags"></i> Thẻ tag</a>
        </nav>
      </div>
    </aside>

    <!-- MIDDLE: Feed -->
    <main>
      <!-- Feed posts -->
      <div class="feed">
        <?php if (!$posts): ?>
          <div class="post">
            <div class="head"><div class="avatar"></div>
              <div><div class="name">Hệ thống</div><div class="time">vừa xong</div></div>
            </div>
            <div class="body"><p>Chưa có bài viết. Hãy tạo bài mới để bắt đầu!</p></div>
          </div>
        <?php else: foreach ($posts as $p): ?>
          <article class="post">
            <div class="head">
              <div class="avatar"></div>
              <div>
                <div class="name"><?= htmlspecialchars($me['username']) ?></div>
                <div class="time"><?= htmlspecialchars($p['published_at'] ?? '') ?></div>
              </div>
            </div>
            <div class="body">
              <?php if (!empty($p['title'])): ?>
                <h3><?= htmlspecialchars($p['title']) ?></h3>
              <?php endif; ?>
              <?php if (!empty($p['content'])): ?>
                <p><?= htmlspecialchars($p['content']) ?></p>
              <?php endif; ?>
              <div class="actions">
                <button class="btn btn-outline"><i class="fa-regular fa-thumbs-up"></i> Thích</button>
                <button class="btn btn-outline"><i class="fa-regular fa-comment"></i> Bình luận</button>
                <a class="btn btn-outline" href="/posts/post_details.php?id=<?= (int)$p['id'] ?>"><i class="fa-regular fa-share-from-square"></i> Chi tiết</a>
              </div>
            </div>
          </article>
        <?php endforeach; endif; ?>
      </div>
    </main>
  </div>

</body>
</html>
