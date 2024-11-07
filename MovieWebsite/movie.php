<?php
session_start();
if (isset($_POST['submit'])) {

  $title = $_POST['submit'];

  include 'dbh.php';
  $im = "SELECT * FROM movies WHERE name = '$title'" ;

  $records = mysqli_query($conn, $im);

  echo "<!DOCTYPE html>";
  echo "<html lang='en' dir='ltr'>";
  echo "<head>";
  echo "<meta charset='utf-8'>";
  echo "<title>" . $title . "</title>";
  echo "<link rel='stylesheet' href='movie.css'>";
  echo "<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>";
  echo "</head>";
  echo "<body>";

  echo "<div class='jumbotron-fluid'>";
  echo "<div class='container'>";
  while ($result = mysqli_fetch_assoc($records)) {
      $mname = $result['name'];
      $person = $_SESSION['id'];
      $movieid = $result['mid'];
      $current = $result['viewers'];
      $newcount = $current + 1;
      $newsql = "UPDATE movies SET viewers = '$newcount' WHERE name='$mname'";
      $nsql = "UPDATE users SET mid = '$movieid' WHERE id ='$person'";
      $updatecount = mysqli_query($conn, $newsql);
      $updatecount = mysqli_query($conn, $nsql);

      $description = ucfirst($result['decription']);
      $shortDescription = mb_substr($description, 0, 20) . '...';

      echo "<br>";
      echo "<a href='homepage.php' style='font-size: 15px; color: orange; border: 1px solid orange; border-radius: 5px; padding: 10px; text-decoration: none;'>Về trang chủ</a>";
      echo "<br><br><h5 style='display: inline;'><br>Tên: </h5><h1 style='display: inline;font-size: 20px'>" . ucwords($result['name']) . "</h1>";
      echo "<br><h5 style='display: inline;'>Thể loại : </h5><h4 style='display: inline;font-size: 20px'>" . ucwords($result['genre']) . "</h4>";
      echo "<br><h5 style='display: inline;'>Năm phát hành: </h5><h4 style='display: inline;font-size: 20px'>" . $result['rdate'] . "</h4>";
      echo "<br><h5 style='display: inline;'>Mô tả : </h5>";
      echo "<h4 style='display: inline;font-size: 20px'><span id='shortDesc'>$shortDescription</span><span id='fullDesc' style='display: none;'>$description</span>";
      echo "<a href='javascript:void(0);' id='toggleLink' onclick='toggleDescription()'>Xem thêm</a></h4>";
      echo "<br><h5 style='display: inline;'>Thời lượng: </h5><h4 style='display: inline;font-size: 20px'>" . $result['runtime'] . " phút</h4>";
      echo "<br><h5 style='display: inline;'>Lượt xem: </h5><h4 style='display: inline;font-size: 20px'>" . $result['viewers'] . "</h4>";

      echo "<br><br><br>";
      echo "<div class='embed-responsive embed-responsive-16by9'>";
      echo "<iframe style='display: inline class='embed-responsive-item' src='video-uploads/" . $result['videopath'] . "' poster='uploads/" . $result['imgpath'] . "' frameborder='0' allowfullscreen></iframe>";
      echo "</div>";
  }
  echo "</div>";
  echo "</div>";

  echo "<script>
      function toggleDescription() {
          var shortDesc = document.getElementById('shortDesc');
          var fullDesc = document.getElementById('fullDesc');
          var toggleLink = document.getElementById('toggleLink');

          if (shortDesc.style.display === 'none') {
              shortDesc.style.display = 'inline';
              fullDesc.style.display = 'none';
              toggleLink.innerHTML = 'Xem thêm';
          } else {
              shortDesc.style.display = 'none';
              fullDesc.style.display = 'inline';
              toggleLink.innerHTML = 'Thu gọn';
          }
      }
  </script>";

  echo "</body>";
  echo "</html>";
}
?>
