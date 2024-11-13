<?php
include '../../dbh.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movieId = $_POST['movie_id'];
    $userId = $_SESSION['id'];
    $rating = $_POST['rating'];
    $reviewContent = $_POST['review_content'];

    if (empty($reviewContent) || empty($rating)) {
        echo json_encode(['message' => 'Vui lòng nhập đầy đủ thông tin.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reviews (movie_id, user_id, rating, review_content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $movieId, $userId, $rating, $reviewContent);
    $stmt->execute();

    $newReviewId = $stmt->insert_id;
    echo json_encode(['id' => $newReviewId, 'message' => 'Đánh giá đã được thêm thành công.']);
}
?>
