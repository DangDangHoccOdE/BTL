<?php
session_start();
include '../../dbh.php';

$fname = $lname = $phn = $email = ""; 
$errors = []; 
$success_message = ""; // Biến để lưu thông báo thành công

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
    $lname = isset($_POST['lname']) ? trim($_POST['lname']) : '';
    $phn = isset($_POST['phn']) ? trim($_POST['phn']) : '';
    $email = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $password = isset($_POST['pass']) ? $_POST['pass'] : '';
    
    $year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
    $month = isset($_POST['month']) ? (int)$_POST['month'] : 0;
    $day = isset($_POST['date']) ? (int)$_POST['date'] : 0;

    if (empty($fname)) {
        $errors['fname'] = "Họ không được bỏ trống.";
    }
    if (empty($lname)) {
        $errors['lname'] = "Tên không được bỏ trống.";
    }
    if (empty($phn)) {
        $errors['phn'] = "Số điện thoại không được bỏ trống.";
    } elseif (!preg_match('/^0\d{9}$/', $phn)) {
        $errors['phn'] = "Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.";
    }
    if (empty($email)) {
        $errors['mail'] = "Email không được bỏ trống.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['mail'] = "Email không hợp lệ.";
    }
    if (empty($password)) {
        $errors['pass'] = "Mật khẩu không được bỏ trống.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors['pass'] = "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ thường, chữ hoa và số.";
    }
    if ($year == 0 || $month == 0 || $day == 0 || !checkdate($month, $day, $year)) {
        $errors['dob'] = "Ngày sinh không hợp lệ.";
    } else {
        $dob = "$year-$month-$day";
    }

    // Kiểm tra nếu email hoặc số điện thoại đã tồn tại trong cơ sở dữ liệu
    if (empty($errors)) {
        $sql_check = "SELECT * FROM users WHERE email = '$email' OR phone = '$phn'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            $errors['database'] = "Email hoặc số điện thoại đã được sử dụng.";
        }
    }

    if (empty($errors)) {
        $name = $fname . " " . $lname;
        $username = $email;

        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(username, passwd, name, phone, email, DOB, role) 
                VALUES ('$username', '$hashed_password', '$name', '$phn', '$email', '$dob', 'user')";
        
        if ($conn->query($sql) === TRUE) {
            // Cập nhật thông báo thành công
            $success_message = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
        } else {
            $errors['database'] = "Lỗi khi đăng ký: " . $conn->error;
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    }
} else {
    if (isset($_SESSION['form_data'])) {
        $fname = $_SESSION['form_data']['fname'];
        $lname = $_SESSION['form_data']['lname'];
        $phn = $_SESSION['form_data']['phn'];
        $email = $_SESSION['form_data']['mail'];
        unset($_SESSION['form_data']);
    }
    if (isset($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng ký</title>
  <link rel="stylesheet" href="user.css" type="text/css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
  <header>
    <div class="container-fluid">
      <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a href="login.php" class="navbar-brand"><img src="images/logo.png" alt="Logo"></a>
        <span class="navbar-text">LetPhich</span>
        <ul class="navbar-nav">
          <li class="nav-item"><a href="user-login.php" class="nav-link">Đăng nhập</a></li>
        </ul>
      </nav>

      <div class="container">
        <div class="jumbotron">
          <h1>Đăng ký</h1>

          <!-- Thông báo thành công -->
          <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
          <?php endif; ?>

          <!-- Thông báo lỗi khi tài khoản đã tồn tại -->
          <?php if (isset($errors['database'])): ?>
            <div class="alert alert-danger"><?php echo $errors['database']; ?></div>
          <?php endif; ?>

          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="row">
              <div class="col">
                <input type="text" class="form-control" placeholder="Họ" name="fname" value="<?php echo htmlspecialchars($fname); ?>" >
                <?php if (isset($errors['fname'])): ?>
                  <small class="text-danger"><?php echo $errors['fname']; ?></small>
                <?php endif; ?>
              </div>
              <div class="col">
                <input type="text" class="form-control" placeholder="Tên" name="lname" value="<?php echo htmlspecialchars($lname); ?>" >
                <?php if (isset($errors['lname'])): ?>
                  <small class="text-danger"><?php echo $errors['lname']; ?></small>
                <?php endif; ?>
              </div>
            </div><br>
            
            <input type="text" class="form-control" placeholder="Số điện thoại" name="phn" maxlength="15" value="<?php echo htmlspecialchars($phn); ?>" >
            <?php if (isset($errors['phn'])): ?>
              <small class="text-danger"><?php echo $errors['phn']; ?></small>
            <?php endif; ?>
            <br>
            
            <input type="email" class="form-control" placeholder="Email" name="mail" maxlength="50" value="<?php echo htmlspecialchars($email); ?>" >
            <?php if (isset($errors['mail'])): ?>
              <small class="text-danger"><?php echo $errors['mail']; ?></small>
            <?php endif; ?>
            <br>
            
            <input type="password" class="form-control" placeholder="Mật khẩu" name="pass" maxlength="60" >
            <?php if (isset($errors['pass'])): ?>
              <small class="text-danger"><?php echo $errors['pass']; ?></small>
            <?php endif; ?>
            <br>

            <div class="form-group">
              <label for="dob"><br>Ngày sinh</label>
              <div class="row">
                <div class="col">
                  <select class="form-control" name="date" >
                    <option value="">Ngày...</option>
                    <?php for ($i = 1; $i <= 31; $i++) { echo "<option value='$i' " . ($i == $day ? "selected" : "") . ">$i</option>"; } ?>
                  </select>
                </div>
                <div class="col">
                  <select class="form-control" name="month" >
                    <option value="">Tháng...</option>
                    <?php for ($i = 1; $i <= 12; $i++) { echo "<option value='$i' " . ($i == $month ? "selected" : "") . ">$i</option>"; } ?>
                  </select>
                </div>
                <div class="col">
                  <select class="form-control" name="year" >
                    <option value="">Năm...</option>
                    <?php for ($i = 1900; $i <= date("Y"); $i++) { echo "<option value='$i' " . ($i == $year ? "selected" : "") . ">$i</option>"; } ?>
                  </select>
                </div>
              </div>
            </div>
            <?php if (isset($errors['dob'])): ?>
              <small class="text-danger"><?php echo $errors['dob']; ?></small>
            <?php endif; ?>
            <br>

            <button type="submit" class="btn btn-primary">Đăng ký</button>
          </form>
        </div>
      </div>
    </div>
  </header>
</body>
</html>
