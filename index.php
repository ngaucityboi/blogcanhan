<?php
// Include các function cần thiết
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions/auth_function.php';
require_once __DIR__ . '/includes/functions/post_function.php';

// Kiểm tra authentication và lấy thông tin user
$me = checkAuthenticationAndGetUser('/user/login.php', '/index.php');

// Lấy danh sách bài viết cho trang chủ
$posts = getPostsForHomepage($pdo, 15);

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Set page title cho header
$pageTitle = 'Trang chủ - MyBook';

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<style>
  .homepage-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 24px;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 32px;
  }
  
  .flash-message {
    max-width: 1400px;
    margin: 24px auto 0;
    padding: 0 24px;
  }
  
  .alert {
    padding: 16px 20px;
    border-radius: 12px;
    border-left: 4px solid;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    animation: slideDown 0.3s ease;
  }
  
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .alert-success {
    background: #e6ffed;
    border-color: #10b981;
    color: #046b1b;
  }
  
  .alert-error {
    background: #ffe9e9;
    border-color: #ef4444;
    color: #b00020;
  }
  
  .sidebar {
    position: sticky;
    top: 90px;
    height: fit-content;
  }
  
  .sidebar-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
  }
  
  .sidebar-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .sidebar-link {
    padding: 12px 16px;
    border-radius: 10px;
    text-decoration: none;
    color: #4b5563;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .sidebar-link:hover {
    background: #f3f4f6;
    color: #667eea;
    transform: translateX(4px);
  }
  
  .sidebar-link i {
    width: 20px;
    text-align: center;
  }
  
  .feed-container {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }
  
  .welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 32px;
    color: white;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
  }
  
  .welcome-card h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
  }
  
  .welcome-card p {
    font-size: 16px;
    opacity: 0.95;
    margin-bottom: 20px;
  }
  
  .welcome-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: #667eea;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  
  .welcome-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
  }
  
  .post-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
  }
  
  .post-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    transform: translateY(-2px);
  }
  
  .post-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }
  
  .post-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
  }
  
  .post-meta {
    flex: 1;
  }
  
  .post-author {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
  }
  
  .post-time {
    font-size: 13px;
    color: #6b7280;
  }
  
  .post-content {
    margin-bottom: 16px;
  }
  
  .post-content h3 {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 12px;
    line-height: 1.4;
  }
  
  .post-content p {
    color: #4b5563;
    line-height: 1.7;
    font-size: 15px;
  }
  
  .post-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
  }
  
  .post-action-btn {
    flex: 1;
    padding: 10px 16px;
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
  }
  
  .post-action-btn:hover {
    background: #f3f4f6;
    color: #667eea;
    border-color: #667eea;
  }
  
  .empty-state {
    text-align: center;
    padding: 60px 24px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  }
  
  .empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 16px;
  }
  
  .empty-state h3 {
    font-size: 20px;
    color: #4b5563;
    margin-bottom: 8px;
  }
  
  .empty-state p {
    color: #6b7280;
    font-size: 15px;
  }
  
  @media (max-width: 968px) {
    .homepage-container {
      grid-template-columns: 1fr;
      gap: 24px;
    }
    
    .sidebar {
      position: static;
      order: 2;
    }
  }
</style>

<?php if ($flash): ?>
  <div class="flash-message">
    <div class="alert alert-<?= $flash['type'] === 'ok' ? 'success' : 'error' ?>">
      <i class="fa-solid fa-<?= $flash['type'] === 'ok' ? 'circle-check' : 'circle-exclamation' ?>"></i>
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  </div>
<?php endif; ?>

<div class="homepage-container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-card">
      <h3 class="sidebar-title">
        <i class="fa-solid fa-compass"></i>
        Menu
      </h3>
      <nav class="sidebar-menu">
        <a href="/posts/new.php" class="sidebar-link">
          <i class="fa-solid fa-pen-to-square"></i>
          Tạo bài viết
        </a>
        <a href="/posts/index.php" class="sidebar-link">
          <i class="fa-solid fa-newspaper"></i>
          Tất cả bài viết
        </a>
        <a href="/posts/index.php?mine=1" class="sidebar-link">
          <i class="fa-solid fa-user"></i>
          Bài viết của tôi
        </a>
        <a href="/posts/index.php?status=draft" class="sidebar-link">
          <i class="fa-regular fa-file-lines"></i>
          Bản nháp
        </a>
      </nav>
    </div>
  </aside>

  <!-- Feed -->
  <main class="feed-container">
    <!-- Welcome Card -->
    <div class="welcome-card">
      <h1>👋 Chào mừng, <?= htmlspecialchars($me['username']) ?>!</h1>
      <p>Khám phá những câu chuyện thú vị và chia sẻ kiến thức của bạn với cộng đồng.</p>
      <a href="/posts/new.php" class="welcome-btn">
        <i class="fa-solid fa-plus"></i>
        Viết bài mới
      </a>
    </div>

    <!-- Posts Feed -->
    <?php if (!$posts): ?>
      <div class="empty-state">
        <i class="fa-regular fa-newspaper"></i>
        <h3>Chưa có bài viết nào</h3>
        <p>Hãy là người đầu tiên chia sẻ câu chuyện của bạn!</p>
      </div>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <article class="post-card">
          <div class="post-header">
            <div class="post-avatar">
              <?= strtoupper(substr($me['username'], 0, 1)) ?>
            </div>
            <div class="post-meta">
              <div class="post-author"><?= htmlspecialchars($me['username']) ?></div>
              <div class="post-time">
                <i class="fa-regular fa-clock"></i>
                <?= htmlspecialchars($p['published_at'] ?? 'Vừa xong') ?>
              </div>
            </div>
          </div>
          
          <div class="post-content">
            <?php if (!empty($p['title'])): ?>
              <h3><?= htmlspecialchars($p['title']) ?></h3>
            <?php endif; ?>
            <?php if (!empty($p['content'])): ?>
              <p><?= htmlspecialchars(mb_substr($p['content'], 0, 200)) ?><?= mb_strlen($p['content']) > 200 ? '...' : '' ?></p>
            <?php endif; ?>
          </div>
          
          <div class="post-actions">
            <button class="post-action-btn">
              <i class="fa-regular fa-thumbs-up"></i>
              Thích
            </button>
            <button class="post-action-btn">
              <i class="fa-regular fa-comment"></i>
              Bình luận
            </button>
            <a class="post-action-btn" href="/posts/post_details.php?id=<?= (int)$p['id'] ?>">
              <i class="fa-regular fa-eye"></i>
              Xem chi tiết
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
