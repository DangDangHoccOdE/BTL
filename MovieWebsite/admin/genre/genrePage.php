<?php
include '../../dbh.php';
include "../filterRequestAdmin.php";

$success_message = ''; // Để lưu thông báo thành công
$error_message = '';   // Để lưu thông báo lỗi

// Xử lý thêm thể loại mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_genre'])) {
    $genre_name = $_POST['genre_name'];

    // Kiểm tra tên thể loại có trùng không
    $check_sql = "SELECT COUNT(*) AS count FROM categories WHERE name = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "s", $genre_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        $error_message = "Tên thể loại đã tồn tại.";
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    } elseif (empty($genre_name)) {
        $error_message = "Tên thể loại không được để trống.";
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    } else {
        $insert_sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "s", $genre_name);
        mysqli_stmt_execute($stmt);
        $success_message = "Thể loại đã được thêm thành công!";
        echo json_encode(['message' => $success_message, 'type' => 'success']);
        exit;
    }
}

// Xử lý cập nhật thể loại
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_genre'])) {
    $genre_id = $_POST['genre_id'];
    $genre_name = $_POST['genre_name'];

    // Kiểm tra tên thể loại có trùng không
    $check_sql = "SELECT COUNT(*) AS count FROM categories WHERE name = ? AND id <> ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "si", $genre_name, $genre_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        $error_message = "Tên thể loại đã tồn tại.";
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    } elseif (empty($genre_name)) {
        $error_message = "Tên thể loại không được để trống.";
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    } else {
        $update_sql = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "si", $genre_name, $genre_id);
        mysqli_stmt_execute($stmt);
        $success_message = "Thể loại đã được cập nhật thành công!";
        echo json_encode(['message' => $success_message, 'type' => 'success']);
        exit;
    }
}

// Xử lý xóa thể loại
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_genre'])) {
    $genre_id = $_GET['delete_genre'];

    $delete_sql = "DELETE FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $genre_id);
    mysqli_stmt_execute($stmt);
    $success_message = "Thể loại đã được xóa thành công!";
    exit;
}

// // Lấy danh sách thể loại và phân trang
// $page = isset($_GET['page']) ? $_GET['page'] : 1;
// $limit = 6;
// $offset = ($page - 1) * $limit;

// $total_sql = "SELECT COUNT(*) AS total FROM categories";
// $total_result = mysqli_query($conn, $total_sql);
// $total_row = mysqli_fetch_assoc($total_result);
// $total_pages = ceil($total_row['total'] / $limit);

// $list_sql = "SELECT * FROM categories ORDER BY id LIMIT ? OFFSET ?";
// $stmt = mysqli_prepare($conn, $list_sql);
// mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
// mysqli_stmt_execute($stmt);
// $result = mysqli_stmt_get_result($stmt);

// // Trả về dữ liệu dưới dạng JSON nếu là yêu cầu AJAX
// if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ajax'])) {
//     $genres = [];
//     while ($row = mysqli_fetch_assoc($result)) {
//         $genres[] = $row;
//     }
//     echo json_encode([
//         'genres' => $genres,
//         'total_pages' => $total_pages
//     ]);
//     exit;
// }

