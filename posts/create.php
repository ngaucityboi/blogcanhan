<?php
// /posts/create.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: /user/login.php?return=' . urlencode('/posts/new.php'));
  exit;
}

require_once __DIR__ . '/../includes/db.php';

/* Chỉ chấp nhận POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /posts/new.php');
  exit;
}

/* ===== Helpers ===== */
function vn_to_ascii(string $s): string {
  $map = [
    'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
    'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
    'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
    'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
    'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
    'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
    'đ'=>'d',
    'À'=>'A','Á'=>'A','Ạ'=>'A','Ả'=>'A','Ã'=>'A','Â'=>'A','Ầ'=>'A','Ấ'=>'A','Ậ'=>'A','Ẩ'=>'A','Ẫ'=>'A','Ă'=>'A','Ằ'=>'A','Ắ'=>'A','Ặ'=>'A','Ẳ'=>'A','Ẵ'=>'A',
    'È'=>'E','É'=>'E','Ẹ'=>'E','Ẻ'=>'E','Ẽ'=>'E','Ê'=>'E','Ề'=>'E','Ế'=>'E','Ệ'=>'E','Ể'=>'E','Ễ'=>'E',
    'Ì'=>'I','Í'=>'I','Ị'=>'I','Ỉ'=>'I','Ĩ'=>'I',
    'Ò'=>'O','Ó'=>'O','Ọ'=>'O','Ỏ'=>'O','Õ'=>'O','Ô'=>'O','Ồ'=>'O','Ố'=>'O','Ộ'=>'O','Ổ'=>'O','Ỗ'=>'O','Ơ'=>'O','Ờ'=>'O','Ớ'=>'O','Ợ'=>'O','Ở'=>'O','Ỡ'=>'O',
    'Ù'=>'U','Ú'=>'U','Ụ'=>'U','Ủ'=>'U','Ũ'=>'U','Ư'=>'U','Ừ'=>'U','Ứ'=>'U','Ự'=>'U','Ử'=>'U','Ữ'=>'U',
    'Ỳ'=>'Y','Ý'=>'Y','Ỵ'=>'Y','Ỷ'=>'Y','Ỹ'=>'Y',
    'Đ'=>'D',
  ];
  return strtr($s, $map);
}
function slugify(string $title): string {
  $s = strtolower(vn_to_ascii($title));
  $s = preg_replace('~[^a-z0-9]+~', '-', $s);
  $s = trim($s, '-');
  return $s !== '' ? $s : 'bai-viet';
}
function make_title(string $text): string {
  $t = trim(preg_replace('/\s+/u', ' ', strip_tags($text)));
  return $t ? mb_substr($t, 0, 255) : 'Bài viết mới';
}
function estimate_minutes(string $text): int {
  $words = preg_split('/\s+/u', trim(strip_tags($text)), -1, PREG_SPLIT_NO_EMPTY);
  $count = $words ? count($words) : 0;
  return max(1, (int)ceil($count / 200));
}

/* Lấy dữ liệu */
$title    = trim($_POST['title'] ?? '');
$content  = trim($_POST['content'] ?? '');
$status   = $_POST['status'] ?? 'published';        // draft | published | archived
$authorId = (int)$_SESSION['user_id'];

if ($content === '') {
  $_SESSION['flash'] = ['type'=>'error','msg'=>'Nội dung bài viết không được để trống.'];
  header('Location: /posts/new.php'); exit;
}
if ($title === '') $title = make_title($content);
if (!in_array($status, ['draft','published','archived'], true)) $status = 'published';

/* Slug duy nhất */
$baseSlug = slugify($title); $slug = mb_substr($baseSlug, 0, 255);
$st = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
$st->execute([$slug]); $i = 2;
while ($st->fetchColumn() > 0) { $slug = mb_substr($baseSlug.'-'.$i, 0, 255); $st->execute([$slug]); $i++; }

$reading     = estimate_minutes($content);
$publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

try {
  $sql = "INSERT INTO posts
            (title, slug, content, status, author_id, reading_minutes, published_at, created_at, updated_at, comment_status)
          VALUES
            (:title,:slug,:content,:status,:author_id,:reading_minutes,:published_at,NOW(),NOW(),'open')";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':title'           => $title,
    ':slug'            => $slug,
    ':content'         => $content,
    ':status'          => $status,
    ':author_id'       => $authorId,
    ':reading_minutes' => $reading,
    ':published_at'    => $publishedAt,
  ]);

  $postId = (int)$pdo->lastInsertId();
  if ($status === 'published') {
    $_SESSION['flash'] = ['type'=>'ok','msg'=>'Đăng bài thành công!'];
    // ➜ quay về TRANG CHỦ và focus ngay bài mới
    header('Location: /index.php#post-' . $postId); 
  } else {
    $_SESSION['flash'] = ['type'=>'ok','msg'=>'Đã lưu bản nháp.'];
    header('Location: /posts/index.php?status=draft');
  }
  exit;

} catch (Throwable $e) {
  $_SESSION['flash'] = ['type'=>'error','msg'=>'Có lỗi khi lưu bài viết. Vui lòng thử lại.'];
  header('Location: /posts/new.php'); exit;
}