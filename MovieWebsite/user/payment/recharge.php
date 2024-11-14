<?php
session_start();
include '../../dbh.php';

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
        $vnp_Returnurl = "http://localhost/BTL/MovieWebsite/user/payment/return_url.php"; // URL để VNPAY gọi lại sau khi thanh toán xong

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
        echo "<p class='text-danger'>Số tiền không hợp lệ. Vui lòng thử lại.</p>";
    }
}
?>

<!DOC<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nạp Tiền - Movie Website</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 2px solid #eee;
            padding: 25px;
        }
        
        .card-header h4 {
            color: #2a5298;
            font-weight: 600;
            margin: 0;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .amount-option {
            display: none;
        }
        
        .amount-label {
            display: block;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .amount-option:checked + .amount-label {
            border-color: #2a5298;
            background-color: #f8f9ff;
        }
        
        .amount-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .amount-value {
            font-size: 1.2em;
            font-weight: 600;
            color: #2a5298;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .payment-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        
        .security-notice {
            font-size: 0.9em;
            color: #666;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><i class="fas fa-wallet mr-2"></i>Nạp Tiền Vào Tài Khoản</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="https://sandbox.vnpayment.vn/paymentv2/Images/brands/logo.svg" alt="VNPAY" height="40" class="mb-3">
                            <p class="text-muted">Chọn số tiền bạn muốn nạp vào tài khoản</p>
                        </div>
                        
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="recharge-form">
                            <div class="form-group">
                                <?php
                                $amounts = [
                                    10000 => '10.000đ',
                                    50000 => '50.000đ',
                                    100000 => '100.000đ',
                                    200000 => '200.000đ',
                                    500000 => '500.000đ'
                                ];
                                
                                foreach ($amounts as $value => $display) {
                                    echo "
                                    <div class='amount-container'>
                                        <input type='radio' name='amount' id='amount_$value' value='$value' class='amount-option' required>
                                        <label for='amount_$value' class='amount-label d-flex justify-content-between align-items-center'>
                                            <span class='amount-value'>$display</span>
                                            <i class='fas fa-check-circle text-success'></i>
                                        </label>
                                    </div>";
                                }
                                ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block submit-btn">
                                <i class="fas fa-lock mr-2"></i>Tiến Hành Thanh Toán
                            </button>
                        </form>
                        
                        <div class="security-notice">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Giao dịch được bảo mật bởi VNPAY
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>