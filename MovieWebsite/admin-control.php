<?php
session_start();
if (isset($_POST['upload'])) {

  include 'dbh.php';

  $targetvid = "video-uploads/".basename($_FILES['video']['name']);
  $target = "uploads/".basename($_FILES['image']['name']);
  $name = ($_POST['mname']);
  $rdate = $_POST['release'];
  $genre = ($_POST['genre']);
  $rtime = $_POST['rtime'];
  $desc = $_POST['desc'];
  $image = $_FILES['image']['name'];
  $video = $_FILES['video']['name'];
  $price = $_POST['price'];

  $sql = "INSERT INTO movies (name, rdate, genre, runtime, decription, imgpath, videopath, price)
    VALUES('$name','$rdate','$genre','$rtime','$desc','$image','$video','$price')";

  mysqli_query($conn,$sql);

  if (move_uploaded_file($_FILES['image']['tmp_name'],$target) && move_uploaded_file($_FILES['video']['tmp_name'],$targetvid)) {
    header("Location: homepage.php");
  }else {
    echo "error uploading";
  }
}


?>
