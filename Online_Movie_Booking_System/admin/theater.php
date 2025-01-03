<?php
session_start();
include('connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
    exit();
}

session_write_close();

$edit_data = null;
if (isset($_GET['editid'])) {
    $editid = $_GET['editid'];

    $sql_edit = "SELECT * FROM theater WHERE theaterid = ?";
    $stmt_edit = $con->prepare($sql_edit);
    $stmt_edit->bind_param("i", $editid);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();

    if ($result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
    } else {
        echo "<script> alert('Theater not found!'); window.location.href='theater.php'; </script>";
        exit();
    }
}

if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];

    $stmt_delete = $con->prepare("DELETE FROM theater WHERE theaterid = ?");
    $stmt_delete->bind_param("i", $deleteid);
    if ($stmt_delete->execute()) {
        echo "<script> alert('Theater deleted successfully!'); window.location.href='theater.php'; </script>";
    } else {
        echo "<script> alert('Failed to delete theater.'); </script>";
    }
}

$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) as total FROM theater";
$total_result = $con->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_theaters = $total_row['total'];
$total_pages = ceil($total_theaters / $limit);

$sql = "SELECT theater.*, movies.title, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
        FROM theater
        INNER JOIN movies ON movies.movieid = theater.movieid
        LEFT JOIN category_movie ON category_movie.movieid = movies.movieid
        LEFT JOIN categories ON categories.catid = category_movie.catid
        GROUP BY theater.theaterid
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater</title>
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <form action="theater.php" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-4">
                        <input type="text" class="form-control" name="theater_name" placeholder="Enter theater name" value="<?= isset($edit_data) ? htmlspecialchars($edit_data['theater_name']) : '' ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <select name="movieid" class="form-control" required>
                            <option value="">Select Movie</option>
                            <?php
                            $sql = "SELECT * FROM movies";
                            $res_movies = $con->query($sql);
                            if ($res_movies->num_rows > 0) {
                                while ($data = $res_movies->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($data['movieid']) . "'"
                                        . (isset($edit_data) && $edit_data['movieid'] == $data['movieid'] ? " selected" : "")
                                        . ">" . htmlspecialchars($data['title']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>No Movies found</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <input type="number" class="form-control" name="duration" placeholder="Enter movie duration in minutes" value="<?= isset($edit_data) ? htmlspecialchars($edit_data['duration']) : '' ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <input type="date" class="form-control" name="date" value="<?= isset($edit_data) ? htmlspecialchars($edit_data['date']) : '' ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <input type="number" class="form-control" name="price" placeholder="Enter ticket price" value="<?= isset($edit_data) ? htmlspecialchars($edit_data['price']) : '' ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        <input type="text" class="form-control" name="location" placeholder="Enter location" value="<?= isset($edit_data) ? htmlspecialchars($edit_data['location']) : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="<?= isset($edit_data) ? 'Update Theater' : 'Add Theater' ?>" name="add">
                        <?php if (isset($edit_data)) : ?>
                            <input type="hidden" name="editid" value="<?= htmlspecialchars($edit_data['theaterid']) ?>">
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Theater</th>
                        <th>Movie</th>
                        <th>Categories</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Ticket</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>

                    <?php
                    if ($res->num_rows > 0) {
                        while ($data = $res->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($data['theaterid']) ?></td>
                                <td><?= htmlspecialchars($data['theater_name']) ?></td>
                                <td><?= htmlspecialchars($data['title']) ?></td>
                                <td><?= htmlspecialchars($data['catnames']) ?></td>
                                <td><?= htmlspecialchars($data['date']) ?></td>
                                <td><?= htmlspecialchars($data['duration']) ?> minutes</td>
                                <td><?= htmlspecialchars($data['price']) ?></td>
                                <td><?= htmlspecialchars($data['location']) ?></td>
                                <td>
                                    <a href="theater.php?editid=<?= htmlspecialchars($data['theaterid']) ?>" class="btn btn-primary"> Edit</a>
                                    <a href="theater.php?deleteid=<?= htmlspecialchars($data['theaterid']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this theater?')"> Delete</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="9">No theaters found</td></tr>';
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

<?php
if (isset($_POST['add'])) {
    $theater_name = trim($_POST['theater_name']);
    $movieid = trim($_POST['movieid']);
    $duration = trim($_POST['duration']);
    $date = trim($_POST['date']);
    $price = trim($_POST['price']);
    $location = trim($_POST['location']);
    
    if (empty($theater_name) || empty($movieid) || empty($duration) || empty($price) || empty($date) || empty($location)) {
        echo "<script> alert('All fields are required!'); </script>";
        return;
    }

    if (isset($_POST['editid'])) {
        $stmt = $con->prepare("UPDATE theater SET theater_name=?, duration=?, date=?, price=?, location=?, movieid=? WHERE theaterid=?");
        $stmt->bind_param("ssssssi", $theater_name, $duration, $date, $price, $location, $movieid, $_POST['editid']);
        if ($stmt->execute()) {
            echo "<script> alert('Theater updated successfully!'); window.location.href='theater.php'; </script>";
        } else {
            echo "<script> alert('Failed to update theater.'); </script>";
        }
    } else {
        $stmt = $con->prepare("INSERT INTO theater (theater_name, duration, date, price, location, movieid) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $theater_name, $duration, $date, $price, $location, $movieid);
        if ($stmt->execute()) {
            echo "<script> alert('Theater added successfully!'); window.location.href='theater.php'; </script>";
        } else {
            echo "<script> alert('Failed to add theater.'); </script>";
            echo "<script> console.log('Error: " . $stmt->error . "'); </script>";
        }
    }
}
?>