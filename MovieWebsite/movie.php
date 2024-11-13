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

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie_id = intval($_POST['movie_id']);
    $rating = intval($_POST['rating']);
    $review_content = $conn->real_escape_string($_POST['review_content']);
    $image_path = "";

    // Kiểm tra tính hợp lệ của số sao (phải nằm trong khoảng 1-5)
    if ($rating < 1 || $rating > 5) {
        echo "Vui lòng chọn số sao từ 1 đến 5.";
        exit;
    }

    // Kiểm tra xem nội dung hoặc hình ảnh có được nhập hay không
    if (empty($review_content) && (!isset($_FILES['review_image']) || $_FILES['review_image']['error'] != UPLOAD_ERR_OK)) {
        echo "Vui lòng nhập nội dung đánh giá hoặc tải lên hình ảnh.";
        exit;
    }

    // Xử lý hình ảnh nếu người dùng tải lên
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['review_image']['name']);
        $image_path = 'uploads/reviews/' . $image_name;

        // Di chuyển file vào thư mục uploads/reviews
        move_uploaded_file($_FILES['review_image']['tmp_name'], $image_path);
    }

    // Chèn dữ liệu đánh giá vào cơ sở dữ liệu
    $sql = "INSERT INTO reviews (movie_id, rating, review_content, image_path) VALUES ($movie_id, $rating, '$review_content', '$image_path')";

    if ($conn->query($sql) === TRUE) {
        echo "Đánh giá đã được gửi thành công!";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['name']); ?> - Xem Phim</title>
    <style>
        /* CSS cơ bản cho giao diện */
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
    </style>
</head>
<body>
<div class="container">
    <div class="movie-detail">
        <h1 class="movie-title"><?php echo htmlspecialchars($movie['name']); ?> </h1>
        <img src="uploads/<?php echo $movie['imgpath']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['name']); ?>">
        <p><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
        
        <!-- Hiển thị video -->
        <iframe width="100%" height="400" src="<?php echo $movie['videopath']; ?>" frameborder="0" allowfullscreen></iframe>
    </div>

    <!-- Form đánh giá phim -->
    <div class="review-form">
        <h3>Đánh giá phim</h3>
        <form action="submit_review.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">

            <label for="rating">Số sao (1-5):</label>
            <select name="rating" id="rating" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>

            <label for="review_content">Nội dung đánh giá:</label>
            <textarea name="review_content" id="review_content" rows="4" required></textarea>

            <label for="review_image">Thêm hình ảnh (tuỳ chọn):</label>
            <input type="file" name="review_image" id="review_image" accept="image/*">

            <button type="submit">Gửi đánh giá</button>
        </form>
    </div>
</div>

<script>
    function watchMovie(movieId) {
        alert("Bắt đầu xem phim ngay!");
        // Ở đây bạn có thể thêm logic để chuyển hướng hoặc xử lý khi người dùng nhấn vào nút xem phim
    }
</script>
</body>
</html>


