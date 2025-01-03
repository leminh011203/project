<?php
session_start();

include('connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
    exit();
}

session_write_close();

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) as total FROM booking";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

$total_pages = ceil($total_records / $limit);

$sql = "SELECT booking.bookingid, booking.bookingdate, theater.theater_name, 
        theater.duration, theater.price, movies.title, 
        GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames, 
        users.name as username, booking.status
        FROM booking
        INNER JOIN theater ON theater.theaterid = booking.theaterid
        INNER JOIN users ON users.userid = booking.userid
        INNER JOIN movies ON movies.movieid = theater.movieid
        INNER JOIN category_movie ON category_movie.movieid = movies.movieid
        INNER JOIN categories ON categories.catid = category_movie.catid
        GROUP BY booking.bookingid
        LIMIT ? OFFSET ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$res = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings</title>
</head>

<body>

    <?php include('header.php'); ?>

    <div class="container">
        <h2>All Bookings</h2>
        <table class="table">
            <tr>
                <th>#</th>
                <th>Theater Name</th>
                <th>Movie Title - Category</th>
                <th>Booking Date</th>
                <th>Duration</th>
                <th>Price</th>
                <th>User Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            if ($res->num_rows > 0) {
                while ($data = $res->fetch_assoc()) {
            ?>
                    <tr>
                        <td><?= htmlspecialchars($data['bookingid']) ?></td>
                        <td><?= htmlspecialchars($data['theater_name']) ?></td>
                        <td><?= htmlspecialchars($data['title']) ?> - <?= htmlspecialchars($data['catnames']) ?></td>
                        <td><?= htmlspecialchars($data['bookingdate']) ?></td>
                        <td><?= htmlspecialchars($data['duration']) ?> minutes</td>
                        <td><?= htmlspecialchars($data['price']) ?></td>
                        <td><?= htmlspecialchars($data['username']) ?></td>
                        <td><?= ($data['status'] == 0) ? 'Pending' : 'Approved'; ?></td>
                        <td>
                            <a href="viewallbooking.php?bookingid=<?= htmlspecialchars($data['bookingid']) ?>" class="btn btn-primary">Approve</a>
                            <a href="viewallbooking.php?cancelid=<?= htmlspecialchars($data['bookingid']) ?>" class="btn btn-danger">Cancel</a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='9'>No bookings found</td></tr>";
            }
            ?>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item<?= ($i == $page) ? ' active' : '' ?>">
                        <a class="page-link" href="viewallbooking.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <?php include('footer.php'); ?>

</body>

</html>

<?php
if (isset($_GET['bookingid'])) {
    $bookingid = mysqli_real_escape_string($con, $_GET['bookingid']);
    $sql = "UPDATE `booking` SET `status` = 1 WHERE bookingid = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $bookingid);

    if ($stmt->execute()) {
        echo "<script> alert('Booking approved successfully!') </script>";
        echo "<script> window.location.href='viewallbooking.php'; </script>";
    } else {
        echo "<script> alert('Booking approval failed.') </script>";
    }
    $stmt->close();
}

if (isset($_GET['cancelid'])) {
    $bookingid = mysqli_real_escape_string($con, $_GET['cancelid']);
    $sql = "DELETE FROM `booking` WHERE bookingid = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $bookingid);

    if ($stmt->execute()) {
        echo "<script> alert('Booking canceled successfully!') </script>";
        echo "<script> window.location.href='viewallbooking.php'; </script>";
    } else {
        echo "<script> alert('Booking cancellation failed.') </script>";
    }
    $stmt->close();
}
?>