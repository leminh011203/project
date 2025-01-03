<?php
include('connect.php');

if (!isset($_GET['id'])) {
    echo "<script> window.location.href='index.php'; </script>";
    exit();
}

$movie_id = $_GET['id'];
$user_ip = $_SERVER['REMOTE_ADDR'];

$check_stmt = $con->prepare("SELECT * FROM likes WHERE movieid = ? AND user_ip = ?");
$check_stmt->bind_param("is", $movie_id, $user_ip);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $stmt = $con->prepare("INSERT INTO likes (movieid, user_ip) VALUES (?, ?)");
    $stmt->bind_param("is", $movie_id, $user_ip);
    $stmt->execute();

    $message = "You liked the movie!";
} else {
    $message = "You have already liked this movie!";
}

$count_stmt = $con->prepare("SELECT COUNT(*) as total_likes FROM likes WHERE movieid = ?");
$count_stmt->bind_param("i", $movie_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_likes = $count_result->fetch_assoc()['total_likes'];

echo json_encode(['message' => $message, 'total_likes' => $total_likes]);
?>