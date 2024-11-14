<?php
include('dbh.php');
$user_logged_in = isset($_SESSION['id']);

include 'header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LetPhich - Trang Chủ</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Khám Phá Thế Giới Phim</h1>
        <p class="lead">Trải nghiệm những bộ phim đặc sắc nhất tại LetPhich</p>
    </div>
</div>
   <!-- Filter Form -->
<div class="container mt-5">
<form id="filterForm" class="form-row align-items-center">
        <!-- Thể loại -->
        <div class="col-md-4 mb-3">
            <label for="genre" class="form-label">Thể loại</label>
            <select name="genre" id="genre" class="form-control custom-select">
                <option value="">Tất cả</option>
                <?php
                    $query_genres = "SELECT id, name FROM categories";
                    $result_genres = $conn->query($query_genres);
                    if ($result_genres->num_rows > 0) {
                        while ($genre = $result_genres->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($genre['id']) . '">' . htmlspecialchars($genre['name']) . '</option>';
                        }
                    }
                ?>
            </select>
        </div>

        <!-- Giá tiền -->
        <div class="col-md-4 mb-3">
            <label for="price" class="form-label">Giá tiền</label>
            <select name="price" id="price" class="form-control custom-select">
                <option value="">Tất cả</option>
                <option value="1">Dưới 100.000 VND</option>
                <option value="2">100.000 - 200.000 VND</option>
                <option value="3">Trên 200.000 VND</option>
            </select>
        </div>

        <div class="col-md-4 mb-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>
</div>

</div>

<!-- Main Content -->
<div class="container mt-4">
    <!-- Phim Thịnh Hành -->
    <h2 class="section-title">Phim Thịnh Hành</h2>
    <div class="row">
    <?php
        // Xử lý lọc phim theo thể loại và giá tiền
        $genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';
        $price_filter = isset($_GET['price']) ? $_GET['price'] : '';

        $query_movies = "SELECT movies.* FROM movies 
                         LEFT JOIN movie_categories ON movies.mid = movie_categories.movie_id";
        
        if ($genre_filter) {
            $query_movies .= " WHERE movie_categories.category_id = '" . $conn->real_escape_string($genre_filter) . "'";
        } else {
            $query_movies .= " WHERE 1";
        }

        if ($price_filter) {
            if ($price_filter == "1") {
                $query_movies .= " AND movies.price < 100000";
            } elseif ($price_filter == "2") {
                $query_movies .= " AND movies.price BETWEEN 100000 AND 200000";
            } elseif ($price_filter == "3") {
                $query_movies .= " AND movies.price > 200000";
            }
        }

        $query_movies .= " LIMIT 6"; // Giới hạn lấy 6 phim
        $result_movies = $conn->query($query_movies);

        if ($result_movies->num_rows > 0):
            while ($movie = $result_movies->fetch_assoc()):
    ?>
                <div class="col-md-4 movie-card">
                    <div class="card">
                        <img src="uploads/<?php echo htmlspecialchars($movie['imgpath']); ?>" class="card-img-top" alt="Movie Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['name']); ?></h5>
                            <p class="card-text">Giá: <?php echo number_format($movie['price'], 0, ',', '.'); ?> VND</p>
                            <a href="movie_preview.php?movie_id=<?php echo $movie['mid']; ?>" class="btn btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
    <?php
            endwhile;
        else:
    ?>
        <p>Không có phim nào để hiển thị.</p>
    <?php endif; ?>
    </div>

    <!-- Phim Hot -->
<h2 class="section-title">Phim Hot</h2>
<div class="row">
<?php
    // Truy vấn lấy 6 phim có lượt xem cao nhất
    $query_movies = "SELECT * FROM movies ORDER BY viewers DESC LIMIT 6";
    $result_movies = $conn->query($query_movies);

    if ($result_movies->num_rows > 0):
        while ($movie = $result_movies->fetch_assoc()):
?>
            <div class="col-md-4 movie-card">
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($movie['imgpath']); ?>" class="card-img-top" alt="Movie Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($movie['name']); ?></h5>
                        <p class="card-text">Giá: <?php echo number_format($movie['price'], 0, ',', '.'); ?> VND</p>
                        <p class="card-text">Lượt xem: <?php echo number_format($movie['viewers']); ?></p>
                        <a href="movie_preview.php?movie_id=<?php echo $movie['mid']; ?>" class="btn btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
<?php
        endwhile;
    else:
?>
    <p>Không có phim nào để hiển thị.</p>
<?php endif; ?>
</div>

</div>

<!-- Pagination -->
<div id="pagination" class="container mt-4"></div>

<?php
    include 'footer.php';
?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
    $(document).ready(function() {
        // Hàm tải phim
        function loadMovies(page = 1) {
            $.ajax({
                url: "fetch_movies.php",
                type: "GET",
                data: {
                    genre: $('#genre').val(),
                    price: $('#price').val(),
                    page: page
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    const movies = data.movies;
                    const totalPages = data.total_pages;
                    const currentPage = data.current_page;

                    let html = '';
                    if (movies.length > 0) {
                        movies.forEach(movie => {
                            html += `
                                <div class="col-md-4 movie-card">
                                    <div class="card">
                                        <img src="uploads/${movie.imgpath}" class="card-img-top" alt="Movie Image">
                                        <div class="card-body">
                                            <h5 class="card-title">${movie.name}</h5>
                                            <p class="card-text">Giá: ${parseInt(movie.price).toLocaleString()} VND</p>
                                            <a href="movie_preview.php?movie_id=${movie.mid}" class="btn btn-primary">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>`;
                        });
                    } else {
                        html = '<p>Không có phim nào để hiển thị.</p>';
                    }

                    $('#movieList').html(html);

                    // Tạo nút phân trang
                    let paginationHTML = '';
                    for (let i = 1; i <= totalPages; i++) {
                        paginationHTML += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1" onclick="loadMovies(${i})">${i}</button>`;
                    }
                    $('#pagination').html(paginationHTML);
                }
            });
        }

        // Gọi hàm loadMovies khi tải trang
        loadMovies();

        // Lọc khi người dùng submit form
        $('#filterForm').submit(function(event) {
            event.preventDefault();
            loadMovies();
        });
    });
</script>