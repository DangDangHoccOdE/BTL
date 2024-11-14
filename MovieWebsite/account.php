<?php
session_start();
include 'dbh.php'; // Đảm bảo đường dẫn đúng, nếu cần thì đổi thành '../dbh.php' tùy thuộc vào vị trí thư mục

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Chuyển hướng nếu chưa đăng nhập
    exit();
}

$userId = $_SESSION['id'];

// Truy vấn thông tin tài khoản
$userQuery = "SELECT * FROM users WHERE id = $userId";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change-password'])) {
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current-password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new-password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm-password']);
    
    // Kiểm tra mật khẩu hiện tại có đúng không
    if (password_verify($currentPassword, $user['password'])) {
        // Kiểm tra mật khẩu mới và mật khẩu xác nhận có khớp không
        if ($newPassword === $confirmPassword) {
            // Mã hóa mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Cập nhật mật khẩu mới vào cơ sở dữ liệu
            $updatePasswordQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = $userId";
            if (mysqli_query($conn, $updatePasswordQuery)) {
                $successMessage = "Mật khẩu đã được thay đổi thành công!";
            } else {
                $errorMessage = "Đã xảy ra lỗi khi thay đổi mật khẩu.";
            }
        } else {
            $errorMessage = "Mật khẩu mới và mật khẩu xác nhận không khớp.";
        }
    } else {
        $errorMessage = "Mật khẩu hiện tại không chính xác.";
    }
}

// Truy vấn phim đã mua
$userId = $_SESSION['id'];  // Lấy ID người dùng từ session

// Truy vấn thông tin các phim đã mua của người dùng
$purchasedMoviesQuery = "SELECT movies.name, movies.rdate, movies.runtime, movies.price, purchases.purchase_time 
                         FROM movies
                         JOIN purchases ON movies.mid = purchases.movie_id
                         WHERE purchases.user_id = ?";

$stmt = mysqli_prepare($conn, $purchasedMoviesQuery);

if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($conn));  // Kiểm tra lỗi khi chuẩn bị câu lệnh
}

// Liên kết tham số và thực thi truy vấn
mysqli_stmt_bind_param($stmt, 'i', $userId);  // Liên kết tham số user_id
mysqli_stmt_execute($stmt);

// Lấy kết quả
$purchasedMoviesResult = mysqli_stmt_get_result($stmt);

// Truy vấn lịch sử giao dịch
$transactionsQuery = "SELECT * FROM recharge_history WHERE user_id = $userId";
$transactionsResult = mysqli_query($conn, $transactionsQuery);

// Truy vấn danh sách yêu thích
$favoritesQuery = "SELECT movies.* FROM movies 
                   JOIN favorite_movies ON movies.mid = favorite_movies.movie_id
                   WHERE favorite_movies.user_id = $userId";
