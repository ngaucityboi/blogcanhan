<?php
// Include các function cần thiết
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions/auth_function.php';
require_once __DIR__ . '/includes/functions/post_function.php';

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

// Kiểm tra authentication và lấy thông tin user
$me = checkAuthenticationAndGetUser('/user/login.php', '/index.php');

// Lấy danh sách bài viết cho trang chủ
$posts = getPostsForHomepage($pdo, 15);
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
  <link rel="stylesheet" href="/assets/css/homepage/home.css">

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
