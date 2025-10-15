<?php
// Cho phép đặt tiêu đề trước khi include: $pageTitle = 'Tiêu đề trang';
if (!isset($pageTitle)) $pageTitle = 'MyBook';
if (session_status() === PHP_SESSION_NONE) session_start();

// Nếu dự án chạy dưới sub-folder (vd /blogcanhan) thì set $BASE_PATH = '/blogcanhan';
$BASE_PATH = '';
$userName  = $_SESSION['username'] ?? null;
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

  <!-- CSS chuẩn dự án -->
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/assets/css/styles.css">

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
  <div class="container inner" style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
    <a class="logo" href="<?= $BASE_PATH ?>/">MyBook</a>

    <form class="searchbox" action="<?= $BASE_PATH ?>/search/search.php" method="get"
          style="display:flex;align-items:center;gap:8px;background:#f3f6fb;border:1px solid var(--card-border);border-radius:999px;padding:8px 12px">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" name="q" placeholder="Tìm kiếm..." style="border:0;background:transparent;padding:0;width:260px">
    </form>

    <nav class="iconbar" style="display:flex;gap:12px">
      <a class="btn btn-ghost btn-icon" title="Trang chủ" href="<?= $BASE_PATH ?>/"><i class="fa-solid fa-house"></i></a>
      <a class="btn btn-ghost btn-icon" title="Bài viết" href="<?= $BASE_PATH ?>/posts/index.php"><i class="fa-solid fa-newspaper"></i></a>

      <?php if ($userName): ?>
        <a class="btn btn-ghost" href="<?= $BASE_PATH ?>/user/index.php"><i class="fa-regular fa-user"></i> <?= htmlspecialchars($userName) ?></a>
        <a class="btn btn-outline btn-sm" href="<?= $BASE_PATH ?>/user/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Thoát</a>
      <?php else: ?>
        <a class="btn btn-outline btn-sm" href="<?= $BASE_PATH ?>/user/login.php">Đăng nhập</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
