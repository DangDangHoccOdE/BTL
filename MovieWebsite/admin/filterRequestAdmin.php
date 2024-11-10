<?php
// Kiểm tra xem URL hiện tại có chứa "/admin" không
$current_url = $_SERVER['REQUEST_URI']; // Lấy URL hiện tại sau domain (ví dụ: /BTL/MovieWebsite/admin/adminPage.php)

// Kiểm tra nếu URL chứa "/admin" và người dùng chưa đăng nhập
if (strpos($current_url, '/admin') !== false) {
    // Kiểm tra xem người dùng đã đăng nhập chưa
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Nếu chưa đăng nhập, chuyển hướng đến trang admin_login
        header("Location: http://localhost/BTL/MovieWebsite/admin/auth/admin_login.php");
        exit(); // Kết thúc script sau khi chuyển hướng
    }
}
?>
