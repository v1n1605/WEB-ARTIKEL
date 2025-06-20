<?php
session_start();
// Koneksi ke database
$host = "localhost:3307";
$user = "root";
$pass = "kzoinucd";
$db   = "dbcms";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Proses login
if (isset($_POST['btn_login'])) {
    $data_email    = mysqli_real_escape_string($conn, $_POST['email']);
    $data_password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek email & password dari tabel author
    $sql = "SELECT * FROM author WHERE email = '$data_email' AND password = '$data_password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $author = mysqli_fetch_assoc($result);

        // Simpan data ke session
        $_SESSION["email"]     = $author['email'];
        $_SESSION["author_id"] = $author['id'];

        // Redirect ke dashboard
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['login_error'] = "Email atau password salah!";
        header('Location: login.php');
        exit();
    }
}
?>
