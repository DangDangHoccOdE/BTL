<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Người Dùng</title>
</head>
<body>
    <h1>Quản Lý Người Dùng</h1>
    <!-- Nội dung quản lý người dùng -->
</body>
</html>
