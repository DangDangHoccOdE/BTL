<?php
session_start(); // Bắt đầu phiên làm việc

// Hủy tất cả các biến trong session
session_unset();

// Hủy phiên làm việc
session_destroy();

// Chuyển hướng về trang đăng nhập admin
header("Location: login.php");
exit();
?>
