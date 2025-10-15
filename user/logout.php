<?php
// Include functions
require_once __DIR__ . '/../includes/function.php';

// Log logout attempt
logInfo("LOGOUT: User logout attempt - session data: " . print_r($_SESSION ?? [], true));

// Sử dụng function logoutUser từ auth_function.php
logoutUser();

logInfo("LOGOUT: User logged out successfully");

// Điều hướng về trang đăng nhập kèm thông báo
redirect('login.php?logged_out=1');
