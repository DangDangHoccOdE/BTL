<?php
include 'dbh.php';
include 'header.php';

if (!isset($_GET['movie_id'])) {
    echo "Phim không tồn tại!";
    exit();
}

$movie_id = $_GET['movie_id'];
$query = "SELECT * FROM movies WHERE mid = $movie_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    // Kiểm tra lỗi truy vấn
    echo "Lỗi truy vấn: " . mysqli_error($conn);
    exit();
}

$movie = mysqli_fetch_assoc($result);

if (!$movie) {
    echo "Phim không tồn tại!";
    exit();
}

// Lấy thể loại của phim hiện tại
$category_query = "
    SELECT categories.name 
    FROM movie_categories 
    JOIN categories ON movie_categories.category_id = categories.id 
    WHERE movie_categories.movie_id = $movie_id
";
$category_result = mysqli_query($conn, $category_query);

if (!$category_result) {
    echo "Lỗi truy vấn thể loại: " . mysqli_error($conn);
    exit();
}

$category = mysqli_fetch_assoc($category_result);

// Kiểm tra xem người dùng đã mua phim chưa
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($user_id) {
    $purchased_query = "
        SELECT * FROM purchases 
        WHERE user_id = $user_id AND movie_id = $movie_id
    ";
    $purchased_result = mysqli_query($conn, $purchased_query);

    if (!$purchased_result) {
        echo "Lỗi truy vấn mua phim: " . mysqli_error($conn);
        exit();
    }

    $purchased = mysqli_num_rows($purchased_result) > 0;
} else {
    $purchased = false; // Nếu người dùng chưa đăng nhập
}

// Lấy danh sách 4 phim cùng thể loại
$related_movies_query = "
    SELECT m.mid, m.name, m.imgpath, m.price
    FROM movies m
    JOIN movie_categories mc ON m.mid = mc.movie_id
    WHERE mc.category_id = (SELECT category_id FROM movie_categories WHERE movie_id = $movie_id LIMIT 1)
    AND m.mid != $movie_id
    LIMIT 4
";
$related_movies_result = mysqli_query($conn, $related_movies_query);

if (!$related_movies_result) {
    echo "Lỗi truy vấn phim cùng thể loại: " . mysqli_error($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thông tin chi tiết phim - <?php echo htmlspecialchars($movie['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .movie-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .movie-image img {
            border-radius: 8px;
        }
        .movie-info h2 {
            font-size: 1.75rem;
            color: #333;
        }
        .related-movies .col-md-3 {
            margin-top: 15px;
        }
        .related-movies img {
            border-radius: 5px;
            transition: transform 0.3s;
        }
        .related-movies img:hover {
            transform: scale(1.05);
        }
        .toast {
            position: fixed;
            top: 40px;
            right: 20px;
            min-width: 250px;
            z-index: 1050;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- Toast thông báo -->
    <div aria-live="polite" aria-atomic="true">
        <div class="toast" id="toastMessage" data-bs-delay="2000">
            <div class="toast-body" id="toastBody">
                <!-- Nội dung thông báo -->
            </div>
        </div>
    </div>

    <!-- Thông tin phim -->
    <div class="movie-container row">
        <div class="col-md-4 movie-image">
            <img src="uploads/<?php echo $movie['imgpath']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['name']); ?>">
        </div>
        <div class="col-md-8 movie-info">
            <h2><?php echo ucwords($movie['name']); ?></h2>
            <p><strong>Thể loại:</strong> <?php echo htmlspecialchars($category['name']); ?></p>
            <p><strong>Giá:</strong> <?php echo $movie['price'] == 0 ? "Miễn phí" : number_format($movie['price'], 2) . " VND"; ?></p>
            <p><strong>Ngày phát hành:</strong> <?php echo $movie['rdate']; ?></p>
            <p><strong>Thời lượng:</strong> <?php echo $movie['runtime']; ?> phút</p>
            <p><strong>Mô tả:</strong> <?php echo $movie['description']; ?></p>
            <?php if ($purchased): ?>
                <button class="btn btn-success" onclick="watchMovie(<?php echo $movie_id; ?>)">Xem ngay</button>
            <?php elseif ($movie['price'] == 0): ?>
                <button class="btn btn-success" onclick="watchMovie(<?php echo $movie_id; ?>)">Xem ngay</button>
            <?php else: ?>
                <button id="actionButton" class="btn btn-primary" onclick="buyMovie(<?php echo $movie_id; ?>)">Mua ngay</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Khu vực hiển thị các phim cùng thể loại -->
    <div class="related-movies mt-5">
        <h4>Phim cùng thể loại</h4>
        <div class="row">
            <?php while ($related_movie = mysqli_fetch_assoc($related_movies_result)): ?>
                <div class="col-md-3 text-center">
                    <a href="movie_preview.php?movie_id=<?php echo $related_movie['mid']; ?>">
                        <img src="uploads/<?php echo $related_movie['imgpath']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($related_movie['name']); ?>">
                    </a>
                    <h5 class="mt-2"><?php echo ucwords($related_movie['name']); ?></h5>
                    <p><?php echo $related_movie['price'] == 0 ? "Miễn phí" : number_format($related_movie['price'], 2) . " VND"; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- JavaScript kích hoạt toast -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Kích hoạt Toast
        const toast = new bootstrap.Toast(document.getElementById('toastMessage'));
        toast.show();
    });

    function watchMovie(movieId) {
        window.location.href = "movie.php?id=" + movieId;
    }

    function buyMovie(movieId) {
        $.ajax({
            url: "user/action/buy_movie.php",
            type: "POST",
            data: { movie_id: movieId },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status) {
                    // Cập nhật nội dung toast với thông báo thành công
                    $("#toastBody").text(data.message);
                    $("#toastMessage").removeClass("bg-danger").addClass("bg-success");

                    // Chuyển đổi nút "Mua ngay" thành "Xem ngay"
                    const actionButton = document.getElementById("actionButton");
                    actionButton.textContent = "Xem ngay";
                    actionButton.classList.remove("btn-primary");
                    actionButton.classList.add("btn-success");
                    actionButton.setAttribute("onclick", "watchMovie()");
                } else {
                    // Cập nhật nội dung toast với thông báo thất bại
                    $("#toastBody").text(data.message);
                    $("#toastMessage").removeClass("bg-success").addClass("bg-danger");
                }
                // Hiển thị toast
                const toast = new bootstrap.Toast(document.getElementById('toastMessage'));
                toast.show();
            }
        });
    }
</script>

</body>
</html>

<?php
    include 'footer.php';
?>
