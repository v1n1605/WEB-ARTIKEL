<?php
$host = "localhost:3307";
$user = "root";
$pass = "kzoinucd";
$db = "dbcms"; // Sesuaikan dengan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "SELECT a.id, a.title, a.date, a.content, a.picture, au.nickname AS author_name
        FROM article a
        LEFT JOIN article_author aa ON a.id = aa.article_id
        LEFT JOIN author au ON aa.author_id = au.id
        ORDER BY a.date DESC"; // Mengambil semua artikel dan mengurutkannya berdasarkan tanggal terbaru

$result = $conn->query($sql);
?>
