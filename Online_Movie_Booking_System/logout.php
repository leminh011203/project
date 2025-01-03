<?php
session_start();
include('connect.php');

unset($_SESSION['uid']);
unset($_SESSION['type']);

if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, "/");
}

if (isset($_SESSION['uid'])) {
    $userid = $_SESSION['uid'];
    $stmt = $con->prepare("UPDATE users SET remember_token = NULL WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->close();
}

session_destroy();

header("Location: index.php?message=" . urlencode("You have successfully logged out."));
exit;
?>