<?php
include '../../dbh.php';
include "../filterRequestAdmin.php";


ini_set('display_errors', 1);
error_reporting(E_ALL);

$success_message = ''; // Để lưu thông báo thành công
$error_message = '';   // Để lưu thông báo lỗi

// Thêm phim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_movie'])) {
    $movie_name = $_POST['movie_name'];

    $rdate = $_POST['rdate'];
    $runtime = $_POST['runtime'];
    $description = $_POST['description'];
    $viewers = $_POST['viewers'];
    $imgpath = $_POST['imgpath'];
    $videopath = $_POST['videopath'];
    $price = $_POST['price'];
    $selected_categories = $_POST['categories'];  // Mảng chứa ID thể loại đã chọn
    
     // Kiểm tra tên phim trong CSDL, xác minh không trùng lặp
     $check_sql = "SELECT mid FROM movies WHERE name = ?";
     $stmt = mysqli_prepare($conn, $check_sql);
     mysqli_stmt_bind_param($stmt, "s", $movie_name);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
 
     if (mysqli_num_rows($result) > 0) {
         $error_message = 'Tên phim đã tồn tại!';
         echo json_encode(['message' => $error_message, 'type' => 'error']);
         exit;
     } else if (empty($movie_name) || empty($rdate) || empty($runtime) || empty($description)) {
         $error_message = 'Dữ liệu không hợp lệ!';
         echo json_encode(['message' => $error_message, 'type' => 'error']);
         exit;
     }else{
        // Chèn phim vào bảng movies
        $insert_sql = "INSERT INTO movies (name, rdate, runtime, description, viewers, imgpath, videopath, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ssssissi", $movie_name, $rdate, $runtime, $description, $viewers, $imgpath, $videopath, $price);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = 'Lỗi khi thêm phim!';
            echo json_encode(['message' => $error_message, 'type' => 'error']);
            exit;
        }

        // Lấy ID phim vừa thêm
        $movie_id = mysqli_insert_id($conn);

        // Chèn thể loại vào bảng movie_categories
        foreach ($selected_categories as $category_id) {
            $insert_category_sql = "INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_category_sql);
            mysqli_stmt_bind_param($stmt, "ii", $movie_id, $category_id);
            if (!mysqli_stmt_execute($stmt)) {
                $error_message = 'Lỗi khi thêm thể loại cho phim!';
                echo json_encode(['message' => $error_message, 'type' => 'error']);
                exit;
            }
        }

        $success_message = 'Phim đã được thêm thành công!';
        echo json_encode(['message' => $success_message, 'type' => 'success']);
        exit;
     }
}

