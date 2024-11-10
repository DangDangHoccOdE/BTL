<?php
session_start();
include '../../dbh.php';  // Đảm bảo đường dẫn đúng đến file dbh.php

// Kiểm tra nếu có từ khóa tìm kiếm
$search = '';
if (isset($_POST['search'])) {
    $search = trim(mysqli_real_escape_string($conn, $_POST['search'])); // Loại bỏ khoảng trắng thừa
}

// Truy vấn SQL với điều kiện LIKE để tìm kiếm theo tên hoặc email
$sql = "SELECT * FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-bar {
            width: 300px; /* Đặt chiều rộng hợp lý */
            display: flex;
            align-items: center;
        }
        .search-bar input {
            border-radius: 20px; /* Tạo góc tròn cho ô tìm kiếm */
            padding: 10px;
            border: 1px solid #ddd;
            width: 100%;
        }
        .search-bar button {
            border: none;
            background: transparent;
            padding: 0 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>User List</h2>

        <!-- Thanh tìm kiếm gọn gàng -->
        <form method="post" class="d-flex search-bar mb-3">
            <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search by name or email" required>
            <button type="submit">
                <i class="bi bi-search"></i> <!-- Biểu tượng tìm kiếm -->
                <img src="https://img.icons8.com/ios-filled/50/000000/search.png" alt="search icon" style="width: 20px; height: 20px;">
            </button>
        </form>

        <!-- Nút thêm người dùng -->
        <a href="insert.php" class="btn btn-success mb-3">Add New User</a>

        <!-- Bảng danh sách người dùng -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['phone']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td>
                        <a href="update.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
