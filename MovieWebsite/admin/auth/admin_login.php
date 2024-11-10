<?php
session_start();
include '../../dbh.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $passwd = mysqli_real_escape_string($conn, $_POST['passwd']);

    // Truy vấn tìm người dùng theo tên đăng nhập
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Lỗi truy vấn SQL: " . mysqli_error($conn));
    }

    // Nếu tìm thấy người dùng
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Kiểm tra mật khẩu
        if (password_verify($passwd, $user['passwd'])) {
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                header("Location: ../adminPage.php");
                exit();
            } else {
                $error = "Tài khoản không có quyền admin";
            }
        } else {
            $error = "Mật khẩu không đúng"; // Chỉ yêu cầu nhập lại mật khẩu
        }
    } else {
        $error = "Tên đăng nhập không tồn tại";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="margin-top: 100px; max-width: 400px;">
        <h2 class="text-center">Đăng nhập Admin</h2>
        <?php if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        } ?>
        <form action="admin_login.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" required
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="passwd">Mật khẩu</label>
                <input type="password" class="form-control" id="passwd" name="passwd" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block">Đăng nhập</button>
        </form>
    </div>
</body>

</html>
