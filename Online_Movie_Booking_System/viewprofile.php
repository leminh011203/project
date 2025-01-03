<?php
include('connect.php');

session_start();

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='login.php'; </script>";
    exit();
}

$error_message = '';
$success_message = '';

if (isset($_POST['change_password'])) {
    $uid = $_SESSION['uid'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $con->prepare("SELECT password FROM users WHERE userid = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        if (password_verify($current_password, $data['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $con->prepare("UPDATE users SET password = ? WHERE userid = ?");
                $stmt->bind_param("si", $hashed_password, $uid);
                if ($stmt->execute()) {
                    $success_message = 'Password changed successfully! Please log in again.';
                    
                    session_destroy();
                    header("Location: login.php");
                    exit();
                } else {
                    $error_message = 'Failed to change password.';
                }
            } else {
                $error_message = 'New password and confirmation do not match.';
            }
        } else {
            $error_message = 'Current password is incorrect.';
        }
    } else {
        $error_message = 'User not found.';
    }
}

session_write_close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12">
                <h3>User Profile</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Password</th>
                    </tr>

                    <?php
                    $uid = $_SESSION['uid'];

                    $stmt = $con->prepare("SELECT * FROM `users` WHERE `userid` = ?");
                    $stmt->bind_param("i", $uid);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($data = $result->fetch_assoc()) {
                            $password = "******";
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($data['userid']) ?></td>
                                <td><?= htmlspecialchars($data['name']) ?></td>
                                <td><?= htmlspecialchars($data['email']) ?></td>
                                <td><?= $password ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center alert alert-warning'>No user found.</td></tr>";
                    }
                    ?>
                </table>

                <h4>Change Password</h4>
                <?php if ($error_message) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <?php if ($success_message) : ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                <form action="viewprofile.php" method="post">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" id="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

</body>
</html>