// Sửa phim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_movie'])) {
    $movie_id = $_POST['movie_id'];
    $movie_name = $_POST['movie_name'];
    $rdate = $_POST['rdate'];
    $runtime = $_POST['runtime'];
    $description = $_POST['description'];
    $viewers = $_POST['viewers'];
    $imgpath = $_POST['imgpath'];
    $videopath = $_POST['videopath'];
    $price = $_POST['price'];
    $selected_categories = $_POST['categories'];

     // Kiểm tra tên phim trong CSDL, tránh trùng tên với phim khác
     $check_sql = "SELECT mid FROM movies WHERE name = ? AND mid != ?";
     $stmt = mysqli_prepare($conn, $check_sql);
     mysqli_stmt_bind_param($stmt, "si", $movie_name, $movie_id);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
 
     if (mysqli_num_rows($result) > 0) {
         $error_message = 'Tên phim đã tồn tại!';
         echo json_encode(['message' => $error_message, 'type' => 'error']);
         exit();
     } else if (empty($movie_name) || empty($rdate) || empty($runtime) || empty($description)) {
         $error_message = 'Dữ liệu không hợp lệ!';
         echo json_encode(['message' => $error_message, 'type' => 'error']);
         exit;
     }

    // Cập nhật thông tin phim
    $update_sql = "UPDATE movies SET name = ?, rdate = ?, runtime = ?, description = ?, viewers = ?, imgpath = ?, videopath = ?, price = ? WHERE mid = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "ssssissii", $movie_name, $rdate, $runtime, $description, $viewers, $imgpath, $videopath, $price, $movie_id);
    if (!mysqli_stmt_execute($stmt)) {
        $error_message = 'Lỗi khi sửa phim!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }

    // Xóa thể loại cũ và chèn lại thể loại mới
    $delete_categories_sql = "DELETE FROM movie_categories WHERE movie_id = ?";
    $stmt = mysqli_prepare($conn, $delete_categories_sql);
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    if (!mysqli_stmt_execute($stmt)) {
        $error_message = 'Lỗi khi cập nhật thể loại!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }

   // Thêm thể loại mới
foreach ($selected_categories as $category_id) {
    $insert_category_sql = "INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_category_sql);
    if (!$stmt) {
        $error_message = 'Lỗi khi chuẩn bị câu truy vấn: ' . mysqli_error($conn);
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ii", $movie_id, $category_id);

    if (!mysqli_stmt_execute($stmt)) {
        $error_message = 'Lỗi khi thực thi câu truy vấn thêm thể loại: ' . mysqli_stmt_error($stmt);
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }
}

    $success_message = 'Phim đã được cập nhật thành công!';
    echo json_encode(['message' => $success_message, 'type' => 'success']);
    exit;
}

// Xóa phim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_movie']) && $_POST['delete_movie'] == true) {
    $movie_id = $_POST['movie_id'];

    // Kiểm tra nếu movie_id là số hợp lệ
    if (!is_numeric($movie_id)) {
        $error_message = 'ID phim không hợp lệ!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }

    // Xóa thể loại phim
    $delete_categories_sql = "DELETE FROM movie_categories WHERE movie_id = ?";
    $stmt = mysqli_prepare($conn, $delete_categories_sql);
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    mysqli_stmt_execute($stmt);

    // Xóa phim
    $delete_sql = "DELETE FROM movies WHERE mid = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    if (!mysqli_stmt_execute($stmt)) {
        $error_message = 'Lỗi khi xóa phim!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }

    $success_message = 'Phim đã được xóa thành công!';
    echo json_encode(['message' => $success_message, 'type' => 'success']);
    exit;
}


// Lấy danh sách thể loại
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['load_categories'])) {
    $category_sql = "SELECT * FROM categories";
    $result = mysqli_query($conn, $category_sql);
    if (!$result) {
        $error_message = 'Lỗi khi truy vấn dữ liệu thể loại!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    echo json_encode(['categories' => $categories]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET['search']) || isset($_GET['sort_by']))) {
    // Xử lý tìm kiếm hoặc sắp xếp
    $search_term = isset($_GET['search']) ? $_GET['search'] : '';
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
    
    // Câu lệnh SQL cơ bản
    $search_sql = "SELECT * FROM movies";
    
    // Điều kiện tìm kiếm
    if ($search_term) {
        $search_sql .= " WHERE name LIKE ?";
        $search_param = "%" . $search_term . "%";
    }

    // Điều kiện sắp xếp
    if ($sort_by == 'viewers_asc') {
        $search_sql .= " ORDER BY viewers ASC";
    } elseif ($sort_by == 'viewers_desc') {
        $search_sql .= " ORDER BY viewers DESC";
    }
    
    $stmt = mysqli_prepare($conn, $search_sql);

    // Ràng buộc tham số tìm kiếm nếu có
    if ($search_term) {
        mysqli_stmt_bind_param($stmt, "s", $search_param);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    echo json_encode(['movies' => $movies]);
    exit;
}


// Lấy tất cả các phim và thể loại của chúng với phân trang
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['load_all_movies'])) {
    // Nhận tham số `page` và `limit`, hoặc đặt giá trị mặc định
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $offset = ($page - 1) * $limit;
    
    $get_movies_sql = "
        SELECT m.mid, m.name, m.rdate, m.runtime, m.description, m.viewers, m.imgpath, m.videopath, m.price,
               GROUP_CONCAT(c.name SEPARATOR ', ') AS genres
        FROM movies m
        LEFT JOIN movie_categories mc ON m.mid = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.id
        GROUP BY m.mid
        LIMIT $limit OFFSET $offset
    ";
    
    $result = mysqli_query($conn, $get_movies_sql);
    if (!$result) {
        $error_message = 'Lỗi khi truy vấn dữ liệu phim!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
        exit;
    }
    
    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    
    // Trả về số lượng phim
    $count_sql = "SELECT COUNT(*) AS total FROM movies";
    $count_result = mysqli_query($conn, $count_sql);
    $total = mysqli_fetch_assoc($count_result)['total'];
    
    echo json_encode([
        'movies' => $movies,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'totalPages' => ceil($total / $limit)
    ]);
    exit;
}

