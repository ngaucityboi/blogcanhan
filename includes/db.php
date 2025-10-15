<?php
// Kết nối database với PDO
$host = 'localhost';  // Sử dụng localhost (bỏ cổng nếu không cần thiết)
$dbname = 'blog';     // Tên cơ sở dữ liệu
$username = 'root';   // Tên người dùng MySQL
$password = '';       // Mật khẩu của bạn (nếu có)

// Kết nối PDO với MySQL
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Hiển thị lỗi nếu không thể kết nối
    die("Không thể kết nối tới cơ sở dữ liệu: " . $e->getMessage());
}
?>
