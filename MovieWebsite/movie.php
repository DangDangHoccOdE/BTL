<?php
include 'dbh.php';
include 'header.php';

// Lấy thông tin phim theo ID
$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM movies WHERE mid = $movieId";
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


// Lấy thông tin người dùng nếu đã đăng nhập
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Phân trang cho đánh giá
$reviewsPerPage = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $reviewsPerPage;

// Lấy các đánh giá của phim
$sqlReviews = "SELECT reviews.*, users.name FROM reviews 
               JOIN users ON reviews.user_id = users.id 
               WHERE reviews.movie_id = $movieId 
               ORDER BY reviews.created_at DESC 
               LIMIT $reviewsPerPage OFFSET $offset";
$reviewsResult = $conn->query($sqlReviews);

// Lấy tổng số đánh giá
$sqlTotalReviews = "SELECT COUNT(*) AS total FROM reviews WHERE movie_id = $movieId";
$totalReviewsResult = $conn->query($sqlTotalReviews);
$totalReviews = $totalReviewsResult->fetch_assoc()['total'];
$totalPages = ceil($totalReviews / $reviewsPerPage);

// Lấy thể loại của phim hiện tại
$category_query = "
    SELECT categories.name 
    FROM movie_categories 
    JOIN categories ON movie_categories.category_id = categories.id 
    WHERE movie_categories.movie_id = $movieId
";
$category_result = mysqli_query($conn, $category_query);

if (!$category_result) {
    echo "Lỗi truy vấn thể loại: " . mysqli_error($conn);
    exit();
}

$category = mysqli_fetch_assoc($category_result);

// Lấy danh sách 4 phim cùng thể loại
$related_movies_query = "
    SELECT m.mid, m.name, m.imgpath, m.price
    FROM movies m
    JOIN movie_categories mc ON m.mid = mc.movie_id
    WHERE mc.category_id = (SELECT category_id FROM movie_categories WHERE movie_id = $movieId LIMIT 1)
    AND m.mid != $movieId
    LIMIT 8
