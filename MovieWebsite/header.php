<?php
session_start();
include('dbh.php'); // Database connection

// Check if the user is logged in
$user_logged_in = isset($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LetPhich - Trang Chủ</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
     /* Navbar Styling */
     .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white !important;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            margin: 0 10px;
            transition: all 0.3s ease;
            border-radius: 20px;
            padding: 8px 15px !important;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .nav-icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">
        <i class="fas fa-film mr-2"></i>LetPhich
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="">
                    <i class="fas fa-home nav-icon"></i>Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#category-section">
                    <i class="fas fa-th-list nav-icon"></i>Thể Loại
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="account.php">
                    <i class="fas fa-user nav-icon"></i>Tài Khoản
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user/payment/recharge.php">
                    <i class="fas fa-wallet nav-icon"></i>Nạp Tiền
                </a>
            </li>
            <?php if ($user_logged_in): ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt nav-icon"></i>Đăng Xuất
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="user/auth/user-login.php">
                        <i class="fas fa-sign-in-alt nav-icon"></i>Đăng Nhập
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav
