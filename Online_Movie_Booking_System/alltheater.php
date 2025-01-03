<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>

<body>

    <?php
    include('connect.php');

    if (!$con) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    include('header.php');

    $limit = 4; 
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $limit;

    $count_sql = "SELECT COUNT(*) AS total FROM theater INNER JOIN movies ON movies.movieid = theater.movieid";
    $count_res = mysqli_query($con, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_res)['total'];
    $total_pages = ceil($total_rows / $limit);

    $sql = "SELECT theater.*, movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
            FROM theater
            INNER JOIN movies ON movies.movieid = theater.movieid
            INNER JOIN category_movie ON category_movie.movieid = movies.movieid
            INNER JOIN categories ON categories.catid = category_movie.catid
            GROUP BY theater.theaterid
            ORDER BY theater.theaterid DESC
            LIMIT $limit OFFSET $offset";

    $res = mysqli_query($con, $sql);
    ?>
    
    <section id="team" class="team section-bg">
        <div class="container aos-init aos-animate" data-aos="fade-up">

            <div class="section-title">
                <h3><span>Theater</span> Movies</h3>
            </div>

            <div class="row mt-5">
                <?php
                if ($res && mysqli_num_rows($res) > 0) {
                    while ($data = mysqli_fetch_array($res)) {
                ?>
                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                            <div class="member">
                                <div class="member-img">
                                    <?php

                                    $imagePath = "admin/uploads/{$data['image']}";
                                    if (file_exists($imagePath)) {
                                        echo "<img src='{$imagePath}' style='height:250px !important; width:250px !important;' alt='" . htmlspecialchars($data['theater_name']) . "'>";
                                    } else {
                                        echo "<img src='default-image.jpg' style='height:250px !important; width:250px !important;' alt='Image not found.'>";
                                    }
                                    ?>
                                    <div class="social">
                                        <?php

                                        $trailerPath = "admin/uploads/{$data['trailer']}";
                                        if (file_exists($trailerPath)) {
                                            echo "<a href='{$trailerPath}' target='_blank' class='btn btn-primary' style='width:150px;'>Watch Trailer.</a>";
                                        } else {
                                            echo "<span class='btn btn-secondary' style='width:150px;'>No Trailer Available.</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4><?= htmlspecialchars($data['theater_name']) ?></h4>
                                    <h6><?= htmlspecialchars($data['title']) ?> <span><?= htmlspecialchars($data['catnames']) ?></span></h6>
                                    <span><?= htmlspecialchars($data['duration']) ?> minutes</span>
                                    <span><?= htmlspecialchars($data['date']) ?></span>
                                    <span><?= htmlspecialchars($data['location']) ?></span>
                                    <h4>Price: <?= htmlspecialchars($data['price']) ?> VND</h4>
                                    <a href="booking.php?id=<?= $data['theaterid'] ?>" class="btn btn-primary"> Book Now </a>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>There are currently no movie theaters available.</p>";
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