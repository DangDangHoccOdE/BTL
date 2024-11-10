<?php
session_start();
include '../../dbh.php';  // Đảm bảo đường dẫn đúng đến file dbh.php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin người dùng từ cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $passwd = mysqli_real_escape_string($conn, $_POST['passwd']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $DOB = mysqli_real_escape_string($conn, $_POST['DOB']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Mã hóa mật khẩu nếu có thay đổi
    $hashed_passwd = !empty($passwd) ? password_hash($passwd, PASSWORD_DEFAULT) : $user['passwd'];

    // Cập nhật thông tin người dùng
    $sql_update = "UPDATE users SET username = '$username', passwd = '$hashed_passwd', name = '$name', phone = '$phone', email = '$email', DOB = '$DOB', role = '$role' WHERE id = $id";

    if (mysqli_query($conn, $sql_update)) {
        echo "<div class='alert alert-success'>User updated successfully!</div>";
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
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit User</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="passwd" class="form-label">Password</label>
                <input type="password" class="form-control" name="passwd">
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo $user['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo $user['phone']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="DOB" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="DOB" value="<?php echo $user['DOB']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <option value="admin" <?php echo ($user['role'] == 'admin' ? 'selected' : ''); ?>>Admin</option>
                    <option value="user" <?php echo ($user['role'] == 'user' ? 'selected' : ''); ?>>User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
