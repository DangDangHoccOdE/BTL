<?php
include 'dbh.php';
session_start();

// Lấy thông tin phim theo ID
$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM movies WHERE mid = $movieId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $movie = $result->fetch_assoc();
} else {
    echo "Phim không tồn tại.";
    exit;
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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['name']); ?> - Xem Phim</title>
    <style>
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
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
<div class="container">
    <div class="movie-detail">
        <h1 class="movie-title"><?php echo htmlspecialchars($movie['name']); ?> </h1>
        <img src="uploads/<?php echo $movie['imgpath']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['name']); ?>">
        <p><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
        <iframe width="100%" height="400" src="<?php echo $movie['videopath']; ?>" frameborder="0" allowfullscreen></iframe>
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


        </div>
           <!-- Phân trang -->
           <div class="pagination" id="pagination-container">
        <!-- Phân trang sẽ được tải qua AJAX -->
    </div>  
</div>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    function showToast(message, color) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: color,
        }).showToast();
    }
     // Hàm tải đánh giá qua AJAX
     function loadReviews(page = 1) {
        fetch(`get_reviews.php?movie_id=<?php echo $movieId; ?>&page=${page}`)
            .then(response => response.json())
            .then(data => {
                // Hiển thị đánh giá
                const reviewsContainer = document.getElementById("reviews-container");
                reviewsContainer.innerHTML = data.reviews.map(review => `
                    <div class="review-item" id="review-${review.id}">
                        <div class="review-header">
                            ${review.name} - ${review.rating}⭐
                        </div>
                        <div class="review-body">
                            ${review.review_content}
                        </div>
                        <div class="review-footer">
                            Đánh giá vào: ${review.created_at}
                            <?php if ($userId == $review['user_id'] || $userRole == 'admin') { ?>
                                <a href="javascript:void(0);" onclick="deleteReview(${review.id})">Xóa</a>
                            <?php } ?>
                        </div>
                    </div>
                `).join("");

                // Hiển thị phân trang
                const paginationContainer = document.getElementById("pagination-container");
                paginationContainer.innerHTML = '';
                for (let i = 1; i <= data.totalPages; i++) {
                    paginationContainer.innerHTML += `<button onclick="loadReviews(${i})">${i}</button>`;
                }
            })
            .catch(error => {
                showToast("Lỗi khi tải đánh giá.", "linear-gradient(to right, #ff5f6d, #ffc371)");
            });
    }

    // Tải đánh giá lần đầu khi trang được mở
    loadReviews();

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
                    <a href="javascript:void(0);" onclick="editReview(${data.id})">Sửa</a> | <a href="javascript:void(0);" onclick="deleteReview(${data.id})">Xóa</a>
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

