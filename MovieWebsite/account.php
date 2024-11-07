<?php
session_start();

 ?>
 <!DOCTYPE html>
 <html lang="en" dir="ltr">
 <head>
   <meta charset="utf-8">
   <title>LetPhich-Tài khoản</title>
   <link rel="stylesheet" href="homepage.css" type="text/css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
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
        <?php
              include 'dbh.php';
              $id = $_SESSION['id'];
              $sql = "SELECT * FROM users WHERE id = $id ";
              $newrecords = mysqli_query($conn,$sql);
              $result = mysqli_fetch_assoc($newrecords);

      echo"  <form  action='update.php' method='POST'>

          <br><br><input type='text' class='form-control' placeholder='Họ tên' name='fname' value='".ucwords($result['name'])."'>
          <br>
          <input type='text' class='form-control' placeholder='Nhập số điện thoại' name='phn' value='".$result['phone']."'>
          <br>
          <label><b>Ngày sinh: </b></label>
          <input type='text' class='from-control' placeholder='Nhập ngày sinh' name='dob' value='".$result['DOB']."'><br>
           <br>
          <label><b>Số dư: </b>".$result['balance']."</label>

              <div class='signupbutton'>
                <br><br>
                <button type='submit' class='btn btn-success' name='sub' value='submit'>Cập nhật</button>
              </div>
              </form>


              <br><br>
              <label><b>Email: </b>".$result['email']."</label>
              <br><br>
              <a href='accountp.php'>Đổi mật khẩu</a>



              ";
         ?>




      </div>

    </body>

  </html>
