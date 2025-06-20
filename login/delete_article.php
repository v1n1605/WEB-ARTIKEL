<?php
include 'function.php';

$id = $_GET['id'] ?? 0;

// Hapus relasi dulu
mysqli_query($conn, "DELETE FROM article_author WHERE article_id = $id");
mysqli_query($conn, "DELETE FROM article_category WHERE article_id = $id");

// Hapus artikel
$query = "DELETE FROM article WHERE id = $id";
if (mysqli_query($conn, $query)) {
    header("Location: artikel.php");
    exit();
} else {
    echo "<div class='alert alert-danger'>Gagal menghapus artikel: " . mysqli_error($conn) . "</div>";
}