";
$related_movies_result = mysqli_query($conn, $related_movies_query);
if (mysqli_num_rows($related_movies_result) == 0) {
    echo "Không có phim cùng thể loại!";
}
if (!$related_movies_result) {
    echo "Lỗi truy vấn phim cùng thể loại: " . mysqli_error($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['name']); ?> - Xem Phim</title>
    <style>
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
        body { background-color: #333; color: #ccc; font-family: Arial, sans-serif; }
        .container { width: 80%; margin: 20px auto; }
        .movie-title { font-size: 24px; color: #fff; }
        .movie-thumbnail { max-width: 100%; height: auto; border-radius: 8px; }
        .watch-button { margin-top: 10px; padding: 10px 20px; background-color: #ff5733; color: #fff; border: none; cursor: pointer; }
        .sidebar { float: right; width: 25%; background-color: #444; padding: 10px; }
        .sidebar h3 { color: #fff; }
        .review-form { margin-top: 30px; }
        .review-form label { display: block; margin-top: 10px; }
        .review-form input[type="file"], .review-form textarea, .review-form select { width: 100%; padding: 10px; margin-top: 5px; background-color: #555; color: #fff; border: none; }
        .review-form button { margin-top: 10px; padding: 10px 20px; background-color: #4CAF50; color: #fff; border: none; cursor: pointer; }
        .review-item { margin-bottom: 20px; padding: 15px; background-color: #444; border-radius: 8px; }
        .review-item .review-header { font-weight: bold; color: #ff5733; }
        .review-item .review-body { color: #ddd; }
        .review-item .review-footer { color: #bbb; font-size: 12px; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a, .pagination button { padding: 8px 12px; margin: 0 5px; background-color: #333; color: #ff5733; border: none; border-radius: 5px; cursor: pointer; }
        .pagination a:hover, .pagination button:hover { background-color: #444; }
        .movie-detail {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    border-radius: 10px;
}

.movie-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 20px;
}

.movie-image {
    flex: 1;
    max-width: 150px;
}

.movie-image img {
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.movie-info {
    flex: 2;
}

.movie-title {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin: 0;
    margin-bottom: 10px;
}

.movie-info p {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
}

.movie-info strong {
    color: var(--primary-color);
}

.movie-video {
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.movie-video iframe {
    width: 100%;
    height: 400px;
    border-radius: 10px;
}

    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
<div class="container">
<div class="movie-detail">
    <div class="movie-header">
        <div class="movie-image">
            <img src="uploads/<?php echo $movie['imgpath']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['name']); ?>">
        </div>
        <div class="movie-info">
            <h1 class="movie-title"><?php echo htmlspecialchars($movie['name']); ?></h1>
            <p><strong>Mô tả:</strong> <?php echo $movie['description']; ?></p>
        </div>
    </div>
    <div class="movie-video">
        <iframe src="<?php echo $movie['videopath']; ?>" frameborder="0" allowfullscreen></iframe>
    </div>
</div>


    <!-- Form đánh giá phim -->
    <div class="review-form">
        <h3>Đánh giá phim</h3>
        <form id="reviewForm" enctype="multipart/form-data">
            <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">
            <label for="rating">Số sao (1-5):</label>
            <select name="rating" id="rating">
                <option value="">Chọn số sao</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <label for="review_content">Nội dung đánh giá:</label>
            <textarea name="review_content" id="review_content" rows="4"></textarea>
            <button type="button" onclick="submitReview()">Gửi đánh giá</button>
        </form>
    </div>

    <!-- Hiển thị các đánh giá -->
    <div class="reviews">
        <h3>Đánh giá của người dùng</h3>
        <?php while ($review = $reviewsResult->fetch_assoc()) { ?>
            <div class="review-item" id="review-<?php echo $review['id']; ?>">
                <div class="review-header">
                    <?php echo htmlspecialchars($review['name']); ?> - <?php echo $review['rating']; ?>⭐
                </div>
                <div class="review-body" id="review-body-<?php echo $review['id']; ?>">
                    <?php echo nl2br(htmlspecialchars($review['review_content'])); ?>
                </div>
                <div class="review-footer">
                    Đánh giá vào: <?php echo $review['created_at']; ?>
                    <?php if ($userId == $review['user_id'] || $userRole == 'admin') { ?>
                        <a href="javascript:void(0);" onclick="deleteReview(<?php echo $review['id']; ?>)">Xóa</a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- Phân trang -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="?id=<?php echo $movieId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php } ?>
        </div>
    </div>

     <!-- Khu vực hiển thị các phim cùng thể loại -->
    <div class="related-movies mt-5">
        <h4>Phim cùng thể loại</h4>
        <div class="row">
            <?php while ($related_movie = mysqli_fetch_assoc($related_movies_result)): ?>
                <div class="col-md-3 text-center">
                    <a href="movie.php?movie_id=<?php echo $related_movie['mid']; ?>">
                        <img src="uploads/<?php echo $related_movie['imgpath']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($related_movie['name']); ?>">
                    </a>
                    <h5 class="mt-2"><?php echo ucwords($related_movie['name']); ?></h5>
                    <p><?php echo $related_movie['price'] == 0 ? "Miễn phí" : number_format($related_movie['price'], 2) . " VND"; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    function showToast(message, color) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "center",
            backgroundColor: color,
        }).showToast();
    }

    function submitReview() {
        const rating = document.getElementById("rating").value;
        const reviewContent = document.getElementById("review_content").value;

        if (!rating) {
            showToast("Vui lòng chọn số sao từ 1 đến 5.", "linear-gradient(to right, #ff5f6d, #ffc371)");
            return;
        }

        if (!reviewContent) {
            showToast("Vui lòng nhập nội dung đánh giá.", "linear-gradient(to right, #ff5f6d, #ffc371)");
            return;
        }

        const formData = new FormData(document.getElementById("reviewForm"));

        fetch("user/review/submit_review.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, "linear-gradient(to right, #00b09b, #96c93d)");

            const reviewItem = document.createElement("div");
            reviewItem.classList.add("review-item");
            reviewItem.id = `review-${data.id}`;  // Gán ID của bình luận mới
            reviewItem.innerHTML = `
                <div class="review-header">
                    Bạn - ${rating}⭐
                </div>
                <div class="review-body">
                    ${reviewContent}
                </div>
                <div class="review-footer">
                    Đánh giá vào: ngay
                    <a href="javascript:void(0);" onclick="deleteReview(${data.id})">Xóa</a>
                </div>
            `;
            document.querySelector(".reviews").appendChild(reviewItem);
            document.getElementById("reviewForm").reset();
        })
        .catch(error => {
            showToast("Đã xảy ra lỗi trong quá trình xử lý.", "linear-gradient(to right, #ff5f6d, #ffc371)");
        });
    }

    function deleteReview(reviewId) {
        if (confirm("Bạn chắc chắn muốn xóa bình luận này?")) {
            fetch(`user/review/delete_review.php?id=${reviewId}`, {
                method: 'GET',
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, "linear-gradient(to right, #00b09b, #96c93d)");

                const reviewElement = document.getElementById(`review-${reviewId}`);
                if (reviewElement) {
                    reviewElement.remove();  // Xóa bình luận khỏi trang
                }
            })
            .catch(error => {
                showToast("Lỗi khi xóa bình luận.", "linear-gradient(to right, #ff5f6d, #ffc371)");
            });
        }
    }
</script>
</body>
</html>

<?php
    include 'footer.php';
?>

