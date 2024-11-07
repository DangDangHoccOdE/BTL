<?php
include 'dbh.php';
session_start();


if (isset($_GET['id'])) {
    $movie_id = $_GET['id'];

    // Lấy thông tin phim từ cơ sở dữ liệu
    $sql = "SELECT * FROM movies WHERE mid = '$movie_id'";
    $result = mysqli_query($conn, $sql);
    $movie = mysqli_fetch_assoc($result);

    if (!$movie) {
        die("Phim không tồn tại.");
    }

    // Xử lý form submit để cập nhật thông tin phim
    if (isset($_POST['update'])) {
        $name = $_POST['mname'];
        $rdate = $_POST['release'];
        $genre = $_POST['genre'];
        $rtime = $_POST['rtime'];
        $desc = $_POST['desc'];
        $price = $_POST['price'];
        
        // Cập nhật thông tin phim
        $update_sql = "UPDATE movies SET name = '$name', rdate = '$rdate', genre = '$genre', runtime = '$rtime', decription = '$desc', price = '$price' WHERE mid = '$movie_id'";

        // Kiểm tra nếu có ảnh mới được tải lên
        if ($_FILES['image']['name']) {
            $image = $_FILES['image']['name'];
            $target = "uploads/".basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            $update_sql = "UPDATE movies SET name = '$name', rdate = '$rdate', genre = '$genre', runtime = '$rtime', decription = '$desc', imgpath = '$image' WHERE mid = '$movie_id'";
        }

        // Kiểm tra nếu có video mới được tải lên
        if ($_FILES['video']['name']) {
            $video = $_FILES['video']['name'];
            $target_vid = "video-uploads/".basename($video);
            move_uploaded_file($_FILES['video']['tmp_name'], $target_vid);
            $update_sql = "UPDATE movies SET name = '$name', rdate = '$rdate', genre = '$genre', runtime = '$rtime', decription = '$desc', imgpath = '$image', videopath = '$video' WHERE mid = '$movie_id'";
        }

        // Cập nhật cơ sở dữ liệu
        if (mysqli_query($conn, $update_sql)) {
            echo "Phim đã được cập nhật!";
            header("Location: admin_movies.php");  // Quay lại trang danh sách phim sau khi cập nhật
        } else {
            echo "Lỗi khi cập nhật phim: " . mysqli_error($conn);
        }
    }
} else {
    echo "Không có id phim.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa phim</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Sửa thông tin phim</h2>

    <form action="admin_edit_movie.php?id=<?php echo $movie['mid']; ?>" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="mname">Tên phim:</label>
            <input type="text" class="form-control" name="mname" value="<?php echo $movie['name']; ?>" required>
        </div>

   
        <div class="form-group">
            <label for="release">Năm phát hành:</label>
            <input type="text" class="form-control" name="release" value="<?php echo $movie['rdate']; ?>" required>
        </div>

     
        <div class="form-group">
            <label for="genre">Thể loại:</label>
            <input type="text" class="form-control" name="genre" value="<?php echo $movie['genre']; ?>" required>
        </div>

     
        <div class="form-group">
            <label for="rtime">Thời lượng (phút):</label>
            <input type="number" class="form-control" name="rtime" value="<?php echo $movie['runtime']; ?>" required>
        </div>

        
        <div class="form-group">
            <label for="desc">Mô tả:</label>
            <textarea class="form-control" name="desc" rows="5" required><?php echo $movie['decription']; ?></textarea>
        </div>

        
        <div class="form-group">
            <label for="image">Ảnh poster mới (nếu có):</label>
            <input type="file" class="form-control" name="image">
            <p>Hiện tại: <img src="uploads/<?php echo $movie['imgpath']; ?>" alt="Poster" style="width: 100px;"></p>
        </div>


        <div class="form-group">
            <label for="video">Video mới (nếu có):</label>
            <input type="file" class="form-control" name="video">
            <p>Video hiện tại: <?php echo $movie['videopath']; ?></p>
        </div>

        <div class="form-group">
            <label for="price">Giá:</label>
            <input type="number" class="form-control" name="price" value="<?php echo $movie['price']; ?>" required>
        </div>

        <button type="submit" name="update" class="btn btn-success">Cập nhật</button>
    </form>

    <br>
    <a href="admin_movies.php" class="btn btn-primary">Quay lại danh sách phim</a>
</div>
</body>
</html>
