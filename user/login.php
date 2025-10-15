<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Include database connection và functions
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/function.php';

// Log page access
logInfo("LOGIN PAGE: Accessed login page");

ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = '';

// Log request method
logDebug("LOGIN PAGE: Request method - " . $_SERVER['REQUEST_METHOD']);

// Log session data
logDebug("LOGIN PAGE: Current session data - " . print_r($_SESSION, true));

// Check database connection
if (!isset($pdo) || !($pdo instanceof PDO)) {
    logError("LOGIN PAGE: Database connection failed - PDO is not available");
    $error = 'Lỗi kết nối database. Vui lòng thử lại sau.';
} else {
    logDebug("LOGIN PAGE: Database connection OK");
}

if (isPost()) {
    logInfo("LOGIN PAGE: Processing POST request");
    
    $identity = sanitizeInput(getPost('identity'));
    $password = getPost('password');
    
    logDebug("LOGIN PAGE: Form data - identity: " . $identity . ", password length: " . strlen($password));
    
    // Sử dụng function loginUser từ auth_function.php
    $loginResult = loginUser($identity, $password, $pdo);
    
    logData($loginResult, "LOGIN RESULT");
    
    if ($loginResult['success']) {
        logInfo("LOGIN PAGE: Login successful, redirecting to index");
        // Đăng nhập thành công, điều hướng về trang chủ
        redirect('../index.php');
    } else {
        logWarning("LOGIN PAGE: Login failed - " . $loginResult['error']);
        $error = $loginResult['error'];
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
  <div class="container">
    <h2>Đăng nhập</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="on">
      <label for="identity">Tên đăng nhập hoặc Email</label>
      <input type="text" id="identity" name="identity" required>

      <label for="password">Mật khẩu</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Đăng nhập</button>
    </form>

    <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
  </div>
</body>
</html>
