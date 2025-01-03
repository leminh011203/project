<?php
session_start();
include('connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script>window.location.href='../login.php';</script>";
    exit();
}

session_write_close();
if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $delete_query = "DELETE FROM movies WHERE movieid = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param("i", $deleteid);
    if ($stmt->execute()) {
        echo "<script>alert('Deleted Successfully!'); window.location.href='movies.php';</script>";
    } else {
        echo "<script>alert('Failed to delete.');</script>";
    }
}
$movie = null;
if (isset($_GET['editid'])) {
    $editid = $_GET['editid'];
    $edit_query = "SELECT * FROM movies WHERE movieid = ?";
    $stmt = $con->prepare($edit_query);
    $stmt->bind_param("i", $editid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
    } else {
        echo "<script>alert('Movie not found.'); window.location.href='movies.php';</script>";
        exit();
    }
}
function uploadFile($file, $upload_dir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Failed to upload file: ' . $file['error']];
    }
    if ($file['size'] == 0) {
        return ['error' => 'File is empty.'];
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'video/mp4'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'Invalid file type.'];
    }

    $filename = basename($file['name']);
    $file_path = $upload_dir . $filename;

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return ['path' => $filename];
    }

    return ['error' => 'Can\'t move file to server'];
}
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releasedate = $_POST['releasedate'];
    $upload_dir = 'uploads/';

    $image_result = uploadFile($_FILES['image'], $upload_dir);
    $trailer_result = uploadFile($_FILES['trailer'], $upload_dir);

    if (!isset($image_result['error']) && !isset($trailer_result['error'])) {
        $stmt = $con->prepare("INSERT INTO movies (title, description, releasedate, image, trailer) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $releasedate, $image_result['path'], $trailer_result['path']);
        if ($stmt->execute()) {
            $movieid = $stmt->insert_id;

            $catids = $_POST['catid'];
            foreach ($catids as $catid) {
                $stmt = $con->prepare("INSERT INTO category_movie (movieid, catid) VALUES (?, ?)");
                $stmt->bind_param("ii", $movieid, $catid);
                $stmt->execute();
            }
            echo "<script>alert('Added Successfully!'); window.location.href='movies.php';</script>";
        } else {
            echo "<script>alert('Failed to add.');</script>";
        }
    }
}
if (isset($_POST['update'])) {
    $movieid = $_GET['editid'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releasedate = $_POST['releasedate'];
    $upload_dir = 'uploads/';

    $image_result = empty($_FILES['image']['name']) ? ['path' => $_POST['old_image']] : uploadFile($_FILES['image'], $upload_dir);
    $trailer_result = empty($_FILES['trailer']['name']) ? ['path' => $_POST['old_trailer']] : uploadFile($_FILES['trailer'], $upload_dir);

    if (!isset($image_result['error']) && !isset($trailer_result['error'])) {
        $stmt = $con->prepare("UPDATE movies SET title=?, description=?, releasedate=?, image=?, trailer=? WHERE movieid=?");
        $stmt->bind_param("sssssi", $title, $description, $releasedate, $image_result['path'], $trailer_result['path'], $movieid);
        if ($stmt->execute()) {
            $stmt = $con->prepare("DELETE FROM category_movie WHERE movieid = ?");
            $stmt->bind_param("i", $movieid);
            $stmt->execute();

            $catids = $_POST['catid'];
            foreach ($catids as $catid) {
                $stmt = $con->prepare("INSERT INTO category_movie (movieid, catid) VALUES (?, ?)");
                $stmt->bind_param("ii", $movieid, $catid);
                $stmt->execute();
            }
            echo "<script>alert('Updated Successfully!'); window.location.href='movies.php';</script>";
        } else {
            echo "<script>alert('Failed to update.');</script>";
        }
    }
}
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

$sql = "SELECT movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
        FROM movies
        LEFT JOIN category_movie ON movies.movieid = category_movie.movieid
        LEFT JOIN categories ON category_movie.catid = categories.catid
        GROUP BY movies.movieid
        LIMIT $limit OFFSET $offset";
$res = mysqli_query($con, $sql);

$total_query = "SELECT COUNT(*) as total FROM movies";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_movies = $total_row['total'];
$total_pages = ceil($total_movies / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <form action="movies.php<?= isset($movie) ? '?editid=' . $movie['movieid'] : '' ?>" method="post" enctype="multipart/form-data">
                    
                    <div class="form-group mb-4">
                        <select name="catid[]" class="form-control" multiple required>
                            <option value="">Categories</option>
                            <?php
                            $sql = "SELECT * FROM categories";
                            $res = mysqli_query($con, $sql);
                            while ($data = mysqli_fetch_array($res)) {
                                $selected = ($movie && in_array($data['catid'], explode(',', $movie['catid']))) ? "selected" : "";
                                echo "<option value=\"" . htmlspecialchars($data['catid']) . "\" $selected>" . htmlspecialchars($data['catname']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <input type="text" class="form-control" name="title" value="<?= $movie ? htmlspecialchars($movie['title']) : '' ?>" placeholder="Title" required>
                    </div>

                    <div class="form-group mb-4">
                        <textarea class="form-control" name="description" rows="4" placeholder="Description" required><?= $movie ? htmlspecialchars($movie['description']) : '' ?></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <input type="date" class="form-control" name="releasedate" value="<?= $movie ? $movie['releasedate'] : '' ?>" required>
                    </div>

                    <div class="form-group mb-4">
                        Poster:
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <?php if ($movie) {
                            $image_path = "uploads/" . htmlspecialchars($movie['image']);
                            if (file_exists($image_path)) {
                                echo "<br><img src=\"$image_path\" height=\"50\" width=\"50\" alt=\"\">";
                            } else {
                                echo "<br><p style='color:red;'>Image not found: $image_path</p>";
                            }
                        } ?>
                    </div>

                    <div class="form-group mb-4">
                        Trailer:
                        <input type="file" class="form-control" name="trailer" accept="video/*">
                        <?php if ($movie) {
                            $trailer_path = "uploads/" . htmlspecialchars($movie['trailer']);
                            if (file_exists($trailer_path)) {
                                echo "<br><video width=\"100\" height=\"auto\" controls><source src=\"$trailer_path\" type=\"video/mp4\"></video>";
                            } else {
                                echo "<br><p style='color:red;'>Trailer not found: $trailer_path</p>";
                            }
                        } ?>
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-primary" name="<?= $movie ? 'update' : 'add' ?>"><?= $movie ? 'Update' : 'Add Movie' ?></button>
                    </div>

                    <?php if ($movie) { ?>
                        <input type="hidden" name="old_image" value="<?= $movie['image'] ?>">
                        <input type="hidden" name="old_trailer" value="<?= $movie['trailer'] ?>">
                    <?php } ?>
                </form>
            </div>

            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT movies.*, GROUP_CONCAT(categories.catname SEPARATOR ', ') AS catnames
                                FROM movies
                                LEFT JOIN category_movie ON movies.movieid = category_movie.movieid
                                LEFT JOIN categories ON category_movie.catid = categories.catid
                                GROUP BY movies.movieid
                                LIMIT $limit OFFSET $offset";
                        $res = mysqli_query($con, $sql);
                        
                        while ($data = mysqli_fetch_array($res)) {
                            echo "<tr>
                                <td>" . htmlspecialchars($data['movieid'] ?? '') . "</td>
                                <td>" . htmlspecialchars($data['title'] ?? '') . "</td>
                                <td>" . htmlspecialchars($data['catnames'] ?? '') . "</td>
                                <td>";
                            $image_path = "uploads/" . htmlspecialchars($data['image'] ?? '');
                            if (file_exists($image_path)) {
                                echo "<img src=\"$image_path\" height=\"50\" width=\"50\" alt=\"\">";
                            } else {
                                echo "<p style='color:red;'>Image not found: $image_path</p>";
                            }
                            echo "</td>
                                <td>
                                    <a href=\"movies.php?editid=" . htmlspecialchars($data['movieid'] ?? '') . "\" class=\"btn btn-primary\">Edit</a>
                                    <a href=\"movies.php?deleteid=" . htmlspecialchars($data['movieid'] ?? '') . "\" class=\"btn btn-danger\">Delete</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
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