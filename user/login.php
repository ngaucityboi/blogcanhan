<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* NẠP KẾT NỐI DB: sửa đường dẫn theo cấu trúc của bạn
   /blogcanhan/
     ├─ includes/db.php
     └─ users/login.php  (hoặc user/login.php)
*/
require_once __DIR__ . '/../includes/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Nếu vẫn lỗi, bỏ comment dòng dưới để kiểm tra nhanh
// if (!isset($pdo) || !($pdo instanceof PDO)) { die('Không có kết nối DB ($pdo)'); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');  // cho phép nhập username hoặc email
    $password = (string)($_POST['password'] ?? '');

    if ($identity === '' || $password === '') {
        $error = 'Vui lòng nhập đủ tài khoản (username/email) và mật khẩu.';
    } else {
        $sql = "SELECT id, username, email, password, role, status
                FROM users
                WHERE username = :id OR email = :id
                LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute([':id' => $identity]);
        $user = $st->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Tài khoản hoặc mật khẩu không đúng.';
        } elseif ($user['status'] !== 'normal') {
            $error = 'Tài khoản của bạn đang bị khóa.';
        } else {
            // Đăng nhập thành công
            $_SESSION['user_id']  = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = (int)$user['role'];

            // Điều hướng về trang chủ (sửa đường dẫn nếu khác)
            header('Location: ../index.php');
            exit;
        }
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
