<?php
include 'function.php';

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($nickname) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $cek = mysqli_query($conn, "SELECT * FROM author WHERE email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $query = "INSERT INTO author (nickname, email, password) VALUES ('$nickname', '$email', '$password')";
            if (mysqli_query($conn, $query)) {
                $success = "Author berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan author: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Author</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<script>
    // Bootstrap form validation
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

<body class="bg-light">
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow rounded">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Add New Author</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nickname" class="form-label">Nickname</label>
                                <input type="text" name="nickname" class="form-control" required minlength="3">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="author.php" class="btn btn-secondary">Back</a>
                                <button type="submit" name="submit" class="btn btn-primary">Save Author</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/scripts.js"></script>
</body>

</html>