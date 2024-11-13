<?php
session_start();
include '../../dbh.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];

// Xử lý yêu cầu xóa phim yêu thích
if (isset($_POST['remove_favorite'])) {
    $movie_id = $_POST['movie_id'];
    $delete_query = "DELETE FROM favorite_movies WHERE user_id = '$id' AND movie_id = '$movie_id'";
    mysqli_query($conn, $delete_query);
    
    // Chuyển hướng lại trang sau khi xóa
    header("Location: favorite_movies.php");
    exit();
}

// Truy vấn danh sách phim yêu thích
$query = "
    SELECT movies.mid, movies.name, movies.imgpath
    FROM movies
    INNER JOIN favorite_movies ON movies.mid = favorite_movies.movie_id
    WHERE favorite_movies.user_id = '$id'
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Yêu Thích</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a href="homepage.php" class="navbar-brand">LetPhich</a>
        <ul class="navbar-nav ml-auto">
            <li class='nav-item'><a href="../../homepage.php" class="nav-link">Trang Chủ</a></li>
            <li class='nav-item'><a href="../auth/logout.php" class="nav-link">Đăng Xuất</a></li>
        </ul>
    </nav>
</header>

<main class="container mt-5">
    <h1>Danh Sách Yêu Thích</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while ($movie = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3">
                    <div class="card mb-4">
                        <img src="uploads/<?php echo $movie['imgpath']; ?>" class="card-img-top" alt="Movie Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $movie['name']; ?></h5>
                            <form action="favorite_movies.php" method="POST">
                                <input type="hidden" name="movie_id" value="<?php echo $movie['mid']; ?>">
                                <button type="submit" name="remove_favorite" class="btn btn-danger">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Không có phim yêu thích nào.</p>
    <?php endif; ?>
</main>

</body>
</html>
