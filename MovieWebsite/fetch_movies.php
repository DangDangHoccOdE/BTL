<?php
include('dbh.php');

$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$movies_per_page = 6;
$offset = ($page - 1) * $movies_per_page;

$query_movies = "SELECT movies.* FROM movies LEFT JOIN movie_categories ON movies.mid = movie_categories.movie_id";
$conditions = [];

// Áp dụng điều kiện lọc nếu có
if ($genre) {
    $conditions[] = "movie_categories.category_id = '" . $conn->real_escape_string($genre) . "'";
}
if ($price) {
    if ($price == "1") {
        $conditions[] = "movies.price < 100000";
    } elseif ($price == "2") {
        $conditions[] = "movies.price BETWEEN 100000 AND 200000";
    } elseif ($price == "3") {
        $conditions[] = "movies.price > 200000";
    }
}

if (!empty($conditions)) {
    $query_movies .= " WHERE " . implode(" AND ", $conditions);
}

// Thực hiện giới hạn theo trang
$query_movies .= " LIMIT $offset, $movies_per_page";
$result_movies = $conn->query($query_movies);
$movies = [];

if ($result_movies->num_rows > 0) {
    while ($movie = $result_movies->fetch_assoc()) {
        $movies[] = $movie;
    }
}

// Tính tổng số trang
$total_movies_query = "SELECT COUNT(*) as total FROM movies";
if (!empty($conditions)) {
    $total_movies_query .= " WHERE " . implode(" AND ", $conditions);
}
$total_result = $conn->query($total_movies_query);
$total_movies = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_movies / $movies_per_page);

// Trả về kết quả dưới dạng JSON
echo json_encode([
    'movies' => $movies,
    'total_pages' => $total_pages,
    'current_page' => $page
]);
?>
