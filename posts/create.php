<?php
// /posts/create.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions/post_function.php';

// 1. Kiểm tra đăng nhập và phương thức request
if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    die('Bạn cần đăng nhập để thực hiện chức năng này.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Phương thức không được hỗ trợ.');
}

// 2. Lấy và kiểm tra dữ liệu từ form
$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$status  = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';

// Nội dung là bắt buộc
if (empty($content)) {
    $_SESSION['flash'] = ['type' => 'err', 'msg' => 'Nội dung bài viết không được để trống.'];
    header('Location: /posts/new.php');
    exit;
}

// 3. Chuẩn bị dữ liệu để lưu vào database
$postData = [
    'title'     => $title,
    'content'   => $content, // Lưu trực tiếp HTML từ TinyMCE
    'status'    => $status,
    'author_id' => $_SESSION['user_id']
];

// 4. Gọi hàm tạo bài viết
$newPostId = createPost($pdo, $postData);

// 5. Điều hướng và thông báo kết quả
if ($newPostId) {
    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Đã tạo bài viết thành công!'];
    // Chuyển hướng đến trang xem bài viết (bạn cần tạo trang này)
    // Tạm thời chuyển về trang chủ
    header('Location: /index.php');
} else {
    $_SESSION['flash'] = ['type' => 'err', 'msg' => 'Có lỗi xảy ra, không thể tạo bài viết.'];
    header('Location: /posts/new.php');
}
exit;