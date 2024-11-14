<?php
  session_start();
  include '../../dbh.php';

  // Khởi tạo thông báo lỗi và biến giữ lại email
  $error_message = '';
  $username = '';

  // Kiểm tra nếu form đã được gửi
  if (isset($_POST['mail']) && isset($_POST['pass'])) {
      $username = $_POST['mail'];
      $password = $_POST['pass'];

      // Cẩn thận với SQL Injection: sử dụng prepared statements
      $sql = "SELECT * FROM users WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $username); // "s" biểu thị chuỗi
      $stmt->execute();
      $result = $stmt->get_result();

      if ($row = $result->fetch_assoc()) {
          // Kiểm tra mật khẩu đã mã hóa
          if (password_verify($password, $row['passwd'])) {
            echo "Đăng nhập thành công!";
              // Mật khẩu đúng, lưu id vào session và chuyển hướng
              $_SESSION['id'] = $row['id'];
              $_SESSION['role'] = $row['role'];
              header("Location: ../../homepage.php");
              exit(); // Dừng thực thi sau khi chuyển hướng
          } else {
              // Nếu mật khẩu không đúng
              $error_message = "Email hoặc mật khẩu không đúng";
          }
      } else {
          // Nếu không tìm thấy email
          $error_message = "Email hoặc mật khẩu không đúng";
      }
  }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>LetPhich-Đăng nhập</title>
    <link rel="stylesheet" href="user-login.css" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  </head>
  <body>
    <header>
      <div class="container-fluid">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <a href="login.php" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
            <span class="navbar-text">LetPhich</span>
            <ul class="navbar-nav">
              <li class="nav-item"> <a href="register.php" class="nav-link"> Đăng ký</a> </li>
            </ul>
        </nav>

        <div class="container">
          <div class="jumbotron">
            <h1>Đăng nhập vào tài khoản của bạn</h1> <br>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
              <!-- Giữ lại giá trị của email khi có lỗi -->
              <input type="email" class="form-control" placeholder="Email" name="mail" value="<?php echo htmlspecialchars($username); ?>">
              <br>
              <input type="password" class="form-control" placeholder="Mật khẩu" name="pass" value="">
              <br><br>
              
              <?php if ($error_message): ?>
                <div style="color: red;"><?= $error_message ?></div>
              <?php endif; ?>

              <div class="loginbutton">
                <button type="submit" class="btn btn-success btn-lg" name="login">Đăng nhập</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </header>

    <footer class="page-footer font-small blue">
      <div class="footer-copyright text-center py-3">© 2024 Copyright:
        <a href="">anthony@gmail.com</a>
      </div>
    </footer>
  </body>
</html>
