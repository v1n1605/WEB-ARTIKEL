<?php
include 'function.php';

$id = $_GET['id'] ?? 0;

// Ambil data author yang akan diedit
$query = "SELECT * FROM author WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Author tidak ditemukan.");
}

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "UPDATE author SET nickname='$nickname', email='$email', password='$password' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $success = "Author berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui author: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Author</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow rounded">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Edit Author</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="nickname" class="form-label">Nickname</label>
                                <input type="text" name="nickname" class="form-control" value="<?= htmlspecialchars($data['nickname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($data['password']) ?>" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-warning">Update</button>
                            <a href="author.php" class="btn btn-secondary">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
