<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .social .btn {
            font-size: 12px;
            padding: 5px 10px;
            margin-left: 5px;
        }
    </style>
</head>

<body>

    <?php
    include('connect.php');
    include('header.php');

    if (!$con) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    $limit = 4; 
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $limit;

    $count_sql = "SELECT COUNT(*) AS total FROM movies INNER JOIN category_movie ON category_movie.movieid = movies.movieid";
    $count_res = mysqli_query($con, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_res)['total'];
    $total_pages = ceil($total_rows / $limit);
    ?>

    <section id="movies" class="movies section-bg">
        <div class="container aos-init aos-animate" data-aos="fade-up">
            <div class="section-title">
                <h3>All Movies</h3>
            </div>

            <div class="row mt-5">
                <?php

                function displayMovies($offset, $limit)
                {
                    global $con;

                    $stmt = $con->prepare("SELECT movies.*, GROUP_CONCAT(category_movie.catid SEPARATOR ', ') AS catids
                                            FROM movies
                                            INNER JOIN category_movie ON category_movie.movieid = movies.movieid
                                            GROUP BY movies.movieid
                                            ORDER BY movies.releasedate DESC
                                            LIMIT ? OFFSET ?");
                    $stmt->bind_param("ii", $limit, $offset);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($data = $result->fetch_assoc()) {
                            $catids = explode(',', $data['catids']);
                            $catnames = [];
                            foreach ($catids as $catid) {
                                $catname_query = $con->prepare("SELECT catname FROM categories WHERE catid = ?");
                                $catname_query->bind_param("i", $catid);
                                $catname_query->execute();
                                $catname_res = $catname_query->get_result();
                                if ($catname_res->num_rows > 0) {
                                    $catname_row = $catname_res->fetch_assoc();
                                    $catnames[] = htmlspecialchars($catname_row['catname']);
                                }
                                $catname_query->close();
                            }
                            $catnames_str = implode(', ', $catnames);
                ?>
                            <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                                <div class="member">
                                    <div class="member-img">
                                        <?php
                                        $imagePath = "admin/uploads/{$data['image']}";
                                        if (file_exists($imagePath)) {
                                            echo "<img src='{$imagePath}' style='height:250px; width:250px;' alt='" . htmlspecialchars($data['title']) . "'>";
                                        } else {
                                            echo "<img src='default-image.jpg' style='height:250px; width:250px;' alt='Image not found'>";
                                        }
                                        ?>
                                        <div class="social">
                                            <?php
                                            $trailerPath = "admin/uploads/{$data['trailer']}";
                                            if (file_exists($trailerPath)) {
                                                echo "<a href='{$trailerPath}' target='_blank' class='btn btn-primary'>Watch Trailer</a>";
                                                echo "<a href='movie_detail.php?id=" . $data['movieid'] . "' class='btn btn-info'>Detail</a>";
                                            } else {
                                                echo "<span class='btn btn-secondary'>No Trailer Available</span>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="member-info">
                                        <h4><?= htmlspecialchars($data['title']) ?></h4>
                                        <span><?= $catnames_str ?></span>
                                    </div>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo '<p>No movies available.</p>';
                    }

                    $stmt->close();
                }

                displayMovies($offset, $limit);
                ?>

            </div>

            <!-- <div class="section-title mt-5">
                <h3>Popular Movies</h3>
            </div>
            <div class="row">
                <?php
                function displayPopularMovies()
                {
                    global $con;

                    $stmt = $con->prepare("SELECT movies.*, GROUP_CONCAT(category_movie.catid SEPARATOR ', ') AS catids
                                            FROM movies
                                            INNER JOIN category_movie ON category_movie.movieid = movies.movieid
                                            GROUP BY movies.movieid
                                            ORDER BY movies.likes DESC
                                            LIMIT 4");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($data = $result->fetch_assoc()) {
                            $catids = explode(',', $data['catids']);
                            $catnames = [];
                            foreach ($catids as $catid) {
                                $catname_query = $con->prepare("SELECT catname FROM categories WHERE catid = ?");
                                $catname_query->bind_param("i", $catid);
                                $catname_query->execute();
                                $catname_res = $catname_query->get_result();
                                if ($catname_res->num_rows > 0) {
                                    $catname_row = $catname_res->fetch_assoc();
                                    $catnames[] = htmlspecialchars($catname_row['catname']);
                                }
                                $catname_query->close();
                            }
                            $catnames_str = implode(', ', $catnames);
                ?>
                            <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                                <div class="member">
                                    <div class="member-img">
                                        <?php
                                        $imagePath = "admin/uploads/{$data['image']}";
                                        if (file_exists($imagePath)) {
                                            echo "<img src='{$imagePath}' style='height:250px; width:250px;' alt='" . htmlspecialchars($data['title']) . "'>";
                                        } else {
                                            echo "<img src='default-image.jpg' style='height:250px; width:250px;' alt='Image not found'>";
                                        }
                                        ?>
                                        <div class="social">
                                            <?php
                                            $trailerPath = "admin/uploads/{$data['trailer']}";
                                            if (file_exists($trailerPath)) {
                                                echo "<a href='{$trailerPath}' target='_blank' class='btn btn-primary'>Watch Trailer</a>";
                                                echo "<a href='movie_detail.php?id=" . $data['movieid'] . "' class='btn btn-info'>Detail</a>";
                                            } else {
                                                echo "<span class='btn btn-secondary'>No Trailer Available</span>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="member-info">
                                        <h4><?= htmlspecialchars($data['title']) ?></h4>
                                        <span><?= $catnames_str ?></span>
                                    </div>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo '<p>No popular movies available.</p>';
                    }

                    $stmt->close();
                }

                displayPopularMovies();
                ?>

            </div> -->

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