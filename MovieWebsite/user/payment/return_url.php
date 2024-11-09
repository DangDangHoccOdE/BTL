<?php
session_start();
include '../../dbh.php';

$vnp_HashSecret = "6F6BZJ0XHW106LYGULFC5BYC0ZQWOIOK"; // Chuỗi bí mật đã nhận từ VNPAY

// Lấy dữ liệu từ URL trả về của VNPAY
$vnp_SecureHash = $_GET['vnp_SecureHash'];
$amount = $_GET['vnp_Amount'];
$inputData = array();

foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = "";

foreach ($inputData as $key => $value) {
    $hashData .= urlencode($key) . '=' . urlencode($value) . '&';
}
$hashData = rtrim($hashData, '&');

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

if ($secureHash === $vnp_SecureHash && $_GET['vnp_ResponseCode'] == '00') {
    // Giao dịch thành công
    $id = $_SESSION['id'];
    $rechargeAmount = $amount / 100; // Số tiền nạp sau khi chia cho 100

    // Cập nhật số dư tài khoản và trạng thái của người dùng
    $sql_update_balance = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_update_balance);
    mysqli_stmt_bind_param($stmt, "di", $rechargeAmount, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Cập nhật trạng thái lịch sử nạp tiền
    $status = "Thành công";
    $sql_insert_history = "INSERT INTO recharge_history (user_id, amount, status) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql_insert_history);
    mysqli_stmt_bind_param($stmt, "ids", $id, $rechargeAmount, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Lưu trạng thái giao dịch vào session để kiểm soát việc hiển thị thông báo
    $_SESSION['payment_status'] = 'success';
    $_SESSION['payment_amount'] = $rechargeAmount;
    
    // Chuyển hướng sau khi xử lý thành công
    header("Location: payment_result.php");
    exit();
} else {
    // Giao dịch không thành công
    $id = $_SESSION['id'];
    $rechargeAmount = $amount / 100; // Số tiền nạp sau khi chia cho 100
    $status = "Thất bại"; // Trạng thái thất bại

    // Cập nhật lịch sử nạp tiền thất bại
    $sql_insert_history = "INSERT INTO recharge_history (user_id, amount, status) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql_insert_history);
    mysqli_stmt_bind_param($stmt, "ids", $id, $rechargeAmount, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Lưu trạng thái giao dịch vào session
    $_SESSION['payment_status'] = 'failure';
    $_SESSION['payment_error_code'] = $_GET['vnp_ResponseCode'];
    
    // Chuyển hướng sau khi xử lý thất bại
    header("Location: payment_result.php");
    exit();
}


?>
