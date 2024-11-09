<?php
session_start();
if (isset($_SESSION['payment_status'])) {
    $status = $_SESSION['payment_status'];
    if ($status == 'success') {
        $amount = number_format($_SESSION['payment_amount']);
        echo "
        <div class='payment-container success'>
            <h1>Thanh toán thành công!</h1>
            <p>Cảm ơn quý khách đã thanh toán. Số tiền {$amount} VND đã được thêm vào tài khoản của quý khách.</p>
            <a href='recharge.php' class='btn'>Trở về trang xem phim</a>
        </div>";
    } else {
        $errorCode = $_SESSION['payment_error_code'];
        echo "
        <div class='payment-container failure'>
            <h1>Thanh toán không thành công</h1>
            <p>Giao dịch của bạn không thành công. Mã lỗi: {$errorCode}</p>
            <a href='recharge.php' class='btn'>Thử lại nạp tiền</a>
        </div>";
    }

    // Xóa trạng thái thanh toán để tránh hiển thị lại khi tải lại trang
    unset($_SESSION['payment_status']);
    unset($_SESSION['payment_amount']);
    unset($_SESSION['payment_error_code']);
} else {
    header("Location: recharge.php");
    exit();
}
?>
