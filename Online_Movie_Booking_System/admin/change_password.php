<?php
session_start();

include('connect.php');

if (!isset($_SESSION['uid'])) {
  echo "<script> window.location.href='../login.php'; </script>";
  exit();
}

if (isset($_GET['userid'])) {
  $userid = mysqli_real_escape_string($con, $_GET['userid']);

  $sql = "SELECT * FROM `users` WHERE `userid` = ?";
  $stmt = $con->prepare($sql);
  $stmt->bind_param("i", $userid);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
    $data = $res->fetch_assoc();
  } else {
    echo "<script> alert('User not found!'); window.location.href='viewallusers.php'; </script>";
    exit();
  }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
  $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
  if ($new_password != $confirm_password) {
    echo "<script> alert('Passwords do not match!'); </script>";
  } else {

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE `users` SET `password` = ? WHERE `userid` = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $userid);
    if ($stmt->execute()) {
      echo "<script> alert('Password updated successfully!'); window.location.href='viewallusers.php'; </script>";
    } else {
      echo "<script> alert('Failed to update password. Please try again.'); </script>";
    }
    $stmt->close();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
</head>

<body>

  <?php include('header.php'); ?>

  <div class="container">
    <h2>Change Password for <?= htmlspecialchars($data['name']) ?></h2>
    <form action="change_password.php?userid=<?= $userid ?>" method="post">
      <div class="form-group">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Update Password</button>
      </div>
    </form>
  </div>

  <?php include('footer.php'); ?>

</body>

</html>