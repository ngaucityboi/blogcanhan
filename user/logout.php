<?php
// /user/logout.php
if (session_status() === PHP_SESSION_NONE) session_start();

/* Xoá toàn bộ session */
$_SESSION = [];

/* Xoá cookie phiên nếu có */
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

/* Huỷ phiên */
session_destroy();

/* Điều hướng về trang đăng nhập kèm cờ thông báo */
header('Location: /user/login.php?logged_out=1');
exit;
