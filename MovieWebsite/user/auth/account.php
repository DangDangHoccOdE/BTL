<?php
session_start();
include '../../dbh.php';

$errors = []; 
$showPasswordForm = false;
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['id'];

    // Cập nhật thông tin cá nhân
    if (isset($_POST['update_info'])) {
        $name = trim($_POST['fname']);
        $phone = trim($_POST['phn']);
        $dob = $_POST['dob'];

        // Kiểm tra đầu vào
        if (empty($name)) {
            $errors['fname'] = "Vui lòng nhập họ tên.";
        }

        if (empty($phone) || !preg_match('/^\d{10}$/', $phone)) {
            $errors['phn'] = "Số điện thoại phải là 10 chữ số.";
        }

        if (empty($dob)) {
            $errors['dob'] = "Vui lòng chọn ngày sinh.";
        }

        // Nếu không có lỗi, thực hiện cập nhật
        if (empty($errors)) {
            $update_sql = "UPDATE users SET name = ?, phone = ?, DOB = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            if (!$stmt) {
                die("Lỗi chuẩn bị câu truy vấn: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $dob, $id);
            if(mysqli_stmt_execute($stmt)) {
                $successMessage = 'Cập nhật thông tin thành công';
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Đổi mật khẩu
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        // Kiểm tra mật khẩu cũ
        $check_sql = "SELECT passwd FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        if (!$stmt) {
            die("Lỗi chuẩn bị câu truy vấn: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!password_verify($current_password, $user['passwd'])) {
            $errors['current_password'] = "Mật khẩu cũ không đúng.";
            $showPasswordForm = true;
        } else {
            // Nếu không có lỗi, thực hiện thay đổi mật khẩu
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_sql = "UPDATE users SET passwd = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_pass_sql);
            if (!$stmt) {
                die("Lỗi chuẩn bị câu truy vấn: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $id);
            if(mysqli_stmt_execute($stmt)) {
                $successMessage = 'Đổi mật khẩu thành công';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Lấy thông tin người dùng
$id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Lỗi chuẩn bị câu truy vấn: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LetPhich-Tài khoản</title>
    <link rel="stylesheet" href="../../homepage.css" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>

    <style>
    .toast-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
    }
    .toast-success {
        background-color: #28a745;
        color: #fff;
        min-width: 200px;
        padding: 8px 16px;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    .toast-success.show {
        opacity: 1;
    }
    .toast-header, .toast-body {
        font-size: 14px;
    }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <a href="homepage.php" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
            <span class="navbar-text">LetPhich</span>
            <ul class="navbar-nav">
                <li class="nav-item"> <a href="homepage.php" class="nav-link">Trang chủ</a> </li>
                <li class="nav-item"> <a href="logout.php" class="nav-link">Đăng xuất</a> </li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="toast-container"></div>

                <!-- Form cập nhật thông tin -->
                <form action="" method="POST" class="mb-5">
                    <h3 class="mb-4">Cập nhật thông tin cá nhân</h3>
                    <input type="hidden" name="update_info" value="1">
                    
                    <div class="form-group">
                        <label for="fname">Họ tên:</label>
                        <input type="text" class="form-control" id="fname" name="fname" 
                               value="<?= htmlspecialchars($user['name']) ?>">
                        <?php if (isset($errors['fname'])): ?>
                            <small class="text-danger"><?= $errors['fname'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phn">Số điện thoại:</label>
                        <input type="text" class="form-control" id="phn" name="phn" 
                               value="<?= htmlspecialchars($user['phone']) ?>">
                        <?php if (isset($errors['phn'])): ?>
                            <small class="text-danger"><?= $errors['phn'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="dob">Ngày sinh:</label>
                        <input type="date" class="form-control" id="dob" name="dob" 
                               value="<?= htmlspecialchars($user['DOB']) ?>">
                        <?php if (isset($errors['dob'])): ?>
                            <small class="text-danger"><?= $errors['dob'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Số dư: <?= number_format($user['balance'], 0, ',', '.') ?> VNĐ</label>
                    </div>

                    <button type="submit" class="btn btn-success">Cập nhật</button>
                </form>

                <div class="mb-3">
                    <label><b>Email: </b><?= htmlspecialchars($user['email']) ?></label>
                </div>

                <div class="mb-3">
                <button class="btn btn-link p-0" onclick="togglePasswordForm()">Đổi mật khẩu</button>

                </div>

                <!-- Form đổi mật khẩu -->
                <form action="" method="POST" id="changePasswordForm" style="display: none;">
                    <h3 class="mb-4">Đổi mật khẩu</h3>
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại:</label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                        <?php if (isset($errors['current_password'])): ?>
                            <small class="text-danger"><?= $errors['current_password'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" class="form-control" id="new_password" 
                               name="new_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Xác nhận</button>
                    <button type="button" class="btn btn-secondary" onclick="togglePasswordForm()">Hủy</button>
                </form>
            </div>
        </div>
    </div>

    <script>
      // Hàm để bật/tắt hiển thị form đổi mật khẩu
// Hàm hiển thị toast
function showToast(message) {
    // Tạo toast element
    const toastHTML = `
        <div class="toast toast-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    // Thêm toast vào container
    $('.toast-container').append(toastHTML);
    const $toast = $('.toast').last();
    
    // Hiển thị toast với hiệu ứng fade in
    setTimeout(() => {
        $toast.addClass('show');
    }, 100);

    // Sau 2 giây, thêm hiệu ứng fade out và xóa toast
    setTimeout(() => {
        $toast.removeClass('show');
        setTimeout(() => {
            $toast.remove();
        }, 300); // Đợi hiệu ứng fade out hoàn thành
    }, 2000);
}

// Hàm để bật/tắt hiển thị form đổi mật khẩu
function togglePasswordForm() {
    const passwordForm = document.getElementById("changePasswordForm");
    // Nếu form đang ẩn thì hiển thị, nếu đang hiển thị thì ẩn
    passwordForm.style.display = (passwordForm.style.display === "none" || passwordForm.style.display === "") ? "block" : "none";
}

// Hiển thị toast khi có thông báo thành công
<?php if (!empty($successMessage)): ?>
    $(document).ready(function() {
        showToast(<?= json_encode($successMessage) ?>);
    });
<?php endif; ?>

// Hiển thị form mật khẩu nếu có lỗi
<?php if ($showPasswordForm): ?>
    $(document).ready(function() {
        document.getElementById("changePasswordForm").style.display = "block";
    });
<?php endif; ?>

    </script>
</body>
</html>
