<?php
session_start();
include '../dbh.php';

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
    $sql = "UPDATE users SET balance = balance + ($amount / 100) WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    // Lưu trạng thái giao dịch vào session để kiểm soát việc hiển thị thông báo
    $_SESSION['payment_status'] = 'success';
    $_SESSION['payment_amount'] = $amount / 100;
    
    // Chuyển hướng sau khi xử lý thành công
    // header("Location: payment_result.php");
    exit();
} else {
    // Giao dịch không thành công
    $_SESSION['payment_status'] = 'failure';
    $_SESSION['payment_error_code'] = $_GET['vnp_ResponseCode'];
    
    // Chuyển hướng sau khi xử lý thất bại
    // header("Location: payment_result.php");
    exit();
}

?>
