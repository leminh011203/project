<?php
include('connect.php');

if (!isset($_GET['id'])) {
    echo "<script> window.location.href='index.php'; </script>";
    exit();
}

$movie_id = $_GET['id'];
$stmt = $con->prepare("SELECT movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames 
                        FROM movies 
                        INNER JOIN category_movie ON category_movie.movieid = movies.movieid 
                        INNER JOIN categories ON categories.catid = category_movie.catid 
                        WHERE movies.movieid = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h1>Movie not found</h1>";
    exit();
}

$movie = $result->fetch_assoc();

$count_stmt = $con->prepare("SELECT COUNT(*) as total_likes FROM likes WHERE movieid = ?");
$count_stmt->bind_param("i", $movie_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_likes = $count_result->fetch_assoc()['total_likes'];

$check_stmt = $con->prepare("SELECT * FROM likes WHERE movieid = ? AND user_ip = ?");
$user_ip = $_SERVER['REMOTE_ADDR'];
$check_stmt->bind_param("is", $movie_id, $user_ip);
$check_stmt->execute();
$liked_result = $check_stmt->get_result();
$is_liked = $liked_result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['title']) ?></title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .movie-details {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px;
        }
        .movie-img {
            max-width: 300px;
            margin-right: 20px;
        }
        .movie-info {
            max-width: 600px;
        }
        .like-btn {
            display: flex;
            align-items: center;
        }
        .like-btn i {
            margin-right: 5px;
        }
        .liked {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container movie-details">
        <div class="movie-img">
            <?php
            $imagePath = "admin/uploads/{$movie['image']}";
            if (file_exists($imagePath)) {
                echo "<img src=\"$imagePath\" class=\"img-fluid\" alt=\"\">";
            } else {
                echo "<img src=\"default-image.jpg\" class=\"img-fluid\" alt=\"Image not found\">";
            }
            ?>
        </div>
        <div class="movie-info">
            <h1><?= htmlspecialchars($movie['title']) ?></h1>
            <p><strong>Release Date:</strong> <?= htmlspecialchars($movie['releasedate']) ?></p>
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['catnames']) ?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($movie['description'])) ?></p>
            <div class="mt-3">
                <button class="btn btn-outline-primary ml-2 like-btn <?= $is_liked ? 'liked' : '' ?>" id="likeBtn">
                    <i class="fas fa-thumbs-up"></i>
                    <span id="likeCount"><?= $total_likes ?></span>
                </button>
                <a href="booking.php?id=<?= $movie_id ?>" class="btn btn-success">Booking</a>
            </div>
            <div class="mt-2">
                <a href="index.php" class="btn btn-primary">Back to Movies</a>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('likeBtn').addEventListener('click', function() {
        const movieId = <?= json_encode($movie_id) ?>;
        fetch(`like.php?id=${movieId}`)
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                document.getElementById('likeCount').innerText = data.total_likes;
                if (data.total_likes > 0) {
                    this.classList.add('liked');
                }
            })
            .catch(error => console.error('Error:', error));
    });
    </script>
</body>

</html>