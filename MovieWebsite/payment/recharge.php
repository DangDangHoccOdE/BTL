<?php
session_start();

include '../dbh.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy số tiền người dùng chọn
    $amount = intval($_POST['amount']);

    // Kiểm tra giá trị số tiền có hợp lệ hay không
    $validAmounts = [10000, 50000, 100000, 200000, 500000];
    if (in_array($amount, $validAmounts)) {
        // Thiết lập thông tin VNPAY
        $vnp_TmnCode = "0AG3YTI9"; // Thay bằng mã TMN thực tế của bạn
        $vnp_HashSecret = "6F6BZJ0XHW106LYGULFC5BYC0ZQWOIOK"; // Thay bằng chuỗi bí mật của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; // URL API thanh toán của VNPAY
        $vnp_Returnurl = "http://localhost/BTL/MovieWebsite/payment/return_url.php"; // URL để VNPAY gọi lại sau khi thanh toán xong

        // Thông tin giao dịch
        $vnp_TxnRef = uniqid(); // Mã giao dịch duy nhất
        $vnp_OrderInfo = "Thanh toán VNPAY";
        $vnp_OrderType = "other";
        $vnp_Amount = $amount * 100; // Số tiền nhân với 100 (VNPAY yêu cầu đơn vị là VND x100)
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Tạo mảng dữ liệu cho VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        // Sắp xếp dữ liệu theo key
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo chữ ký
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); // Mã hóa dữ liệu
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng người dùng đến trang thanh toán VNPAY
        header('Location: ' . $vnp_Url);
        exit();
    } else {
        echo "<p>Số tiền không hợp lệ. Vui lòng thử lại.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Xem Phim - Nạp Tiền</title>
    <link rel="stylesheet" href="recharge.css" type="text/css">

</head>
<body>

<div class="recharge-container">
    <h2>Nạp Tiền</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="recharge-form">
        <label for="amount">Chọn số tiền nạp:</label>
        <select name="amount" id="amount" required>
            <option value="10000">10.000đ</option>
            <option value="50000">50.000đ</option>
            <option value="100000">100.000đ</option>
            <option value="200000">200.000đ</option>
            <option value="500000">500.000đ</option>
        </select>
        <button type="submit">Nạp Tiền</button>
    </form>
</div>

</body>
</html>
