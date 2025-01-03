<?php
include('connect.php');
session_start();

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='login.php'; </script>";
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
    <title>User Booking</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <?php include('header.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12">
                <h3>Your Bookings</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Theater Name</th>
                            <th>Movie</th>
                            <th>Show Date</th>
                            <th>Duration</th>
                            <th>Ticket Price</th>
                            <th>Booking Date</th>
                            <th>Location</th>
                            <th>User</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $uid = $_SESSION['uid'];

                        $stmt = $con->prepare("SELECT 
                        booking.bookingid, 
                        booking.bookingdate, 
                        booking.person, 
                        theater.theater_name, 
                        theater.duration, 
                        theater.price, 
                        theater.location, 
                        movies.title, 
                        GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames,
                        users.name as 'username',
                        booking.status
                    FROM booking
                    INNER JOIN theater ON theater.theaterid = booking.theaterid
                    INNER JOIN users ON users.userid = booking.userid
                    INNER JOIN movies ON movies.movieid = theater.movieid
                    INNER JOIN category_movie ON category_movie.movieid = movies.movieid
                    INNER JOIN categories ON categories.catid = category_movie.catid
                    WHERE booking.userid = ?
                    GROUP BY booking.bookingid");
                        $stmt->bind_param("i", $uid);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($data = $result->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['bookingid']) ?></td>
                                    <td><?= htmlspecialchars($data['theater_name']) ?></td>
                                    <td><?= htmlspecialchars($data['title']) ?> - <?= htmlspecialchars($data['catnames']) ?></td>
                                    <td><?= htmlspecialchars($data['bookingdate']) ?></td>
                                    <td><?= htmlspecialchars($data['duration']) ?> minutes</td>
                                    <td><?= htmlspecialchars($data['price']) ?> VND</td>
                                    <td><?= htmlspecialchars($data['bookingdate']) ?> </td>
                                    <td><?= htmlspecialchars($data['location']) ?></td>
                                    <td><?= htmlspecialchars($data['username']) ?></td>
                                    <td>
                                        <?php
                                        if ($data['status'] == 0) {
                                            echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                        } else {
                                            echo "<span class='badge bg-success'>Approved</span>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center alert alert-warning'>No bookings found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

</body>

</html>