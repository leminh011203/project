<?php

session_start();

include('connect.php');

if (isset($_POST['register'])) {


    $name = mysqli_real_escape_string($con, $_POST['name']);

    $email = mysqli_real_escape_string($con, $_POST['email']);

    $password = $_POST['password'];


    $sql_check_email = "SELECT * FROM `users` WHERE email = ?";

    $stmt = mysqli_prepare($con, $sql_check_email);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        $error_message = 'Email is already in use. Please choose another email.';
    } else {


        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $sql = "INSERT INTO `users` (`name`, `email`, `password`, `roteype`) VALUES (?, ?, ?, '2')";

        $stmt = mysqli_prepare($con, $sql);

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {

            echo "<script>alert('Registration successful! Please log in.'); window.location.href='login.php';</script>";
        } else {

            $error_message = 'Registration failed. Please try again!';
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>
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

    <section id="team" class="team section-bg">

        <div class="container aos-init aos-animate" data-aos="fade-up">

            <div class="section-title">

                <h2>Register for Booking Ticket</h2>

            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                    <form action="register.php" method="post" role="form" class="php-email-form">
                        <div class="row">
                            <div class="col form-group mb-3">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Your Name" required="">
                            </div>
                        </div>
                        <div class="col form-group mb-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required="">
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Your Password" required="">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="register">Register</button>
                        </div>

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </section>

</body>

</html>