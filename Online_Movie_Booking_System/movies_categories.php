<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories and Movies</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .social .btn {
            font-size: 12px;
            padding: 5px 10px;
            margin-left: 5px;
        }
        .category {
            margin-bottom: 30px;
        }
        .category h2 {
            text-align: center;
            margin-bottom: 15px;
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

    $categories_result = mysqli_query($con, "SELECT * FROM categories");

    while ($category = mysqli_fetch_assoc($categories_result)):
        ?>
        <div class="category">
            <h2><?= htmlspecialchars($category['catname']) ?></h2>
            <div class="row">
                <?php
                $catid = $category['catid'];
                $movies_result = mysqli_query($con, "SELECT movies.* FROM movies
                                                      INNER JOIN category_movie ON category_movie.movieid = movies.movieid
                                                      WHERE category_movie.catid = $catid");

                if ($movies_result && mysqli_num_rows($movies_result) > 0) {
                    while ($movie = mysqli_fetch_assoc($movies_result)): ?>
                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                            <div class="member">
                                <div class="member-img">
                                    <?php
                                    $imagePath = "admin/uploads/{$movie['image']}";
                                    if (file_exists($imagePath)) {
                                        echo "<img src='{$imagePath}' style='height:250px; width:250px;' alt='" . htmlspecialchars($movie['title']) . "'>";
                                    } else {
                                        echo "<img src='default-image.jpg' style='height:250px; width:250px;' alt='Image not found'>";
                                    }
                                    ?>
                                    <div class="social">
                                        <?php
                                        $trailerPath = "admin/uploads/{$movie['trailer']}";
                                        if (file_exists($trailerPath)) {
                                            echo "<a href='{$trailerPath}' target='_blank' class='btn btn-primary'>Watch Trailer</a>";
                                            echo "<a href='movie_detail.php?id=" . $movie['movieid'] . "' class='btn btn-info'>Detail</a>";
                                        } else {
                                            echo "<span class='btn btn-secondary'>No Trailer Available</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4><?= htmlspecialchars($movie['title']) ?></h4>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                } else {
                    echo '<p>No movies available in this category.</p>';
                }
                ?>
            </div>
        </div>
    <?php endwhile; ?>

    <?php include('footer.php'); ?>

</body>

</html>