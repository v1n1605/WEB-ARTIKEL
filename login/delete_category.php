<?php
include 'function.php';

$id = $_GET['id'] ?? 0;
$query = "DELETE FROM category WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: kategori.php");
    exit();
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Gagal menghapus kategori: " . mysqli_error($conn) . "</div></div>";
}
