<?php
// Cho phép đặt tiêu đề trước khi include: $pageTitle = 'Tiêu đề trang';
if (!isset($pageTitle)) $pageTitle = 'MyBook';
if (session_status() === PHP_SESSION_NONE) session_start();

// Nếu dự án chạy dưới sub-folder (vd /blogcanhan) thì set $BASE_PATH = '/blogcanhan';
$BASE_PATH = '';
$userName  = $_SESSION['username'] ?? null;
$userRole  = $_SESSION['role'] ?? null;
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- CSS CHUNG CỦA DỰ ÁN -->
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/assets/css/styles.css">
  
  <!-- CSS CỦA HEADER (ĐÃ TÁCH RA FILE RIÊNG) -->
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/assets/css/header.css">

  <!-- CSS RIÊNG CHO TỪNG TRANG (NẠP ĐỘNG) -->
  <?php if (isset($customCss) && is_array($customCss)): ?>
    <?php foreach ($customCss as $cssFile): ?>
      <link rel="stylesheet" href="<?= $BASE_PATH . htmlspecialchars($cssFile) ?>">
    <?php endforeach; ?>
  <?php endif; ?>
</head>
<body>

<header class="modern-header">
  <div class="header-container">
    <a class="brand" href="<?= $BASE_PATH ?>/">
      <div class="brand-icon">
        <i class="fa-solid fa-book-open"></i>
      </div>
      <span>MyBook</span>
    </a>

    <div class="search-container">
      <form class="search-form" action="<?= $BASE_PATH ?>/search/search.php" method="get">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="search" name="q" class="search-input" placeholder="Tìm kiếm bài viết, tác giả...">
      </form>
    </div>

    <nav class="nav-menu">
      <a class="nav-link" href="<?= $BASE_PATH ?>/">
        <i class="fa-solid fa-house"></i>
        <span>Trang chủ</span>
      </a>
      <a class="nav-link" href="<?= $BASE_PATH ?>/posts/index.php">
        <i class="fa-solid fa-newspaper"></i>
        <span>Bài viết</span>
      </a>

      <?php if ($userName): ?>
        <?php if ($userRole === 'admin'): ?>
          <a class="nav-link" href="<?= $BASE_PATH ?>/admin/dashboard.php">
            <i class="fa-solid fa-gauge"></i>
            <span>Admin</span>
          </a>
        <?php endif; ?>
        
        <div class="user-menu">
          <div class="user-avatar">
            <?= strtoupper(substr($userName, 0, 1)) ?>
          </div>
          <span class="user-name"><?= htmlspecialchars($userName) ?></span>
        </div>
        
        <a class="btn-logout" href="<?= $BASE_PATH ?>/user/logout.php">
          <i class="fa-solid fa-right-from-bracket"></i>
          Thoát
        </a>
      <?php else: ?>
        <a class="btn-primary" href="<?= $BASE_PATH ?>/user/login.php">
          <i class="fa-solid fa-right-to-bracket"></i>
          Đăng nhập
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>