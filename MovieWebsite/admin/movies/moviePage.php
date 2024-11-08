<?php
include '../../dbh.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    
    if (empty($movie_name) || empty($rdate) || empty($runtime) || empty($description)) {
        echo json_encode(['message' => 'Dữ liệu không hợp lệ!', 'type' => 'error']);
        exit;
    }

    // Chèn phim vào bảng movies
    $insert_sql = "INSERT INTO movies (name, rdate, runtime, description, viewers, imgpath, videopath, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "ssssissi", $movie_name, $rdate, $runtime, $description, $viewers, $imgpath, $videopath, $price);
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['message' => 'Lỗi khi thêm phim!', 'type' => 'error']);
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
            echo json_encode(['message' => 'Lỗi khi thêm thể loại cho phim!', 'type' => 'error']);
            exit;
        }
    }

    echo json_encode(['message' => 'Phim đã được thêm thành công!', 'type' => 'success']);
    exit;
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

    if (empty($movie_name) || empty($rdate) || empty($runtime) || empty($description)) {
        echo json_encode(['message' => 'Dữ liệu không hợp lệ!', 'type' => 'error']);
        exit;
    }

    // Cập nhật thông tin phim
    $update_sql = "UPDATE movies SET name = ?, rdate = ?, runtime = ?, description = ?, viewers = ?, imgpath = ?, videopath = ?, price = ? WHERE mid = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "ssssissii", $movie_name, $rdate, $runtime, $description, $viewers, $imgpath, $videopath, $price, $movie_id);
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['message' => 'Lỗi khi sửa phim!', 'type' => 'error']);
        exit;
    }

    // Xóa thể loại cũ và chèn lại thể loại mới
    $delete_categories_sql = "DELETE FROM movie_categories WHERE movie_id = ?";
    $stmt = mysqli_prepare($conn, $delete_categories_sql);
    mysqli_stmt_bind_param($stmt, "i", $movie_id);
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['message' => 'Lỗi khi cập nhật thể loại!', 'type' => 'error']);
        exit;
    }

   // Thêm thể loại mới
foreach ($selected_categories as $category_id) {
    $insert_category_sql = "INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_category_sql);
    if (!$stmt) {
        echo json_encode(['message' => 'Lỗi khi chuẩn bị câu truy vấn: ' . mysqli_error($conn), 'type' => 'error']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ii", $movie_id, $category_id);

    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['message' => 'Lỗi khi thực thi câu truy vấn thêm thể loại: ' . mysqli_stmt_error($stmt), 'type' => 'error']);
        exit;
    }
}


    echo json_encode(['message' => 'Phim đã được cập nhật thành công!', 'type' => 'success']);
    exit;
}

// Xóa phim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_movie']) && $_POST['delete_movie'] == true) {
    $movie_id = $_POST['movie_id'];

    // Kiểm tra nếu movie_id là số hợp lệ
    if (!is_numeric($movie_id)) {
        echo json_encode(['message' => 'ID phim không hợp lệ!', 'type' => 'error']);
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
        echo json_encode(['message' => 'Lỗi khi xóa phim!', 'type' => 'error']);
        exit;
    }

    echo json_encode(['message' => 'Phim đã được xóa thành công!', 'type' => 'success']);
    exit;
}


// Lấy danh sách thể loại
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['load_categories'])) {
    $category_sql = "SELECT * FROM categories";
    $result = mysqli_query($conn, $category_sql);
    if (!$result) {
        echo json_encode(['message' => 'Lỗi khi truy vấn dữ liệu thể loại!', 'type' => 'error']);
        exit;
    }
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    echo json_encode(['categories' => $categories]);
    exit;
}

// Tìm kiếm và sắp xếp phim
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $sort_by = isset($_GET['sort_by']) && $_GET['sort_by'] == 'viewers' ? 'viewers DESC' : 'name ASC';

    $search_sql = "SELECT * FROM movies WHERE name LIKE ? ORDER BY $sort_by";
    $stmt = mysqli_prepare($conn, $search_sql);
    $search_param = "%$search_term%";
    mysqli_stmt_bind_param($stmt, "s", $search_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    echo json_encode(['movies' => $movies]);
    exit;
}