// Lấy danh sách thể loại và phân trang
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Kiểm tra xem có từ khóa tìm kiếm không
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Cập nhật SQL để tìm kiếm thể loại theo từ khóa
$list_sql = "SELECT * FROM categories WHERE name LIKE ? ORDER BY id LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $list_sql);
$searchTermLike = "%" . $searchTerm . "%"; // Thêm ký tự % để tìm kiếm theo chuỗi
mysqli_stmt_bind_param($stmt, "sii", $searchTermLike, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Trả về dữ liệu dưới dạng JSON
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ajax'])) {
    $genres = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $genres[] = $row;
    }

    // Tính toán tổng số trang
    $total_sql = "SELECT COUNT(*) AS total FROM categories WHERE name LIKE ?";
    $stmt = mysqli_prepare($conn, $total_sql);
    mysqli_stmt_bind_param($stmt, "s", $searchTermLike);
    mysqli_stmt_execute($stmt);
    $total_result = mysqli_stmt_get_result($stmt);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_pages = ceil($total_row['total'] / $limit);

    echo json_encode([
        'genres' => $genres,
        'total_pages' => $total_pages
    ]);
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genre Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            display: none;
        }

        .toast.show {
            opacity: 1;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1>Genre Management</h1>

        <!-- Toast container -->
        <div id="toast-container"></div>

        <!-- Thêm thể loại mới -->
        <div class="row mb-3">
            <div class="col">
                <h3>Add New Genre</h3>
                <form id="addGenreForm">
                    <div class="form-group">
                        <label for="genre_name">Genre Name:</label>
                        <input type="text" class="form-control" id="genre_name" name="genre_name" required>
                        <small id="errorMessage" class="form-text text-danger"></small>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Genre</button>
                </form>
            </div>
        </div>

       <!-- Tìm kiếm thể loại -->
        <div class="row mb-3">
            <div class="col">
                <h3>Search Genre</h3>
                <div class="d-flex">
                    <input type="text" class="form-control" id="searchGenre" placeholder="Tìm theo tên thể loại">
                    <button class="btn btn-primary ml-2" id="searchBtn">Tìm kiếm</button>
                </div>
            </div>
        </div>



        <!-- Hiển thị danh sách thể loại -->
        <table class="table table-striped" id="genreTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dữ liệu sẽ được tải qua AJAX -->
            </tbody>
        </table>

        <!-- Phân trang -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
                <!-- Dữ liệu phân trang sẽ được tải qua AJAX -->
            </ul>
        </nav>
    </div>

    <!-- Modal Edit Genre -->
    <div class="modal fade" id="editGenreModal" tabindex="-1" role="dialog" aria-labelledby="editGenreLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGenreLabel">Edit Genre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editGenreForm">
                        <input type="hidden" id="editGenreId" name="genre_id">
                        <div class="form-group">
                            <label for="editGenreName">Genre Name:</label>
                            <input type="text" class="form-control" id="editGenreName" name="genre_name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        // Hàm hiển thị toast
        function showToast(message, type ) {
            var toastColor = type === 'error' ? 'bg-danger' : 'bg-success'; // 'bg-danger' cho lỗi (red), 'bg-success' cho thành công (green)

    // Tạo toast HTML
    var toastHTML = `
        <div class="toast align-items-center text-white ${toastColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        </div>`;

    // Thêm toast vào body
    document.body.insertAdjacentHTML('beforeend', toastHTML);

    // Hiển thị toast
    var toast = new bootstrap.Toast(document.querySelector('.toast'));
    toast.show();

    // Xóa toast sau khi hiển thị 2 giây
    setTimeout(function() {
        document.querySelector('.toast').remove();
    }, 2000);
        }

        // Thêm thể loại mới
        $('#addGenreForm').on('submit', function(e) {
            e.preventDefault();
            const genreName = $('#genre_name').val();
            $.ajax({
                url: 'genrePage.php',
                method: 'POST',
                data: { add_genre: true, genre_name: genreName },
                success: function(response) {
                    const data = JSON.parse(response);
                    showToast(data.message, data.type);
                    $('#genre_name').val('');
                    loadGenres(); // Cập nhật lại bảng thể loại
                },
                error: function() {
                    showToast('Đã xảy ra lỗi, vui lòng thử lại!', 'error');
                }
            });
        });

        // Cập nhật thể loại
        $('#editGenreForm').on('submit', function(e) {
            e.preventDefault();
            const genreId = $('#editGenreId').val();
            const genreName = $('#editGenreName').val();
            $.ajax({
                url: 'genrePage.php',
                method: 'POST',
                data: { update_genre: true, genre_id: genreId, genre_name: genreName },
                success: function(response) {
                    const data = JSON.parse(response);
                    showToast(data.message, data.type);
                    $('#editGenreModal').modal('hide');
                    loadGenres(); // Cập nhật lại bảng thể loại
                },
                error: function() {
                    showToast('Đã xảy ra lỗi, vui lòng thử lại!', 'error');
                }
            });
        });

        // Xóa thể loại
        function deleteGenre(genreId) {
            if (confirm("Bạn có chắc chắn muốn xóa thể loại này?")) {
                $.ajax({
                    url: 'genrePage.php',
                    method: 'GET',
                    data: { delete_genre: genreId },
                    success: function() {
                        showToast('Thể loại đã được xóa thành công!', 'success');
                        loadGenres(); // Cập nhật lại bảng thể loại
                    },
                    error: function() {
                        showToast('Đã xảy ra lỗi khi xóa thể loại!', 'error');
                    }
                });
            }
        }

       // Xử lý khi nhấn nút Tìm kiếm
$('#searchBtn').on('click', function() {
    const searchTerm = $('#searchGenre').val();
    loadGenres(1, searchTerm); // Tải lại thể loại theo từ khóa tìm kiếm
});

// Hàm tải danh sách thể loại qua AJAX, có thể truyền tham số tìm kiếm
function loadGenres(page = 1, searchTerm = '') {
    $.ajax({
        url: 'genrePage.php',
        method: 'GET',
        data: { ajax: true, page: page, search: searchTerm }, // Gửi từ khóa tìm kiếm
        success: function(response) {
            const data = JSON.parse(response);
            const genres = data.genres;
            const totalPages = data.total_pages;
            
            // Hiển thị danh sách thể loại
            const tbody = $('#genreTable tbody');
            tbody.empty();
            genres.forEach(genre => {
                tbody.append(`
                    <tr>
                        <td>${genre.id}</td>
                        <td>${genre.name}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editGenre(${genre.id}, '${genre.name}')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteGenre(${genre.id})">Delete</button>
                        </td>
                    </tr>
                `);
            });

            // Hiển thị phân trang
            const pagination = $('#pagination');
            pagination.empty();
            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                    <li class="page-item ${i === page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadGenres(${i}, '${searchTerm}')">${i}</a>
                    </li>
                `);
            }
        },
        error: function() {
            showToast('Đã xảy ra lỗi khi tải danh sách thể loại!', 'error');
        }
    });
}



        // Tải danh sách thể loại khi tải trang
        $(document).ready(function() {
            loadGenres();
        });

        // Sửa thể loại
        function editGenre(id, name) {
            $('#editGenreId').val(id);
            $('#editGenreName').val(name);
            $('#editGenreModal').modal('show');
        }
    </script>
</body>
</html>
