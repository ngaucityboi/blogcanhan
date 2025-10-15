<?php
// /posts/new.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: /user/login.php?return=' . urlencode('/posts/new.php'));
  exit;
}
$pageTitle = 'Tạo bài viết';
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

// flash message (nếu có)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="container" style="margin:24px auto; max-width: 800px;">
  <div class="card">
    <h2 class="mb-2">Tạo bài viết</h2>

    <?php if ($flash): ?>
      <div class="mb-2" style="padding:10px;border-radius:10px;
           background:<?= $flash['type']==='ok' ? '#e6ffed' : '#ffe9e9' ?>;
           color:<?= $flash['type']==='ok' ? '#046b1b' : '#b00020' ?>;
           border:1px solid <?= $flash['type']==='ok' ? '#c6f6d5' : '#ffd0d0' ?>;">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/posts/create.php">
      <div class="form-group">
        <label for="title">Tiêu đề (tuỳ chọn)</label>
        <input id="title" name="title" type="text" placeholder="Nhập tiêu đề nếu muốn">
      </div>

      <div class="form-group">
        <label for="content">Nội dung <span class="muted">(bắt buộc)</span></label>
        <textarea id="content" name="content" rows="10" placeholder="Viết nội dung bài viết tại đây..." required></textarea>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button class="btn btn-outline" type="submit" name="status" value="draft">Lưu nháp</button>
        <button class="btn" type="submit" name="status" value="published">Đăng</button>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
