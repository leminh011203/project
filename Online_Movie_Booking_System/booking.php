<?php
include('connect.php');
session_start();

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='login.php';  </script>";
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script> alert('Invalid Theater ID.'); window.location.href='index.php'; </script>";
    exit();
}

$theaterid = $_GET['id'];

session_write_close();

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <?php include('header.php'); ?>

    <section id="team" class="team section-bg">
        <div class="container aos-init aos-animate" data-aos="fade-up">
            <div class="section-title">
                <h2>Book Tickets for Theater</h2>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                    <form action="booking.php?id=<?= htmlspecialchars($theaterid) ?>" method="post">
                        <div class="row">
                            <input type="hidden" name="theaterid" value="<?= htmlspecialchars($theaterid) ?>">
                            <div class="col form-group mb-3">
                                <input type="text" class="form-control" name="person" placeholder="Number of People" required="">
                            </div>
                        </div>
                        <div class="col form-group mb-3">

                            <input type="date" class="form-control" name="date" value="<?= $today ?>" required="">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="ticketbook">Book Tickets</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include('footer.php'); ?>

</body>

</html>

<?php

if (isset($_POST['ticketbook'])) {
    $person = $_POST['person'];
    $date = $_POST['date'];
    $theaterid = $_POST['theaterid'];
    $uid = $_SESSION['uid'];

    if (!is_numeric($person) || $person <= 0) {
        echo "<script> alert('Invalid number of people.'); </script>";
        exit();
    }

    $stmt = $con->prepare("INSERT INTO `booking` (`theaterid`, `bookingdate`, `person`, `userid`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $theaterid, $date, $person, $uid);

    if ($stmt->execute()) {
        echo "<script> alert('Ticket booked successfully!'); </script>";
        echo "<script> window.location.href='index.php'; </script>";
    } else {
        echo "<script> alert('Ticket not booked. Error: " . $stmt->error . "'); </script>";
    }

    $stmt->close();
}
?>