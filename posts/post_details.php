<?php
// /posts/post_details.php
$pageTitle = 'Chi tiết bài viết';
require_once __DIR__ . '/../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($id > 0) {
  $st = $pdo->prepare("
    SELECT id, title, content, slug, status, author_id, reading_minutes,
           published_at, created_at, updated_at, comment_status
    FROM posts
    WHERE id = ? LIMIT 1
  ");
  $st->execute([$id]);
  $post = $st->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container" style="margin:24px auto">
  <div class="layout" style="grid-template-columns:1fr">
    <article class="card">
      <?php if ($post): ?>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p class="meta mb-2">
          <?= $post['published_at'] ? 'Đăng: ' . htmlspecialchars($post['published_at']) . ' • ' : '' ?>
          ~<?= (int)($post['reading_minutes'] ?? 1) ?> phút đọc
        </p>
        <div class="post-content">
          <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
      <?php else: ?>
        <h2>Không tìm thấy bài viết</h2>
        <p><a class="btn btn-outline" href="/posts/index.php">Về danh sách</a></p>
      <?php endif; ?>
    </article>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
