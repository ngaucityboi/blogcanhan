<?php
$pageTitle = 'Tất cả bài viết';
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

$mine   = isset($_GET['mine']) && !empty($_SESSION['user_id']);
$status = $_GET['status'] ?? '';

$where = '1';
$params = [];

if ($mine)                { $where .= ' AND author_id = ?'; $params[] = (int)$_SESSION['user_id']; }
if ($status === 'draft')  { $where .= " AND status = 'draft'"; }

$sql  = "SELECT id, title, excerpt, published_at FROM posts WHERE $where ORDER BY published_at DESC LIMIT 20";
$st   = $pdo->prepare($sql);
$st->execute($params);
?>
<div class="container" style="margin:24px auto">
  <h2 class="mb-2">Tất cả bài viết</h2>
  <div class="grid">
    <?php while ($post = $st->fetch(PDO::FETCH_ASSOC)): ?>
      <article class="post-card">
        <div class="thumb"></div>
        <div>
          <h3><?= htmlspecialchars($post['title']) ?></h3>
          <p><?= htmlspecialchars($post['excerpt']) ?></p>
          <a class="btn btn-outline" href="post_details.php?id=<?= (int)$post['id'] ?>">Đọc tiếp</a>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