$favoritesResult = mysqli_query($conn, $favoritesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <style>
        .navbar { margin-bottom: 20px; }
        .nav-tabs {
            display: block;
        }
        .nav-item {
            width: 100%;
        }
        .nav-link {
            display: block;
            padding: 10px 15px;
            text-align: left;
        }
        .tab-content {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .nav-item + .nav-item {
            margin-top: 10px;
        }
        .status-circle {
            display: inline-block;
            padding: 3px 10px;
            background-color: #28a745;
            color: white;
            border-radius: 50%;
            font-weight: bold;
            font-size: 0.8rem;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a href="index.php" class="navbar-brand">
            <img src="images/logo.png" alt="Logo" style="width: 40px; height: auto;">
        </a>
        <span class="navbar-text">LetPhich</span>
    </nav>

    <div class="container">
        <h1 class="my-4">Thông tin tài khoản</h1>
        <div class="row">
            <div class="col-md-3">
                <ul class="nav nav-tabs flex-column" id="accountTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="account-info-tab" data-toggle="tab" href="#account-info" role="tab">Thông tin cá nhân</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="purchased-movies-tab" data-toggle="tab" href="#purchased-movies" role="tab">Phim đã mua</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="transaction-history-tab" data-toggle="tab" href="#transaction-history" role="tab">Lịch sử giao dịch</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="favorites-tab" data-toggle="tab" href="#favorites" role="tab">Yêu thích</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="change-password-tab" data-toggle="tab" href="#change-password" role="tab">Đổi mật khẩu</a>
                    </li>
                </ul>
            </div>

            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Thông tin cá nhân -->
                    <div class="tab-pane fade show active" id="account-info" role="tabpanel">
                        <h3>Thông tin cá nhân</h3>
                        <table class="table table-bordered">
                            <tr><td><strong>Tên:</strong></td><td><?php echo htmlspecialchars($user['name']); ?></td></tr>
                            <tr><td><strong>Số điện thoại:</strong></td><td><?php echo htmlspecialchars($user['phone']); ?></td></tr>
                            <tr><td><strong>Email:</strong></td><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
                            <tr><td><strong>Ngày sinh:</strong></td><td><?php echo htmlspecialchars($user['DOB']); ?></td></tr>
                            <tr><td><strong>Số dư:</strong></td><td><?php echo number_format($user['balance'], 2); ?> VND</td></tr>
                        </table>
                    </div>

                    <!-- Phim đã mua -->
                    <div class="tab-pane fade" id="purchased-movies" role="tabpanel">
                        <h3>Danh sách phim đã mua</h3>
                        <?php if (mysqli_num_rows($purchasedMoviesResult) > 0): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tên Phim</th>
                                        <th>Ngày phát hành</th>
                                        <th>Thời gian chạy</th>
                                        <th>Giá</th>
                                        <th>Ngày Mua</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($movie = mysqli_fetch_assoc($purchasedMoviesResult)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($movie['name']); ?></td>
                                            <td><?php echo htmlspecialchars($movie['rdate']); ?></td>
                                            <td><?php echo htmlspecialchars($movie['runtime']); ?></td>
                                            <td><?php echo number_format($movie['price'], 2) . ' VND'; ?></td>
                                            <td><?php echo htmlspecialchars($movie['purchase_time']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Bạn chưa mua phim nào.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Lịch sử giao dịch -->
                    <div class="tab-pane fade" id="transaction-history" role="tabpanel">
                        <h3>Lịch sử giao dịch</h3>
                        <?php if (mysqli_num_rows($transactionsResult) > 0): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Trạng thái</th><th>Số tiền</th><th>Thời gian</th></tr>
                                </thead>
                                <tbody>
                                    <?php while ($transaction = mysqli_fetch_assoc($transactionsResult)): ?>
                                        <tr>
                                            <td>
                                                <?php if ($transaction['status'] == 'Thành công') {
                                                    echo '<span class="status-circle">' . htmlspecialchars($transaction['status']) . '</span>';
                                                } else {
                                                    echo htmlspecialchars($transaction['status']);
                                                } ?>
                                            </td>
                                            <td><?php echo number_format($transaction['amount'], 2) . ' VND'; ?></td>
                                            <td><?php echo htmlspecialchars($transaction['recharge_time']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Không có lịch sử giao dịch.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Danh sách yêu thích -->
                    <div class="tab-pane fade" id="favorites" role="tabpanel">
                        <h3>Danh sách yêu thích</h3>
                        <?php if (mysqli_num_rows($favoritesResult) > 0): ?>
                            <table class="table table-bordered">
                                <tr><th>Tên Phim</th></tr>
                                <?php while ($favorite = mysqli_fetch_assoc($favoritesResult)): ?>
                                    <tr><td><?php echo htmlspecialchars($favorite['name']); ?></td></tr>
                                <?php endwhile; ?>
                            </table>
                        <?php else: ?>
                            <p>Bạn chưa có phim yêu thích nào.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Đổi mật khẩu -->
                    <div class="tab-pane fade" id="change-password" role="tabpanel">
                        <h3>Đổi mật khẩu</h3>
                        <!-- Hiển thị thông báo thành công hoặc lỗi -->
                        <?php if (isset($successMessage)): ?>
                            <div class="alert alert-success"><?php echo $successMessage; ?></div>
                        <?php endif; ?>
                        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="current-password">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" id="current-password" name="current-password" required>
                            </div>
                            <div class="form-group">
                                <label for="new-password">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="new-password" name="new-password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm-password">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                            </div>
                            <button type="submit" name="change-password" class="btn btn-primary">Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thêm jQuery và Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
