<?php
include('connect.php');
session_start();

if (!isset($_SESSION['uid'])) {

    echo "<script> window.location.href='../login.php'; </script>";

    exit();
}

session_write_close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include('header.php'); ?>

    <div class="container text-center">
        <h4>Welcome to Admin Dashboard!!</h4>

        <div class="row">

            <div class="col-lg-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <h5>CATEGORIES</h5>
                            <?php
                            $stmt = $con->prepare("SELECT COUNT(catid) AS category FROM `categories`");
                            if ($stmt) {
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $catdata = $res->fetch_assoc();
                                echo "<h6>" . htmlspecialchars($catdata['category'] ?? 0) . "</h6>";
                            } else {
                                echo "<h6>Error fetching data</h6>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <h5>MOVIES</h5>
                            <?php
                            $stmt = $con->prepare("SELECT COUNT(movieid) AS total_movies FROM `movies`");
                            if ($stmt) {
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $moviedata = $res->fetch_assoc();
                                echo "<h6>" . htmlspecialchars($moviedata['total_movies'] ?? 0) . "</h6>";
                            } else {
                                echo "<h6>Error fetching data</h6>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <h5>THEATER</h5>
                            <?php
                            $stmt = $con->prepare("SELECT COUNT(theaterid) AS total_theater FROM `theater`");
                            if ($stmt) {
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $theaterdata = $res->fetch_assoc();
                                echo "<h6>" . htmlspecialchars($theaterdata['total_theater'] ?? 0) . "</h6>";
                            } else {
                                echo "<h6>Error fetching data</h6>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <h5>BOOKING</h5>
                            <?php
                            $stmt = $con->prepare("SELECT COUNT(bookingid) AS total_booking FROM `booking` WHERE status = 1");
                            if ($stmt) {
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $bookingdata = $res->fetch_assoc();
                                echo "<h6>" . htmlspecialchars($bookingdata['total_booking'] ?? 0) . "</h6>";
                            } else {
                                echo "<h6>Error fetching data</h6>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <h5>USERS</h5>
                            <?php
                            $stmt = $con->prepare("SELECT COUNT(userid) AS total_users FROM `users` WHERE roteype = 2");
                            if ($stmt) {
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $userdata = $res->fetch_assoc();
                                echo "<h6>" . htmlspecialchars($userdata['total_users'] ?? 0) . "</h6>";
                            } else {
                                echo "<h6>Error fetching data</h6>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-text">
                            <h5>SALES</h5>
                            <?php
                            $stmt = $con->prepare("SELECT COALESCE(SUM(theater.price), 0) AS total_sale 
                                                    FROM booking 
                                                    INNER JOIN theater ON theater.theaterid = booking.theaterid 
                                                    WHERE booking.status = 1");
                            if ($stmt) {
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $salesdata = $res->fetch_assoc();
                                echo "<h6>" . htmlspecialchars($salesdata['total_sale'] ?? 0) . "</h6>";
                            } else {
                                echo "<h6>Error fetching data</h6>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include('footer.php'); ?>

</body>

</html>