// Lấy tất cả các phim và thể loại của chúng
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['load_all_movies'])) {
    $get_movies_sql = "
        SELECT m.mid, m.name, m.rdate, m.runtime, m.description, m.viewers, m.imgpath, m.videopath, m.price,
               GROUP_CONCAT(c.name SEPARATOR ', ') AS genres
        FROM movies m
        LEFT JOIN movie_categories mc ON m.mid = mc.movie_id
        LEFT JOIN categories c ON mc.category_id = c.id
        GROUP BY m.mid
    ";
    
    $result = mysqli_query($conn, $get_movies_sql);
    if (!$result) {
        echo json_encode(['message' => 'Lỗi khi truy vấn dữ liệu phim!', 'type' => 'error']);
        exit;
    }
    $movies = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $movies[] = $row;
    }
    
    echo json_encode(['movies' => $movies]);
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container my-5">
    <h1>Movie Management</h1>

    <!-- Form thêm phim -->
    <!-- Form thêm phim -->
<form id="addMovieForm">
    <h3>Add New Movie</h3>
    <input type="text" class="form-control mb-2" id="movie_name" name="movie_name" placeholder="Movie Name" required>
    <input type="text" class="form-control mb-2" id="rdate" name="rdate" placeholder="Release Date" required>
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


    <!-- Tìm kiếm và sắp xếp phim -->
    <input type="text" id="search" placeholder="Search by name" class="form-control mb-2">
    <select id="sort_by" class="form-control mb-2">
        <option value="name">Sort by Name</option>
        <option value="viewers">Sort by Viewers</option>
    </select>

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
                    <input type="text" class="form-control mb-2" id="edit_rdate" name="rdate" placeholder="Release Date" required>
                    <input type="text" class="form-control mb-2" id="edit_runtime" name="runtime" placeholder="Runtime" required>
                    <textarea class="form-control mb-2" id="edit_description" name="description" placeholder="Description" required></textarea>
                    <input type="number" class="form-control mb-2" id="edit_viewers" name="viewers" placeholder="Viewers" value="1" required>
                    <input type="text" class="form-control mb-2" id="edit_imgpath" name="imgpath" placeholder="Image Path" required>
                    <input type="text" class="form-control mb-2" id="edit_videopath" name="videopath" placeholder="Video Path" required>
                    <input type="number" class="form-control mb-2" id="edit_price" name="price" placeholder="Price" required>
                    <button type="submit" class="btn btn-primary">Update Movie</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function () {
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
                alert(response);

                try {
                    var data = JSON.parse(response);  // Kiểm tra và xử lý dữ liệu JSON
                    alert(data.message);
                    if (data.type === 'success') {
                        loadMovies();  // Hàm này sẽ load lại danh sách phim nếu cần
                    }
                } catch (e) {
                    console.error('Lỗi khi phân tích cú pháp JSON:', e);
                    alert('Có lỗi xảy ra khi thêm phim.');
                }
            },
            error: function(xhr, status, error) {
                alert('Lỗi trong quá trình thêm phim. Vui lòng thử lại.');
            }
        });
    });

    // Hàm load danh sách phim
function loadMovies() {
    $.get('moviePage.php', { load_all_movies: true }, function(response) {
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
            }
        } catch (e) {
            console.error('Lỗi khi phân tích cú pháp JSON:', e);
            alert('Có lỗi khi tải danh sách phim.');
        }
    });
}

    // Gọi loadMovies() khi trang được tải
    loadMovies();

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
                    $('#editModal').modal('show');
                }
            } catch (e) {
                console.error('Lỗi khi phân tích cú pháp JSON:', e);
                alert('Có lỗi khi tải thông tin phim.');
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
                alert(response);
                try {
                    var data = JSON.parse(response);
                    if (data.type === 'success') {
                        loadMovies(); 
                        $('#editModal').modal('hide');
                    }
                } catch (e) {
                    console.error('Lỗi khi phân tích cú pháp JSON:', e);
                    alert('Có lỗi xảy ra khi sửa phim.');
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
            alert(data.message);
            if (data.type === 'success') {
                loadMovies();
            }
        });
    }
});
});
</script>

</body>
</html>