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

// Thêm CSS riêng cho trang chủ
$customCss = ['/assets/css/homepage/home.css'];

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($flash): ?>
  <div class="flash-message">
    <div class="alert alert-<?= $flash['type'] === 'ok' ? 'success' : 'error' ?>">
      <i class="fa-solid fa-<?= $flash['type'] === 'ok' ? 'circle-check' : 'circle-exclamation' ?>"></i>
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  </div>
<?php endif; ?>

<div class="container layout">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="card">
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
      <a href="/posts/new.php" class="btn" style="background: white; color: #667eea;">
        <i class="fa-solid fa-plus"></i>
        Viết bài mới
      </a>
    </div>

    <!-- Posts Feed -->
    <?php if (!$posts): ?>
      <div class="card center empty-state">
        <i class="fa-regular fa-newspaper"></i>
        <h3>Chưa có bài viết nào</h3>
        <p>Hãy là người đầu tiên chia sẻ câu chuyện của bạn!</p>
      </div>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <article class="card feed-post">
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
            <button class="btn btn-ghost">
              <i class="fa-regular fa-thumbs-up"></i>
              Thích
            </button>
            <button class="btn btn-ghost">
              <i class="fa-regular fa-comment"></i>
              Bình luận
            </button>
            <a class="btn btn-ghost" href="/posts/post_details.php?id=<?= (int)$p['id'] ?>">
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