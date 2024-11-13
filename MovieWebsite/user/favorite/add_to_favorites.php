<?php
session_start();
include '../../dbh.php';

$response = ["status" => false, "message" => ""];

if (isset($_POST['movie_id']) && isset($_SESSION['id'])) {
    $movie_id = $_POST['movie_id'];
    $user_id = $_SESSION['id'];

    // Kiểm tra xem phim đã tồn tại trong danh sách yêu thích chưa
    $checkQuery = "SELECT * FROM favorite_movies WHERE user_id = '$user_id' AND movie_id = '$movie_id'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) == 0) {
        // Nếu chưa có, thêm phim vào danh sách yêu thích
        $insertQuery = "INSERT INTO favorite_movies (user_id, movie_id) VALUES ('$user_id', '$movie_id')";
        if (mysqli_query($conn, $insertQuery)) {
            $response["status"] = true;
            $response["message"] = "Phim đã được thêm vào danh sách yêu thích!";
        } else {
            $response["message"] = "Có lỗi xảy ra khi thêm vào danh sách yêu thích.";
        }
    } else {
        $response["message"] = "Phim đã có trong danh sách yêu thích!";
    }
} else {
    $response["message"] = "Yêu cầu không hợp lệ.";
}

// Trả về phản hồi JSON
echo json_encode($response);
?>
