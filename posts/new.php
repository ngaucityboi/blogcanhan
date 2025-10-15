<?php
// /posts/new.php
if (session_status() === PHP_SESSION_NONE) session_start();
// Sửa lại: Dùng user_id từ session đã có
if (empty($_SESSION['user_id'])) {
  header('Location: /user/login.php?return=' . urlencode('/posts/new.php'));
  exit;
}
$pageTitle = 'Tạo bài viết mới';
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

// Flash message (nếu có)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<!-- Nạp thư viện TinyMCE từ CDN -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<div class="container" style="margin:24px auto; max-width: 900px;">
  <div class="card p-3">
    <h1 class="mb-2">Tạo bài viết mới</h1>

    <?php if ($flash): ?>
      <div class="mb-2 alert alert-<?= $flash['type']==='ok' ? 'success' : 'error' ?>">
        <?= htmlspecialchars($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <!-- Sửa lại action trỏ tới file /posts/create.php -->
    <form method="post" action="/posts/create.php">
      <div class="form-group">
        <label for="title">Tiêu đề</label>
        <input id="title" name="title" type="text" placeholder="Nhập tiêu đề bài viết..." style="font-size: 1.2rem; padding: 14px;">
      </div>

      <div class="form-group">
        <label for="content-editor">Nội dung</label>
        <!-- Textarea này sẽ được TinyMCE thay thế -->
        <textarea id="content-editor" name="content"></textarea>
      </div>

      <div class="mt-3" style="display:flex;gap:12px;justify-content:flex-end">
        <button class="btn btn-outline" type="submit" name="status" value="draft">Lưu nháp</button>
        <button class="btn" type="submit" name="status" value="published">Đăng bài</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Khởi tạo TinyMCE
  tinymce.init({
    selector: '#content-editor', // Target textarea bằng ID
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 500, // Chiều cao của editor
    
    // Cấu hình upload ảnh
    images_upload_url: '/api/upload_image.php', // Trỏ tới file PHP xử lý upload
    images_upload_base_path: '/',
    images_upload_credentials: true,
    
    // Tự động thêm thuộc tính loading="lazy" cho ảnh để tối ưu tốc độ
    image_advtab: true,
    image_caption: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    
    // Tuỳ chỉnh giao diện (optional)
    content_style: 'body { font-family:Inter,sans-serif; font-size:16px }'
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>