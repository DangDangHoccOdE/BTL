<?php
include "../dbh.php";
include "filterRequestAdmin.php";

// Kết nối cơ sở dữ liệu



// Lấy thông tin tổng quan
$sql_total_users = "SELECT COUNT(*) AS total_users FROM users";
$sql_total_movies = "SELECT COUNT(*) AS total_movies FROM movies";
$sql_total_genres = "SELECT COUNT(name) AS total_genres FROM categories";
$sql_total_revenue = "SELECT SUM(amount) AS total_revenue FROM recharge_history";

$result_users = mysqli_query($conn,$sql_total_users);
$result_movies = mysqli_query($conn,$sql_total_movies);
$result_genres = mysqli_query($conn,$sql_total_genres);
$result_revenue = mysqli_query($conn,$sql_total_revenue);

$total_users = $result_users->fetch_assoc()['total_users'];
$total_movies = $result_movies->fetch_assoc()['total_movies'];
$total_genres = $result_genres->fetch_assoc()['total_genres'];
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .sidebar {
      background-color: #343a40;
      color: #fff;
      padding: 20px;
      height: 100vh;
    }

    .sidebar a {
      color: #fff;
      text-decoration: none;
    }

    .sidebar a:hover {
      color: #adb5bd;
    }

    .content {
      padding: 30px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-3 sidebar">
        <h3>Menu</h3>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a href="#" class="nav-link">Dashboard</a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">Manage Users</a>
          </li>
          <li class="nav-item">
            <a href="movies/moviePage.php" class="nav-link">Manage Movies</a>
          </li>
          <li class="nav-item">
            <a href="genre/genrePage.php" class="nav-link">Manage Genres</a>
          </li>
          <li class="nav-item">
            <a href="auth/admin_logout.php" class="nav-link">Logout</a>
          </li>
        </ul>
      </div>
      <div class="col-9 content">
        <h1>Admin Dashboard</h1>
        <div class="row">
          <div class="col-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text"><?php echo $total_users; ?></p>
              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Total Movies</h5>
                <p class="card-text"><?php echo $total_movies; ?></p>
              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Total Genres</h5>
                <p class="card-text"><?php echo $total_genres; ?></p>
              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <p class="card-text"><?php echo $total_revenue; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>