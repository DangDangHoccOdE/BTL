<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
     <meta charset="utf-8">
     <title>LetPhich-Admin Home Page</title>
     <link rel="stylesheet" href="homepage.css" type="text/css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
     <header>

          <nav class="navbar navbar-expand-md navbar-dark bg-dark">
               <a href="homepage.php" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
               <span class="navbar-text">Admin</span>

               <ul class="navbar-nav">

                    <li class="nav-item"> <a href="homepage.php" class="nav-link"></a> </li>

                    <li class="nav-item"> <a href="logout.php" class="nav-link">Đăng xuất</a> </li>
               </ul>


          </nav>

     </header>

    
</body>

</html>