// Lấy thông tin của một phim cụ thể
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['load_movie_by_id'])) {
    $movie_id = $_GET['load_movie_by_id'];

    $movie_sql = "
        SELECT m.*, GROUP_CONCAT(c.id) AS categories
        FROM movies m
        LEFT JOIN movie_categories mc ON m.mid = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.id
        WHERE m.mid = ?
        GROUP BY m.mid
    ";

    $stmt = mysqli_prepare($conn, $movie_sql);
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['movie' => $row]);
    } else {
        $error_message = 'Phim không tồn tại!';
        echo json_encode(['message' => $error_message, 'type' => 'error']);
    }
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Management</title>
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
    <h1>Movie Management</h1>

       <!-- Toast container -->
       <div id="toast-container"></div>
    <!-- Form thêm phim -->
<form id="addMovieForm">
    <h3>Add New Movie</h3>
    <input type="text" class="form-control mb-2" id="movie_name" name="movie_name" placeholder="Movie Name" required>
    <input type="date" class="form-control mb-2" id="rdate" name="rdate" placeholder="Release Date" required>
    <input type="text" class="form-control mb-2" id="runtime" name="runtime" placeholder="Runtime" required>
    <textarea class="form-control mb-2" id="description" name="description" placeholder="Description" required></textarea>
    <input type="number" class="form-control mb-2" id="viewers" name="viewers" placeholder="Viewers" value="1" required>
    <input type="text" class="form-control mb-2" id="imgpath" name="imgpath" placeholder="Image Path" required>
    <input type="text" class="form-control mb-2" id="videopath" name="videopath" placeholder="Video Path" required>
    <input type="number" class="form-control mb-2" id="price" name="price" placeholder="Price" required>

    <!-- Thể loại phim -->
    <select multiple class="form-control mb-2" id="categories" name="categories[]">
        <?php
        // Lấy tất cả thể loại từ cơ sở dữ liệu
        $get_categories_sql = "SELECT * FROM categories";
        $result = mysqli_query($conn, $get_categories_sql);
        while ($category = mysqli_fetch_assoc($result)) {
            echo "<option value='{$category['id']}'>{$category['name']}</option>";
        }
        ?>
    </select>

    <button type="submit" class="btn btn-primary">Add Movie</button>
</form>

<input type="text" id="search" placeholder="Tìm kiếm theo tên phim" class="form-control mb-2">
<select id="sort_by" class="form-control mb-2">
    <option value="">Không sắp xếp</option>
    <option value="viewers_asc">Sắp xếp theo lượt xem tăng dần</option>
    <option value="viewers_desc">Sắp xếp theo lượt xem giảm dần</option>
</select>
<button id="searchBtn" class="btn btn-primary">Tìm kiếm / Sắp xếp</button>

    <!-- Bảng danh sách phim -->
    <table class="table" id="movieTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Genre</th>
            <th>Release Date</th>
            <th>Runtime</th>
            <th>Description</th>
            <th>Viewers</th>
            <th>Image Path</th>
            <th>Video Path</th>
            <th>Price</th>     
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>

</table>
<!-- Phân trang -->
<nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
                <!-- Dữ liệu phân trang sẽ được tải qua AJAX -->
            </ul>
        </nav>
</div>

