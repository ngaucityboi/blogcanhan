<?php
// /api/upload_image.php
header('Content-Type: application/json');

// Cấu hình thư mục upload
$uploadDir = __DIR__ . '/../uploads/images/';
// URL công khai để truy cập ảnh
$uploadUrl = '/uploads/images/'; 

// Tạo thư mục nếu chưa tồn tại
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Kiểm tra xem có file nào được gửi lên không
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => ['message' => 'Lỗi upload file.']]);
    exit;
}

$tempFile = $_FILES['file']['tmp_name'];

// Kiểm tra loại file an toàn (chỉ cho phép ảnh)
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$fileMimeType = mime_content_type($tempFile);
if (!in_array($fileMimeType, $allowedMimeTypes)) {
    echo json_encode(['error' => ['message' => 'Định dạng file không được phép.']]);
    exit;
}

// Tạo tên file mới, duy nhất để tránh ghi đè
$fileName = uniqid() . '-' . basename($_FILES['file']['name']);
$targetFile = $uploadDir . $fileName;

// Di chuyển file đã upload vào thư mục đích
if (move_uploaded_file($tempFile, $targetFile)) {
    // Trả về JSON chứa URL của ảnh cho TinyMCE
    // Định dạng JSON này là bắt buộc theo tài liệu của TinyMCE
    echo json_encode(['location' => $uploadUrl . $fileName]);
} else {
    echo json_encode(['error' => ['message' => 'Không thể di chuyển file đã upload.']]);
}