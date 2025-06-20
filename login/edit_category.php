<?php
include 'function.php';

$id = $_GET['id'] ?? 0;
$query = "SELECT * FROM category WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Kategori tidak ditemukan.");
}

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "UPDATE category SET name='$name', description='$description' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $success = "Kategori berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui kategori: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow rounded">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Edit Category</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" placeholder="Category name" required>
                                <label>Category Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea name="description" class="form-control" placeholder="Description" style="height: 100px"><?= htmlspecialchars($data['description']) ?></textarea>
                                <label>Description</label>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="kategori.php" class="btn btn-secondary">Back</a>
                                <button type="submit" name="submit" class="btn btn-warning">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
