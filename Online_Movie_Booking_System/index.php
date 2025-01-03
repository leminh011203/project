<?php
include('connect.php');
include('header.php');

$movie_search = isset($_POST['movie_search']) ? mysqli_real_escape_string($con, $_POST['movie_search']) : '';
$catid = isset($_POST['catid']) ? (int)$_POST['catid'] : '';

$movies_per_page = 4;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $movies_per_page;

$total_sql = "SELECT COUNT(*) AS total FROM movies
              INNER JOIN category_movie ON category_movie.movieid = movies.movieid
              INNER JOIN categories ON categories.catid = category_movie.catid
              WHERE movies.title LIKE ?";
$total_stmt = $con->prepare($total_sql);
$searchTerm = "%$movie_search%";
$total_stmt->bind_param("s", $searchTerm);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_movies = $total_row['total'];
$total_pages = ceil($total_movies / $movies_per_page);

if (isset($_POST['btnSearch'])) {
    if ($catid > 0) {
        $sql = "SELECT movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
                FROM movies
                INNER JOIN category_movie ON category_movie.movieid = movies.movieid
                INNER JOIN categories ON categories.catid = category_movie.catid
                WHERE movies.title LIKE ? AND categories.catid = ?
                GROUP BY movies.movieid
                LIMIT ?, ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("siii", $searchTerm, $catid, $offset, $movies_per_page);
    } else {
        $sql = "SELECT movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
                FROM movies
                INNER JOIN category_movie ON category_movie.movieid = movies.movieid
                INNER JOIN categories ON categories.catid = category_movie.catid
                WHERE movies.title LIKE ?
                GROUP BY movies.movieid
                LIMIT ?, ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sii", $searchTerm, $offset, $movies_per_page);
    }
} else {
    $sql = "SELECT movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
            FROM movies
            INNER JOIN category_movie ON category_movie.movieid = movies.movieid
            INNER JOIN categories ON categories.catid = category_movie.catid
            GROUP BY movies.movieid
            ORDER BY movies.movieid DESC
            LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $offset, $movies_per_page);
}

$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>

<body>

<section id="team" class="team section-bg">
    <div class="container aos-init aos-animate" data-aos="fade-up">
        <div class="section-title">
            <h2>Latest Movies</h2>
            <h3>Our <span>Movies</span></h3>
        </div>

        <form action="index.php" method="post">
            <div class="row">
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="form-group">
                        <input type="text" class="form-control" name="movie_search" placeholder="Search Movie Name" value="<?= htmlspecialchars($movie_search) ?>">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="form-group">
                        <select name="catid" class="form-control">
                            <option value="">Select Category</option>
                            <?php
                            $category_sql = "SELECT * FROM `categories`";
                            $category_res = mysqli_query($con, $category_sql);
                            if (mysqli_num_rows($category_res) > 0) {
                                while ($category = mysqli_fetch_array($category_res)) {
                                    $selected = ($category['catid'] == $catid) ? 'selected' : '';
                                    echo "<option value=\"{$category['catid']}\" $selected>{$category['catname']}</option>";
                                }
                            } else {
                                echo '<option value="">No Category found</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-6 d-flex">
                    <div class="form-group">
                        <input type="submit" name="btnSearch" value="Search" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </form>

        <div class="row mt-5">
            <?php
            if (mysqli_num_rows($res) > 0) {
                while ($data = mysqli_fetch_array($res)) {
                    ?>
                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                        <div class="member">
                            <div class="member-img">
                                <?php
                                $imagePath = "admin/uploads/{$data['image']}";
                                if (file_exists($imagePath)) {
                                    echo "<img src=\"$imagePath\" style=\"height:250px !important; width:250px !important;\" alt=\"\">";
                                } else {
                                    echo "<img src=\"default-image.jpg\" style=\"height:250px !important; width:250px !important;\" alt=\"Image not found\">";
                                }
                                ?>
                                <div class="social">
                                    <a href="admin/uploads/<?= $data['trailer'] ?>" target="_blank" class="btn btn-primary" style="width:150px;">Watch Trailer</a>
                                    <a href="movie_detail.php?id=<?= $data['movieid'] ?>" class="btn btn-info" style="width:150px;">Details</a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4><?= htmlspecialchars($data['title']) ?></h4>
                                <span><?= htmlspecialchars($data['catnames']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No movies found.</p>";
            }
            ?>
        </div>

        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $current_page - 1 ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $current_page + 1 ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

    </div>
</section>

<?php include('footer.php'); ?>

</body>

</html>