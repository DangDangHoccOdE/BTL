<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>LetPhich - Homepage</title>
  <link rel="stylesheet" href="homepage.css" type="text/css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <a href="#" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
      <span class="navbar-text">LetPhich</span>
      <ul class="navbar-nav">
        <?php
        session_start();
        if (isset($_SESSION['id'])) {
          if ($_SESSION['id'] == 1) {
            echo "<li class='nav-item'> <a href='admin.php' class='nav-link'>Thêm phim</a> </li>";
          }
          echo "<li class='nav-item'><a href='account.php' class='nav-link'>Tài khoản</a></li>";
          echo "<li class='nav-item'><a href='favorite_movies.php' class='nav-link'>Yêu thích</a></li>";
          echo "<li class='nav-item'><a href='user/payment/history_recharge.php' class='nav-link'>Lịch sử nạp tiền</a></li>";
          echo "<li class='nav-item'><a href='user/payment/recharge.php' class='nav-link'>Nạp tiền</a></li>";
          echo "<li class='nav-item'><a href='logout.php' class='nav-link'>Đăng xuất</a></li>";
        } else {
          echo "<li class='nav-item'><a href='user/auth/login.php' class='nav-link'>Đăng nhập</a></li>";
        }
        ?>
      </ul>
    </nav>
  </header>

  <section>
    <div class="container">
      <!-- Thông báo Yêu thích -->
      <div id="favorite-message" class="alert alert-success" style="display: none;"></div>

      <!-- Thông báo Mua phim -->
      <div id="movie-message" class="alert alert-success" style="display: none;"></div>

      <div class="jumbotron">
        <h2>Tất cả phim</h2>
        <div class="row">
          <?php
          include 'dbh.php';
          $query = "SELECT * FROM movies ORDER BY name ASC";
          $records = mysqli_query($conn, $query);

          while ($movie = mysqli_fetch_assoc($records)) {
              echo "<div class='col-md-3'>";
              echo "<img src='uploads/" . $movie['imgpath'] . "' height='250' width='200' class='img-fluid'/>";
              echo "<div class='text-center mt-2'>";
              echo "<h5><a href='movie_preview.php?movie_id=" . $movie['mid'] . "'>" . ucwords($movie['name']) . "</a></h5>";
              echo "<p>Giá: " . number_format($movie['price'], 2) . " VND</p>";
              echo "<button class='btn btn-outline-danger favorite-btn' data-movie-id='" . $movie['mid'] . "'><i class='fas fa-heart'></i> Yêu thích</button>";
              echo "<button class='btn btn-outline-primary buy-movie-btn' data-movie-id='" . $movie['mid'] . "'><i class='fas fa-ticket-alt'></i> Mua phim</button>";
              echo "</div>";
              echo "</div>";
          }
          ?>
        </div>
      </div>
    </div>
  </section>

  <script>
    $(document).ready(function() {
      // Xử lý sự kiện yêu thích phim
      $(".favorite-btn").click(function(e) {
        e.preventDefault();
        const movieId = $(this).data("movie-id");
        
        $.ajax({
          url: "user/favorite/add_to_favorites.php",
          type: "POST",
          data: { movie_id: movieId },
          success: function(response) {
            const data = JSON.parse(response);
            if (data.status) {
              $("#favorite-message").text(data.message).show().delay(3000).fadeOut();
            } else {
              $("#favorite-message").text(data.message).addClass("alert-danger").show().delay(3000).fadeOut(function() {
                $(this).removeClass("alert-danger").addClass("alert-success");
              });
            }
          }
        });
      });

      // Xử lý sự kiện mua phim
      $(".buy-movie-btn").click(function(e) {
        e.preventDefault();
        const movieId = $(this).data("movie-id");

        $.ajax({
          url: "user/action/buy_movie.php",
          type: "POST",
          data: { movie_id: movieId },
          success: function(response) {
            console.log(response);
            const data = JSON.parse(response);
            if (data.status) {
              $("#movie-message").text(data.message).removeClass("alert-danger").addClass("alert-success").show().delay(3000).fadeOut();
            } else {
              $("#movie-message").text(data.message).removeClass("alert-success").addClass("alert-danger").show().delay(3000).fadeOut();
            }
          }
        });
      });
    });
  </script>
</body>
</html>
