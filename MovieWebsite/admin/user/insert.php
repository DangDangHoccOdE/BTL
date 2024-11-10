<?php
session_start();
include '../../dbh.php';  // Đảm bảo đường dẫn đúng đến file dbh.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và phòng chống SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $passwd = mysqli_real_escape_string($conn, $_POST['passwd']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $DOB = mysqli_real_escape_string($conn, $_POST['DOB']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Mã hóa mật khẩu
    $hashed_passwd = password_hash($passwd, PASSWORD_DEFAULT);

    // Truy vấn thêm người dùng mới vào cơ sở dữ liệu
    $sql = "INSERT INTO users (username, passwd, name, phone, email, DOB, role) 
            VALUES ('$username', '$hashed_passwd', '$name', '$phone', '$email', '$DOB', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success'>User added successfully!</div>";
        header("Location: index.php");  // Chuyển hướng về trang danh sách người dùng
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add New User</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label for="passwd" class="form-label">Password</label>
                <input type="password" class="form-control" name="passwd" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="DOB" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="DOB" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
