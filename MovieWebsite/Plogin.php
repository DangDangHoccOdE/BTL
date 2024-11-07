<?php
  session_start();
  include 'dbh.php';




    $username =  $_POST['mail'];
    $password =  $_POST['pass'];



    $sql = "SELECT * FROM users WHERE username = '$username' AND passwd = '$password' ";

    $result = $conn->query($sql);

    if(!$row = $result->fetch_assoc()) {
      echo "Email hoặc mật khẩu không đúng";
    }else {

        $_SESSION['id'] = $row['id'];
        header("Location: homepage.php");
      }

    

?>
