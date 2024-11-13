<?php
session_start();
include('dbh.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
$user_logged_in = isset($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LetPhich - Trang Chủ</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .movie-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Thanh điều hướng -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a href="#" class="navbar-brand">
        <img src="images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-top">
    </a>
    <span class="navbar-text">LetPhich</span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <!-- Kiểm tra xem người dùng đã đăng nhập chưa -->
            <?php if ($user_logged_in): ?>
                <li class="nav-item active">
                    <a class="nav-link" href="account.php">Trang Cá Nhân</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Đăng Xuất</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="user/auth/user-login.php">Đăng Nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user/auth/register.php">Đăng Ký</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Phần chính -->
<div class="container mt-5">
    <div class="row">
        <!-- Cột trái (Danh sách phim) -->
        <div class="col-md-12">
            <h2>Danh Sách Phim Đang Bán</h2>
            <div class="row">
                <!-- Lấy danh sách phim từ cơ sở dữ liệu -->
                <?php
                $query_movies = "SELECT * FROM movies WHERE price > 0 LIMIT 6"; // Lấy 6 phim có giá (đang bán)
                $result_movies = $conn->query($query_movies);

                if ($result_movies->num_rows > 0):
                    while ($movie = $result_movies->fetch_assoc()):
                ?>
                        <div class="col-md-4 movie-card">
                            <div class="card">
                                <img src="images/<?php echo htmlspecialchars($movie['imgpath']); ?>" class="card-img-top" alt="Movie Image">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($movie['name']); ?></h5>
                                    <p class="card-text">Giá: <?php echo number_format($movie['price'], 0, ',', '.'); ?> VND</p>
                                    <a href="movie_preview.php?movie_id=<?php echo $movie['mid']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p>Không có phim nào để hiển thị.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Thêm Bootstrap JS và jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
