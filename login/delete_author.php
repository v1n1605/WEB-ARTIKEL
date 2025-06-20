<?php
include 'function.php';

$id = $_GET['id'] ?? 0;

$query = "DELETE FROM author WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: author.php");
    exit();
} else {
    echo "Gagal menghapus author: " . mysqli_error($conn);
}
?>