<?php
session_start();

include('connect.php');

if (!isset($_SESSION['uid'])) {
  echo "<script> window.location.href='../login.php'; </script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users</title>
</head>

<body>

  <?php include('header.php'); ?>

  <div class="container">

    <div class="row">
      <div class="col-lg-12">
        <table class="table">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Password</th>
            <th>Role Type</th>
            <th>Action</th>
          </tr>

          <?php

          $sql = "SELECT * FROM `users`";
          $stmt = $con->prepare($sql);
          $stmt->execute();
          $res = $stmt->get_result();

          if ($res->num_rows > 0) {
            while ($data = $res->fetch_assoc()) {
          ?>
              <tr>
                <td><?= htmlspecialchars($data['userid']) ?></td>
                <td><?= htmlspecialchars($data['name']) ?></td>
                <td><?= htmlspecialchars($data['email']) ?> </td>
                <td>***** </td>
                <td>
                  <?php
                  echo ($data['roteype'] == 1) ? "ADMIN" : "USER";
                  ?>
                </td>
                <td>

                  <a href="change_password.php?userid=<?= htmlspecialchars($data['userid']) ?>" class="btn btn-warning"> Change Password </a>
                  <a href="viewallusers.php?userid=<?= htmlspecialchars($data['userid']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')"> Delete </a>
                </td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="6">No users found.</td></tr>';
          }
          ?>
        </table>
      </div>
    </div>
  </div>

  <?php include('footer.php'); ?>

</body>

</html>

<?php

if (isset($_GET['userid'])) {

  $userid = mysqli_real_escape_string($con, $_GET['userid']);

  $sql = "DELETE FROM `users` WHERE `userid` = ?";

  $stmt = $con->prepare($sql);
  $stmt->bind_param("i", $userid);

  if ($stmt->execute()) {

    echo "<div class='alert alert-success'>User deleted successfully!</div>";
    echo "<script> setTimeout(function(){ window.location.href='viewallusers.php'; }, 2000); </script>";
  } else {
    echo "<div class='alert alert-danger'>Failed to delete user. Please try again.</div>";
  }
  $stmt->close();
}
?>