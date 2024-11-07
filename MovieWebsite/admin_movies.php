<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
     header("Location: admin_login.php");
     exit();
}
include 'dbh.php';

$movies_per_page = 4;


$sql_total_movies = "SELECT COUNT(*) FROM movies";
$result_total = mysqli_query($conn, $sql_total_movies);
$row = mysqli_fetch_row($result_total);
$total_movies = $row[0];


$total_pages = ceil($total_movies / $movies_per_page);


$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $movies_per_page;


$sql = "SELECT * FROM movies ORDER BY mid DESC LIMIT $start_from, $movies_per_page";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
     <meta charset="UTF-8">
     <title>Quản lý phim</title>
     <link rel="stylesheet" href="user.css" type="text/css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
     <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
     <script>
          document.addEventListener('DOMContentLoaded', function() {
               const requiredFields = document.querySelectorAll('input[required], textarea[required], select[required]');
               requiredFields.forEach(field => {

                    field.addEventListener('invalid', function() {

                         field.setCustomValidity('Bạn quên nhập này.');
                    });

                    field.addEventListener('input', function() {

                         field.setCustomValidity('');
                    });
               });
          });

          $(document).ready(function() {
               $("#myInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#myTable tr").filter(function() {
                         $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
               });
          });
     </script>


     <style>
          #toggleForm {
               display: none;
          }

          #addMovieForm {
               display: none;
               margin-top: 20px;
          }

          #toggleForm:checked+label+#addMovieForm {
               display: block;
          }
     </style>
</head>

<body>
     <header>
          <div class="container-fluid">
               <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                    <a href="homepage.php" class="navbar-brand"> <img src="images/logo.png" alt=""> </a>
                    <span class="navbar-text">LetPhich</span>
                    <ul class="navbar-nav">
                         <li class="nav-item"> <a href="admin_dashboard.php" class="nav-link">Admin</a></li>
                         <li class="nav-item"> <a href="admin_logout.php" class="nav-link">Đăng xuất</a> </li>
                    </ul>
               </nav>
               <br><br>
               <div class="container">
                    <button type="button" class="alert alert-success" data-toggle="modal" data-target="#myModal">
                         Thêm phim mới
                    </button>
                    <div class="modal" id="myModal">
                         <div class="modal-dialog">
                              <div class="modal-content">
                                   <div class="modal-header">
                                        <h4 class="modal-title">Nhập thông tin phim</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                   </div>
                                   <div class="modal-body">
                                        <form class="" action="admin-control.php" method="POST" enctype="multipart/form-data">

                                             <input type="text" class="form-control" placeholder="Tên" name="mname" value="" required><br>
                                             <input type="text" class="form-control" placeholder="Năm phát hành" name="release" value="" required>
                                             <br>
                                             <input type="text" class="form-control" placeholder="Thể loại" name="genre" value="" required>
                                             <br>
                                             <input type="number" class="form-control" placeholder="Thời lượng (phút)" name="rtime" value="" required>
                                             <br>
                                             <input type="text" class="form-control" placeholder="Mô tả..." name="desc" value="" required>
                                             <br>

                                             <div class="row">
                                                  <div class="col">
                                                       <table>
                                                            <tr>
                                                                 <td> <label for=""><b>Tải lên Poster : </b></label> </td>
                                                                 <td>
                                                                      <div class="">
                                                                           <input type="hidden" name="size" value="100000">

                                                                           <input type="file" name="image" value="">
                                                                      </div>
                                                                 </td>
                                                            </tr>
                                                       </table>
                                                  </div>
                                                  <div class="col">
                                                       <table>
                                                            <tr>
                                                                 <td> <label for=""><b>Tải lên Phim : </b></label> </td>
                                                                 <td>
                                                                      <div class="">
                                                                           <input type="hidden" name="size" value="30000000">

                                                                           <input type="file" name="video" value="">
                                                                      </div>
                                                                 </td>
                                                            </tr>
                                                       </table>

                                                  </div>
                                                  <br>
                                             </div>
                                             <input type="number" class="form-control" placeholder="Giá phim" name="desc" value="">
                                             <br>
                                             <br><br>
                                             <div class="signupbutton">
                                                  <input type="submit" class="btn btn-success btn-lg" name="upload" value="Thêm">
                                             </div>
                                        </form>
                                   </div>
                                   <!-- <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                                   </div> -->

                              </div>
                         </div>
                    </div>
                    <div class='col'>
                    <form action='admin_search.php' method='POST'>
                      <select  name='option' style='padding:5px;'>
                        <option selected>Tìm theo</option>
                        <option value='name'>Tên</option>
                        <option value='genre'>Thể loại</option>
                        <option value='rdate'>Năm phát hành</option>
                      </select>
                      <input type='text' placeholder='Nhập...' style='margin-left:10px;margin-top:10px;padding:5px;' name='textoption'>

                      <input type='submit' name='submit' class='btn btn-success' style='display:inline;width:100px;margin-left:20px;margin-right:20px;margin-top:5px;' value='Tìm kiếm'/></h4>
                    </form>
                  </div>
               </div>
               <h4>Danh sách phim</h4>
               <div class="table-container">
                    <table class="table table-striped">
                         <thead>
                              <tr>
                                   <th>Tên phim</th>
                                   <th>Poster</th>
                                   <th>Năm phát hành</th>
                                   <th>Thể loại</th>
                                   <th>Thời lượng</th>
                                   <th>Giá</th>
                                   <th>Hành động</th>
                              </tr>
                         </thead>
                         <tbody>
                              <?php while ($movie = mysqli_fetch_assoc($result)) { ?>
                                   <tr>
                                        <td><?php echo $movie['name']; ?></td>
                                        <td><img src="uploads/<?php echo $movie['imgpath']; ?>" alt="Poster" style="width: 80px; height: auto;"></td>
                                        <td><?php echo $movie['rdate']; ?></td>
                                        <td><?php echo $movie['genre']; ?></td>
                                        <td><?php echo $movie['runtime']; ?> phút</td>
                                        <td><?php echo $movie['price']; ?> VNĐ</td>
                                        <td>
                                             <a href="admin_edit_movie.php?id=<?php echo $movie['mid']; ?>" class="alert alert-warning">Sửa</a>
                                             <a href="admin_delete_movie.php?id=<?php echo $movie['mid']; ?>" onclick="return confirm('Bạn có chắc muốn xóa phim này?');" class="alert alert-danger">Xóa</a>
                                        </td>
                                   </tr>
                              <?php } ?>
                         </tbody>
                    </table>
               </div>
               <nav>
                    <ul class="pagination">
                         <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                              <a class="page-link" href="admin_movies.php?page=<?php echo $page - 1; ?>">Trước</a>
                         </li>

                         <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                              <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                   <a class="page-link" href="admin_movies.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                              </li>
                         <?php } ?>

                         <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                              <a class="page-link" href="admin_movies.php?page=<?php echo $page + 1; ?>">Sau</a>
                         </li>
                    </ul>
               </nav>
          </div>
     </header>


     <footer class="page-footer font-small blue">

          <div class="footer-copyright text-center py-3">© 2024 Copyright:
               <a href="">anthony@gmail.com</a>
          </div>

     </footer>

</body>

</html>