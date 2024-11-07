<?php
session_start();
// Kiểm tra xem người dùng đã đăng nhập với vai trò admin chưa
if (!isset($_SESSION['admin_logged_in'])) {
     header("Location: admin_login.php");
     exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
     <meta charset="UTF-8">
     <title>Trang Chủ Admin</title>
     <link rel="stylesheet" href="user.css" type="text/css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
     <div class="container-fluid">
          <nav class="navbar navbar-expand-md navbar-dark bg-dark">
               <a href="homepage.php" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
               <span class="navbar-text">LetPhich</span>

               <ul class="navbar-nav">
                    <li class="nav-item"> <a href="homepage.php" class="nav-link"> Trang chủ </a> </li>
                    <li class="nav-item"> <a href="logout.php" class="nav-link"> Đăng xuất</a> </li>

               </ul>

          </nav>
          <div class="container">

               <div class="list-group mt-4">
                    <a href="admin_movies.php" class="list-group-item list-group-item-action">Quản Lý Phim</a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action">Quản Lý Người Dùng</a>
                    <a href="admin_genre.php" class="list-group-item list-group-item-action">Quản Lý Thể Loại</a>
               </div>
          </div>
     </div>
</body>

</html>