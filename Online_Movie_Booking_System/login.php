<?php
session_start();
include('connect.php');

if (isset($_SESSION['uid'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $sql = "SELECT * FROM `users` WHERE email = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);

        if (password_verify($password, $data['password'])) {
            $_SESSION['uid'] = $data['userid'];
            $_SESSION['type'] = $data['roteype'];

            if ($remember) {
                $token = bin2hex(random_bytes(16));
                $stmt = $con->prepare("UPDATE users SET remember_token = ? WHERE userid = ?");
                $stmt->bind_param("si", $token, $data['userid']);
                $stmt->execute();
                setcookie('remember_me', $token, time() + (86400 * 30), "/");
            }

            $role = $_SESSION['type'];
            if ($role == 1) {
                $message = 'Admin login successful!';
                header("Location: admin/dashboard.php?message=" . urlencode($message));
                exit;
            } else if ($role == 2) {
                $message = 'User login successful!';
                header("Location: index.php?message=" . urlencode($message));
                exit;
            }
        } else {
            $error_message = 'Incorrect email or password.';
        }
    } else {
        $error_message = 'Email does not exist.';
    }
}

if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $con->prepare("SELECT userid, roteype FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($userid, $role);
    
    if ($stmt->fetch()) {
        $_SESSION['uid'] = $userid;
        $_SESSION['type'] = $role;
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}

session_write_close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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
                <h2>Login Admin / User</h2>
            </div>

            <?php if ($error_message) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                    <center>
                        <form action="login.php" method="post" role="form" class="php-email-form">
                            <div class="row">
                                <div class="col form-group mb-3">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required="">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Your Password" required="">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="login" class="btn btn-primary">Login</button>
                                <a href="register.php" class="btn btn-primary">Register</a>
                            </div>
                        </form>
                    </center>
                </div>
            </div>
        </div>
    </section>
</body>

</html>