<!-- Modal to Edit Movie -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Movie</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMovieForm">
                    <input type="hidden" id="edit_movie_id" name="movie_id">
                    <input type="text" class="form-control mb-2" id="edit_movie_name" name="movie_name" placeholder="Movie Name" required>
                    <input type="date" class="form-control mb-2" id="edit_rdate" name="rdate" placeholder="Release Date" required>
                    <input type="text" class="form-control mb-2" id="edit_runtime" name="runtime" placeholder="Runtime" required>
                    <textarea class="form-control mb-2" id="edit_description" name="description" placeholder="Description" required></textarea>
                    <input type="number" class="form-control mb-2" id="edit_viewers" name="viewers" placeholder="Viewers" value="1" required>
                    <input type="text" class="form-control mb-2" id="edit_imgpath" name="imgpath" placeholder="Image Path" required>
                    <input type="text" class="form-control mb-2" id="edit_videopath" name="videopath" placeholder="Video Path" required>
                    <input type="number" class="form-control mb-2" id="edit_price" name="price" placeholder="Price" required>

                    <!-- Form edit movie (with categories) -->
                    <select multiple class="form-control mb-2" id="edit_categories" name="categories[]">
                        <?php
                        $result = mysqli_query($conn, $get_categories_sql);
                        while ($category = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$category['id']}'>{$category['name']}</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" class="btn btn-primary">Update Movie</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>

        
// $(document).ready(function () {
  // Hàm hiển thị toast
function showToast(message, type) {
    var toastColor = type === 'error' ? 'bg-danger' : 'bg-success';

    // Xóa toast cũ (nếu có) để tránh hiển thị chồng chéo
    document.querySelectorAll('.toast').forEach(toast => toast.remove());

    // Tạo toast HTML mới
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
    var toastElement = document.querySelector('.toast');
    var toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Xóa toast sau khi hiển thị 5 giây
    setTimeout(function() {
        if (toastElement) {
            toastElement.remove();
        }
    }, 5000);
}


    // Xử lý form thêm phim
    $('#addMovieForm').submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('add_movie', true);

        $.ajax({
            url: 'moviePage.php',  // Đảm bảo đường dẫn chính xác
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {

                try {
                    var data = JSON.parse(response);  // Kiểm tra và xử lý dữ liệu JSON
                    showToast(data.message, data.type);
                    if (data.type === 'success') {
                        loadMovies();  // Hàm này sẽ load lại danh sách phim nếu cần
                    }
                } catch (e) {
                    console.error('Lỗi khi phân tích cú pháp JSON:', e);
                    showToast(data.message, data.type);
                }
            },
            error: function(xhr, status, error) {
                showToast('Lỗi trong quá trình thêm phim. Vui lòng thử lại.','error');
            }
        });
    });

    function loadMovies(page = 1) {
    $.get('moviePage.php', { load_all_movies: true, page: page, limit: 5 }, function(response) {
        try {
            var data = JSON.parse(response);
            if (data.movies) {
                $('#movieTable tbody').empty();
                data.movies.forEach(function(movie) {
                    $('#movieTable tbody').append(`
                        <tr>
                            <td>${movie.mid}</td>
                            <td>${movie.name}</td>
                            <td>${movie.genres}</td>
                            <td>${movie.rdate}</td>
                            <td>${movie.runtime}</td>
                            <td>${movie.description}</td>
                            <td>${movie.viewers}</td>
                            <td>${movie.imgpath}</td>
                            <td>${movie.videopath}</td>
                            <td>${movie.price}</td>
                            <td>
                                <button class="btn btn-warning editBtn" data-id="${movie.mid}">Edit</button>
                                <button class="btn btn-danger deleteBtn" data-id="${movie.mid}">Delete</button>
                            </td>
                        </tr>
                    `);
                });

                // Hiển thị các nút phân trang
                $('#pagination').empty();
                for (let i = 1; i <= data.totalPages; i++) {
                    $('#pagination').append(`
                          <li class="page-item ${i === page ? 'active' : ''}">
                                <a class="page-link" href="javascript:void(0)" onclick="loadMovies(${i})">${i}</a>
                         </li>
                    `);
                }
            }
        } catch (e) {
            console.error('Lỗi khi phân tích cú pháp JSON:', e);
            showToast('Có lỗi khi tải danh sách phim.','error');
        }
    });
}

    // Gọi loadMovies() khi trang được tải
    $(document).ready(function() {
            loadMovies();
        });

    // Sửa phim (Mở modal)
