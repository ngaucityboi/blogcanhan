<?php
// Kết nối cơ sở dữ liệu
require_once '../includes/db.php';  // Đảm bảo đường dẫn đúng đến db.php

// Khởi tạo thông báo lỗi
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận giá trị từ form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Kiểm tra xem các trường có rỗng không
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "Tên đăng nhập, email và mật khẩu không được để trống.";
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại trong cơ sở dữ liệu chưa
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error_message = "Tên đăng nhập hoặc email đã tồn tại. Vui lòng chọn tên khác.";
        } else {
            // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Giá trị mặc định cho role và status
            $role = 2;  // Giá trị mặc định cho role
            $status = 'normal';  // Giá trị mặc định cho status

            // Thêm người dùng mới vào cơ sở dữ liệu
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $role, $status])) {
                // Đăng ký thành công, chuyển hướng tới trang đăng nhập
                header("Location: login.php");
                exit();
            } else {
                $error_message = "Đã xảy ra lỗi. Vui lòng thử lại.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Website Blog</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Đăng ký tài khoản mới</h2>
        
        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Form đăng ký -->
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" name="username" id="username" placeholder="Nhập tên đăng nhập" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Nhập email" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit">Đăng ký</button>
        </form>
        
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</body>
</html>
