<?php
session_start();

?>


<!DOCTYPE html>

<head>
  <meta charset="utf-8">
  <title>LetPhich-Admin</title>
  <link rel="stylesheet" href="user.css" type="text/css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
  <header>
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

        <div class="jumbotron">
          <h1> Nhập thông tin phim</h1>
          <p> <b></b> </p> <br>

          <form class="" action="admin-control.php" method="POST" enctype="multipart/form-data">

            <input type="text" class="form-control" placeholder="Tên" name="mname" value=""><br>
            <input type="text" class="form-control" placeholder="Năm phát hành" name="release" value="">
            <br>
            <input type="text" class="form-control" placeholder="Thể loại" name="genre" value="">
            <br>
            <input type="number" class="form-control" placeholder="Thời lượng (phút)" name="rtime" value="">
            <br>
            <input type="text" class="form-control" placeholder="Mô tả..." name="desc" value="">
            <br>
            <div class="row">
              <div class="col">
                <table>
                  <tr>
                    <td> <label for=""><b>Tải lên Poster : </b></label> </td>
                    <td>
                      <div class="">
                        <input type="hidden" name="size" value="100000">

                        <input type="file" name="image" value="">
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="col">
                <table>
                  <tr>
                    <td> <label for=""><b>Tải lên Phim : </b></label> </td>
                    <td>
                      <div class="">
                        <input type="hidden" name="size" value="30000000">

                        <input type="file" name="video" value="">
                      </div>
                    </td>
                  </tr>
                </table>

              </div>
            </div> <br><br>
            <div class="signupbutton">
              <input type="submit" class="btn btn-success btn-lg" name="upload" value="Submit">
            </div>


          </form>

        </div>


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