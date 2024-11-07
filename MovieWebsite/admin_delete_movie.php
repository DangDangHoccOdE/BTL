<?php
include 'dbh.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM movies WHERE mid='$id'";
    mysqli_query($conn, $sql);

    header("Location: admin_movies.php");
    exit();
}
?>
