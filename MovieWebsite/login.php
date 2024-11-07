<?php
session_start();

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>LetPhich</title>
    <link rel="stylesheet" href="master.css" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  </head>
  <body>
    <header>
      <div class="container-fluid">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark ">
            <a href="login.php" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
            <span class="navbar-text">LetPhich</span>

            <ul class="navbar-nav">
              <li class="nav-item"> <a href="#A" class="nav-link"> Dịch vụ</a> </li>
              <li class="nav-item"> <a href="user-login.php" class="nav-link"> Đăng nhập</a> </li>

            </ul>

        </nav>

        <div class="container">
          <div class="jumbotron">
            <h1>Xem mọi lúc, <br> Xem mọi nơi... </h1> <br>
            <a href="test.php" type="button" class="btn btn-danger btn-block">Đăng ký ngay</a>
          </div>
        </div>
      </div>

      </header>



    <section class="features">
        <a href="#" name="A"></a>
        <h2>Dịch vụ của chúng tôi</h2>

        <div class="container">
          <div class="row">
            <div class="col-md-4">
              <p class="arrange"><img src="images/mob.png" alt=""> <br> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
              </p>
            </div><div class="col-md-4">
              <p class="arrange"><img src="images/mess.png" alt=""> <br> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
              </p>
            </div>
              <div class="col-md-4">

                <p class="arrange">
                  <img src="images/desktop.jpg"> <br>   Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
              </div>

            </div>

          </div>
          <h3></h3>

    </section>
    <footer class="page-footer font-small blue">

      <div class="footer-copyright text-center py-3">© 2024 Copyright:
        <a href="">anthony@gmail.com</a>
      </div>

    </footer>
  </body>
</html>
