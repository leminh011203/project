<?php
include('connect.php');
session_start();

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
    exit();
}

$message = '';
if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $sql = "DELETE FROM `categories` WHERE catid = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $deleteid);

    if (mysqli_stmt_execute($stmt)) {
        $message = 'Category deleted successfully!';
    } else {
        $message = 'Failed to delete category: ' . mysqli_error($con);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $catname = $_POST['catname'];
        $sql = "INSERT INTO `categories` (catname) VALUES (?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', $catname);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Category added successfully!';
        } else {
            $message = 'Failed to add category: ' . mysqli_error($con);
        }
    } elseif (isset($_POST['update'])) {
        $catid = $_POST['catid'];
        $catname = $_POST['catname'];
        $sql = "UPDATE `categories` SET catname = ? WHERE catid = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $catname, $catid);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Category updated successfully!';
        } else {
            $message = 'Failed to update category: ' . mysqli_error($con);
        }
    }
}
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) as total FROM `categories`";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

$total_pages = ceil($total_records / $limit);

$sql = "SELECT * FROM `categories` LIMIT $limit OFFSET $offset";
$res = mysqli_query($con, $sql);

$editdata = null;
if (isset($_GET['editid'])) {
    $editid = $_GET['editid'];
    $edit_query = "SELECT * FROM `categories` WHERE catid = ?";
    $stmt = mysqli_prepare($con, $edit_query);
    mysqli_stmt_bind_param($stmt, 'i', $editid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result) {
        $editdata = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container">
        <?php if ($message) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-6">
                <form action="categories.php" method="post">
                    <?php if (isset($editdata)) : ?>
                        <div class="form-group mb-4">
                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($editdata['catid']) ?>" name="catid">
                        </div>
                    <?php endif; ?>
                    <div class="form-group mb-4">
                        <input type="text" class="form-control" name="catname" value="<?= isset($editdata) ? htmlspecialchars($editdata['catname']) : '' ?>" placeholder="Enter category name" required>
                    </div>
                    <div class="form-group">
                        <?php if (isset($editdata)) : ?>
                            <input type="submit" class="btn btn-info" value="Update" name="update">
                        <?php else : ?>
                            <input type="submit" class="btn btn-success" value="Add" name="add">
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    if (mysqli_num_rows($res) > 0) {
                        while ($data = mysqli_fetch_array($res)) {
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($data['catid']) ?></td>
                                <td><?= htmlspecialchars($data['catname']) ?></td>
                                <td>
                                    <a href="categories.php?editid=<?= $data['catid'] ?>" class="btn btn-primary">Edit</a>
                                    <a href="categories.php?deleteid=<?= $data['catid'] ?>" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="3">No categories found.</td></tr>';
                    }
                    ?>
                </table>

                <nav>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>