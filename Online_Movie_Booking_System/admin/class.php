<?php
session_start();

include('connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script>window.location.href='../login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Page</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container">
        <h1>Class Page</h1>
        
        <div style="text-align: center; font-size: 24px; color: green; margin-top: 20px;">
            Coming Soon
        </div>

    </div>

    <?php include('footer.php'); ?>
</body>
</html>