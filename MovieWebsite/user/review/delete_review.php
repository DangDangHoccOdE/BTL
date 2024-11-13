<?php
include '../../dbh.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $reviewId = intval($_GET['id']);
    $userId = $_SESSION['id'];
    $userRole = $_SESSION['role'];

    // Kiểm tra quyền xóa bình luận
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ? AND (user_id = ? OR ? = 'admin')");
    $stmt->bind_param("iis", $reviewId, $userId, $userRole);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Xóa bình luận
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $reviewId);
        $stmt->execute();

        echo json_encode(['message' => 'Bình luận đã được xóa.']);
    } else {
        echo json_encode(['message' => 'Bạn không có quyền xóa bình luận này.']);
    }
}
?>
