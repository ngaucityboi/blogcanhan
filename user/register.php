<?php
// Include database connection và functions
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/function.php';

// Log page access
logInfo("REGISTER PAGE: Accessed register page");

// Khởi tạo thông báo lỗi
$error_message = '';

// Log request method
logDebug("REGISTER PAGE: Request method - " . $_SERVER['REQUEST_METHOD']);

// Check database connection
if (!isset($pdo) || !($pdo instanceof PDO)) {
    logError("REGISTER PAGE: Database connection failed - PDO is not available");
    $error_message = 'Lỗi kết nối database. Vui lòng thử lại sau.';
} else {
    logDebug("REGISTER PAGE: Database connection OK");
}

if (isPost()) {
    logInfo("REGISTER PAGE: Processing POST request");
    
    // Nhận và sanitize giá trị từ form
    $username = sanitizeInput(getPost('username'));
    $email = sanitizeInput(getPost('email'));
    $password = getPost('password');
    
    logDebug("REGISTER PAGE: Form data - username: " . $username . ", email: " . $email . ", password length: " . strlen($password));
    
    // Sử dụng function registerUser từ auth_function.php
    $registerResult = registerUser($username, $email, $password, $pdo);
    
    logData($registerResult, "REGISTER RESULT");
    
    if ($registerResult['success']) {
        logInfo("REGISTER PAGE: Registration successful, redirecting to login");
        // Đăng ký thành công, chuyển hướng tới trang đăng nhập
        redirect('login.php');
    } else {
        logWarning("REGISTER PAGE: Registration failed - " . $registerResult['error']);
        $error_message = $registerResult['error'];
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
