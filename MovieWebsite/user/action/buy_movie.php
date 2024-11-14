<?php
session_start();
include '../../dbh.php'; // Đảm bảo bạn đã kết nối đúng với cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập và có ID phim không
if (isset($_POST['movie_id'])){
    if(!isset($_SESSION['id'])){
        echo json_encode(["status" => false, "message" => "Bạn phải đăng nhập để tiếp tục."]);
    }else{
        $movie_id = $_POST['movie_id'];
        $user_id = $_SESSION['id'];

        // Bảo vệ chống SQL Injection
        $movie_id = mysqli_real_escape_string($conn, $movie_id);
        $user_id = mysqli_real_escape_string($conn, $user_id);

        // Truy vấn thông tin về phim
        $query = "SELECT * FROM movies WHERE mid = $movie_id";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $movie = mysqli_fetch_assoc($result);
            $price = $movie['price'];

            // Truy vấn thông tin về người dùng
            $user_query = "SELECT balance FROM users WHERE id = $user_id";
            $user_result = mysqli_query($conn, $user_query);
            if ($user_result && mysqli_num_rows($user_result) > 0) {
                $user = mysqli_fetch_assoc($user_result);
                $balance = $user['balance'];

                // Kiểm tra xem số dư có đủ để mua phim không
                if ($balance >= $price) {
                    // Giảm số dư tài khoản
                    $new_balance = $balance - $price;
                    $update_balance_query = "UPDATE users SET balance = $new_balance WHERE id = $user_id";
                    if (mysqli_query($conn, $update_balance_query)) {
                        // Thêm thông tin vào bảng purchases
                        $purchase_query = "INSERT INTO purchases (user_id, movie_id) VALUES ($user_id, $movie_id)";
                        if (mysqli_query($conn, $purchase_query)) {
                            echo json_encode(["status" => true, "message" => "Mua phim thành công!"]);
                        } else {
                            echo json_encode(["status" => false, "message" => "Đã xảy ra lỗi khi lưu thông tin mua phim."]);
                        }
                    } else {
                        echo json_encode(["status" => false, "message" => "Đã xảy ra lỗi khi cập nhật số dư."]);
                    }
                } else {
                    echo json_encode(["status" => false, "message" => "Số dư không đủ để mua phim."]);
                }
            } else {
                echo json_encode(["status" => false, "message" => "Không tìm thấy người dùng."]);
            }
        } else {
            echo json_encode(["status" => false, "message" => "Không tìm thấy phim."]);
        }
    }
} else {
    echo json_encode(["status" => false, "message" => "Lỗi, không thể xử lý yêu cầu."]);
}
?>
