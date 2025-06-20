<?php
include 'function.php';

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($description)) {
        $error = "Semua field harus diisi!";
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        $description = mysqli_real_escape_string($conn, $description);

        $query = "INSERT INTO category (name, description) VALUES ('$name', '$description')";
        if (mysqli_query($conn, $query)) {
            $success = "Kategori berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan kategori: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Category</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow rounded">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Add New Category</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control" placeholder="Category name" required minlength="3">
                                <label>Category Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea name="description" class="form-control" placeholder="Description" style="height: 100px" required minlength="5"></textarea>
                                <label>Description</label>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="kategori.php" class="btn btn-secondary">Back</a>
                                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Validasi form Bootstrap
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
