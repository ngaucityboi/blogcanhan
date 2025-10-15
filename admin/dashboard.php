<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
?>
<h1>Trang Quản trị</h1>
<p>Chào mừng, admin!</p>
<ul>
    <li><a href="manage_posts.php">Quản lý bài viết</a></li>
    <li><a href="manage_users.php">Quản lý người dùng</a></li>
    <li><a href="manage_comments.php">Quản lý bình luận</a></li>
</ul>
