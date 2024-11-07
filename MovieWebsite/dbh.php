<?php
$conn = mysqli_connect("localhost", "root", "Dang972004@", "letphichmovie");


if (!$conn) {
    die('Could not connect: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