$(document).on('click', '.editBtn', function() {
    var movie_id = $(this).data('id');
    // Lấy thông tin phim từ server
    $.get('moviePage.php', { load_movie_by_id: movie_id }, function(response) {
        try {
            var data = JSON.parse(response);
            if (data.movie) {
                var movie = data.movie;
                $('#edit_movie_id').val(movie.mid);
                $('#edit_movie_name').val(movie.name);
                $('#edit_rdate').val(movie.rdate);
                $('#edit_runtime').val(movie.runtime);
                $('#edit_description').val(movie.description);
                $('#edit_viewers').val(movie.viewers);
                $('#edit_imgpath').val(movie.imgpath);
                $('#edit_videopath').val(movie.videopath);
                $('#edit_price').val(movie.price);
                
                // Populate categories
                $('#edit_categories').val(movie.categories);
                
                // Show the modal
                $('#editModal').modal('show');
            }
        } catch (e) {
            console.error('Lỗi khi phân tích cú pháp JSON:', e);
            showToast('Có lỗi khi tải thông tin phim.','error');
        }
    });
});


    // Xử lý form sửa phim
    $('#editMovieForm').submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('edit_movie', true);

        $.ajax({
            url: 'moviePage.php', 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    showToast(data.message,data.type);
                    if (data.type === 'success') {
                        loadMovies(); 
                        $('#editModal').modal('hide');
                    }
                } catch (e) {
                    console.error('Lỗi khi phân tích cú pháp JSON:', e);
                    showToast(data.message, data.type);
                }
            }
        });
    });

   // Xóa phim
$(document).on('click', '.deleteBtn', function() {
    var movie_id = $(this).data('id');
    if (confirm('Are you sure you want to delete this movie?')) {
        $.post('moviePage.php', { delete_movie: true, movie_id: movie_id }, function(response) {
            const data = JSON.parse(response);
            showToast(data.message, data.type);
            if (data.type === 'success') {
                loadMovies();
            }
        });
    }
});

$(document).ready(function () {
    // Hàm tìm kiếm hoặc sắp xếp phim
    function searchOrSortMovies() {
        var searchTerm = $('#search').val().trim();
        var sortBy = $('#sort_by').val();
        
        // Chỉ định các tham số tùy vào việc người dùng tìm kiếm hoặc sắp xếp
        var params = {};
        
        if (searchTerm) {
            params.search = searchTerm;  // Nếu có từ khóa tìm kiếm, sẽ tìm kiếm theo tên
        }
        
        if (sortBy) {
            params.sort_by = sortBy;  // Nếu chọn sắp xếp, sẽ sắp xếp theo lượt xem
        }

        // Gửi yêu cầu GET với các tham số tìm kiếm hoặc sắp xếp
        $.get('moviePage.php', params, function (response) {
            try {
                var data = JSON.parse(response);
                if (data.movies) {
                    // Xóa danh sách phim hiện tại
                    $('#movieTable tbody').empty();

                    // Hiển thị danh sách phim sau khi tìm kiếm hoặc sắp xếp
                    data.movies.forEach(function (movie) {
                        $('#movieTable tbody').append(`
                            <tr>
                                <td>${movie.mid}</td>
                                <td>${movie.name}</td>
                                <td>${movie.rdate}</td>
                                <td>${movie.runtime}</td>
                                <td>${movie.description}</td>
                                <td>${movie.viewers}</td>
                                <td>${movie.imgpath}</td>
                                <td>${movie.videopath}</td>
                                <td>${movie.price}</td>
                                <td>
                                    <button class="btn btn-warning editBtn" data-id="${movie.mid}">Edit</button>
                                    <button class="btn btn-danger deleteBtn" data-id="${movie.mid}">Delete</button>
                                </td>
                            </tr>
                        `);
                    });
                }
            } catch (e) {
                console.error('Lỗi khi phân tích cú pháp JSON:', e);
                showToast('Có lỗi khi tìm kiếm hoặc sắp xếp phim.', 'error');
            }
        });
    }

    // Gọi hàm tìm kiếm hoặc sắp xếp khi nhấn nút Tìm kiếm / Sắp xếp
    $('#searchBtn').on('click', searchOrSortMovies);
});

// });
</script>

</body>
</html>