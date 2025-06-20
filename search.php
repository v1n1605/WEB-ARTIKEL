<?php
include 'koneksi.php';

$query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;

// SQL dasar + join ke article_category
$sql = "SELECT DISTINCT a.* FROM article a
        LEFT JOIN article_category ac ON a.id = ac.article_id
        WHERE 1";

// Tambahkan filter pencarian judul jika query tidak kosong
if (!empty($query)) {
    $sql .= " AND a.title LIKE '%$query%'";
}

// Tambahkan filter kategori jika kategori dipilih
if ($category > 0) {
    $sql .= " AND ac.category_id = $category";
}

$sql .= " ORDER BY a.id DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian</title>
    <link rel="stylesheet" href="assets/css/theme-1.css">
</head>

<body>

    <header class="header text-center">
        <h1 class="blog-name pt-lg-4 mb-0"><a class="no-text-decoration" href="index.php">Warita Astra</a></h1>
        <nav class="navbar navbar-expand-lg navbar-dark">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navigation" class="collapse navbar-collapse flex-column">
                <div class="profile-section pt-3 pt-lg-0">
                    <img class="profile-image mb-3 rounded-circle mx-auto" src="assets/images/profile.jpg" alt="image">

                    <div class="bio mb-3">Jangan lewatkan update terbaru setiap minggunya — karena setiap tempat punya cerita, dan kami siap menceritakannya untukmu.<br>

                    </div>
                    <!-- Form Pencarian berdasarkan judul dan kategori -->
                    <form class="search-form row g-2 p-3" action="search.php" method="GET">
                        <div class="col-12 mb-2">
                            <input type="text" name="query" class="form-control" placeholder="Cari judul artikel...">
                        </div>
                        <div class="col-12 mb-2">
                            <select name="category" class="form-select">
                                <option value="">-- Semua Kategori --</option>
                                <?php
                                $kategori = mysqli_query($conn, "SELECT * FROM category ORDER BY name ASC");
                                while ($k = mysqli_fetch_assoc($kategori)) {
                                    echo '<option value="' . $k['id'] . '">' . htmlspecialchars($k['name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Cari</button>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary w-100" onclick="history.back()">Back</button>
                        </div>

                    </form>

                </div>
            </div>
        </nav>

    </header>

    <div class="main-wrapper">
        <section class="blog-list px-3 py-5 p-md-5">
            <div class="container single-col-max-width">
                <h2>Hasil pencarian</h2>

                <?php if (empty($query) && $category > 0): ?>
                    <p>Menampilkan semua artikel dalam kategori terpilih.</p>
                <?php elseif (!empty($query) && $category > 0): ?>
                    <p>Mencari judul "<strong><?php echo htmlspecialchars($query); ?></strong>" dalam kategori tertentu.</p>
                <?php elseif (!empty($query)): ?>
                    <p>Mencari judul "<strong><?php echo htmlspecialchars($query); ?></strong>" di semua kategori.</p>
                <?php endif; ?>

                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $picture = !empty($row['picture']) ? 'login/uploads/' . $row['picture'] : 'assets/images/blog/default.jpg';
                        echo '<div class="item mb-5">';
                        echo '  <div class="row g-3 g-xl-0">';
                        echo '      <div class="col-3">';
                        echo '          <img class="img-fluid post-thumb" src="' . htmlspecialchars($picture) . '" alt="Thumbnail">';
                        echo '      </div>';
                        echo '      <div class="col">';
                        echo '          <h3 class="title mb-1"><a class="text-link" href="blog-post.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';
                        echo '          <div class="meta mb-1"><span class="date">' . htmlspecialchars($row['date']) . '</span></div>';
                        echo '          <div class="intro">' . substr(strip_tags($row['content']), 0, 150) . '...</div>';
                        echo '          <a class="text-link" href="blog-post.php?id=' . $row['id'] . '">Read more &rarr;</a>';
                        echo '      </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Tidak ditemukan artikel yang sesuai.';
                    if (!empty($query)) {
                        echo ' Judul: "<strong>' . htmlspecialchars($query) . '</strong>"';
                    }
                    if ($category > 0) {
                        echo ' dalam kategori yang dipilih.';
                    }
                    echo '</p>';
                }
                ?>
            </div>
        </section>
    </div>
    
</body>


</html>