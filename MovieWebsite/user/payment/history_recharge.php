<?php
include '../../dbh.php'; // Kết nối với cơ sở dữ liệu
session_start();

// Xử lý lọc
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$filter_amount = isset($_GET['filter_amount']) ? $_GET['filter_amount'] : '';

// Lấy user_id từ session
$userId = $_SESSION['id'];

// Lấy danh sách lịch sử nạp tiền và phân trang
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Xây dựng câu truy vấn SQL với điều kiện lọc
$sql = "SELECT * FROM recharge_history WHERE user_id = ?";
$params = [$userId];  // Đưa userId vào mảng tham số

if ($filter_date) {
    $sql .= " AND DATE(recharge_time) = ?";
    $params[] = $filter_date;
}

if ($filter_amount) {
    $sql .= " AND amount >= ?";
    $params[] = $filter_amount;
}

$sql .= " ORDER BY recharge_time DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Chuẩn bị câu truy vấn
$stmt = mysqli_prepare($conn, $sql);

// Gán tham số cho câu truy vấn
mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);

// Thực thi câu truy vấn
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Tính tổng số bản ghi
$total_sql = "SELECT COUNT(*) AS total FROM recharge_history WHERE user_id = ?";
$total_params = [$userId];  // Đưa userId vào mảng tham số của truy vấn tổng

if ($filter_date) {
    $total_sql .= " AND DATE(recharge_time) = ?";
    $total_params[] = $filter_date;
}

if ($filter_amount) {
    $total_sql .= " AND amount >= ?";
    $total_params[] = $filter_amount;
}

$total_stmt = mysqli_prepare($conn, $total_sql);

// Gán tham số cho câu truy vấn tổng
mysqli_stmt_bind_param($total_stmt, str_repeat("s", count($total_params)), ...$total_params);

mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $limit);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recharge History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1>Recharge History</h1>

        <!-- Lọc theo ngày tháng và số tiền -->
        <form method="get" action="">
            <div class="row mb-3">
                <div class="col">
                    <input type="date" class="form-control" name="filter_date" value="<?php echo $filter_date; ?>">
                </div>
                <div class="col">
                    <input type="number" class="form-control" name="filter_amount" value="<?php echo $filter_amount; ?>" placeholder="Min amount">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Hiển thị bảng lịch sử nạp tiền -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Recharge Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['recharge_id']; ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?> VND</td>
                        <td><?php echo $row['recharge_time']; ?></td>
                        <td>
                            <?php if ($row['status'] == 'Thành công') { ?>
                                <span class="badge badge-success"><?php echo $row['status']; ?></span>
                            <?php } else { ?>
                                <span class="badge badge-danger"><?php echo $row['status']; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&filter_date=<?php echo $filter_date; ?>&filter_amount=<?php echo $filter_amount; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
