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

  <!-- CSS chuẩn dự án -->
  <link rel="stylesheet" href="<?= $BASE_PATH ?>/assets/css/styles.css">

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    
    .modern-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      padding: 0;
    }
    
    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
    }
    
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: white;
      font-size: 24px;
      font-weight: 700;
      transition: transform 0.3s ease;
    }
    
    .brand:hover { transform: scale(1.05); }
    
    .brand-icon {
      background: rgba(255,255,255,0.2);
      width: 42px;
      height: 42px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }
    
    .search-container {
      flex: 1;
      max-width: 500px;
    }
    
    .search-form {
      position: relative;
      display: flex;
      align-items: center;
    }
    
    .search-input {
      width: 100%;
      padding: 12px 48px 12px 48px;
      border: none;
      border-radius: 50px;
      background: rgba(255,255,255,0.95);
      font-size: 15px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .search-input:focus {
      outline: none;
      background: white;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    
    .search-icon {
      position: absolute;
      left: 18px;
      color: #667eea;
      font-size: 16px;
    }
    
    .nav-menu {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .nav-link {
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      color: white;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(10px);
    }
    
    .nav-link:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }
    
    .nav-link i { font-size: 16px; }
    
    .user-menu {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 16px;
      background: rgba(255,255,255,0.15);
      border-radius: 50px;
      backdrop-filter: blur(10px);
    }
    
    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
    }
    
    .user-name {
      color: white;
      font-weight: 600;
      font-size: 14px;
    }
    
    .btn-primary {
      padding: 10px 24px;
      background: white;
      color: #667eea;
      border: none;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }
    
    .btn-logout {
      padding: 8px 16px;
      background: rgba(255,255,255,0.2);
      color: white;
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 50px;
      font-weight: 500;
      font-size: 13px;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    
    .btn-logout:hover {
      background: rgba(255,255,255,0.3);
      border-color: rgba(255,255,255,0.5);
    }
    
    @media (max-width: 768px) {
      .header-container { flex-wrap: wrap; padding: 12px 16px; }
      .search-container { order: 3; width: 100%; max-width: 100%; }
      .nav-link span { display: none; }
    }
  </style>
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
