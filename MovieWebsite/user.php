

<?php
  session_start();
  include 'dbh.php';


    $fname = ($_POST['fname']);
    $lname =  ($_POST['lname']);
    $name = $fname." ".$lname;
    $phn =  $_POST['phn'];
    $email =  $_POST['mail'];
    $username =  $_POST['mail'];
    $password =  $_POST['pass'];
    $date = $_POST['date'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $dob = $date."/".$month."/".$year;


    $sql = "INSERT INTO users(username, passwd, name, phone, email, DOB)
    values('$username','$password','$name','$phn','$email','$dob')";
    $result = $conn->query($sql);

    header("Location: user-login.php");
?>
