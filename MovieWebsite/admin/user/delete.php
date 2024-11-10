<?php
session_start();
include '../../dbh.php';  // Đảm bảo đường dẫn đúng đến file dbh.php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Truy vấn để xóa người dùng
    $sql_delete = "DELETE FROM users WHERE id = $id";

    if (mysqli_query($conn, $sql_delete)) {
        echo "User deleted successfully!";
        header("Location: index.php");  // Chuyển hướng về trang danh sách người dùng
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
