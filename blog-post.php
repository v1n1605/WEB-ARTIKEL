<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM article WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Ambil category_id dari artikel ini
$cat_query = mysqli_query($conn, "SELECT category_id FROM article_category WHERE article_id = $id");
$cat_id = mysqli_fetch_assoc($cat_query)['category_id'] ?? 0;

// Ambil artikel terkait
$related_query = mysqli_query($conn, "
    SELECT a.id, a.title 
    FROM article a
    JOIN article_category ac ON a.id = ac.article_id
    WHERE ac.category_id = $cat_id AND a.id != $id
    ORDER BY a.date DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link id="theme-style" rel="stylesheet" href="assets/css/theme-1.css">
</head>

<body>

    <header class="header text-center">
        <h1 class="blog-name pt-lg-4 mb-0">
            <a class="no-text-decoration" href="index.php">Warita Astra</a>
        </h1>

        <nav class="navbar navbar-expand-lg navbar-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigation"
                aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navigation" class="collapse navbar-collapse flex-column">
                <div class="profile-section pt-3 pt-lg-0">
                    <img class="profile-image mb-3 rounded-circle mx-auto" src="assets/images/profile.jpg" alt="Profile">
                    <div class="bio mb-3 text-white px-3">
                        Jangan lewatkan update terbaru setiap minggunya — karena setiap tempat punya cerita, dan kami siap
                        menceritakannya untukmu.
                    </div>

                    <!-- Form Pencarian -->
                    <form class="search-form row g-2 px-3 mb-3" action="search.php" method="GET">
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

    <!-- Konten Artikel -->
    <div class="main-wrapper">
        <article class="blog-post px-3 py-5 p-md-5">
            <div class="container single-col-max-width">
                <h2 class="title mb-2"><?php echo htmlspecialchars($data['title']); ?></h2>
                <div class="meta mb-3">
                    <span class="date"><?php echo htmlspecialchars($data['date']); ?></span>
                </div>

                <?php if (!empty($data['picture'])): ?>
                    <div class="mb-4 text-center">
                        <img src="login/uploads/<?php echo htmlspecialchars($data['picture']); ?>" alt="Gambar Artikel"
                            style="width: 12cm; height: 12cm; object-fit: cover; border-radius: 8px;">
                    </div>
                <?php endif; ?>

                <div class="article-content mb-5">
                    <?php echo $data['content']; ?>
                </div>

                <!-- Artikel Terkait -->
                <?php
                echo "<!-- total artikel terkait: " . mysqli_num_rows($related_query) . " -->";
                ?>
                <?php if (mysqli_num_rows($related_query) > 0): ?>
                    <hr class="my-5">
                    <h4 class="mb-3">Artikel Terkait</h4>
                    <ul class="list-unstyled">
                        <?php while ($rel = mysqli_fetch_assoc($related_query)): ?>
                            <li class="mb-2">
                                <a href="blog-post.php?id=<?= $rel['id'] ?>" class="text-decoration-none text-success">
                                    <?= htmlspecialchars($rel['title']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </article>
    </div>
    <footer class="footer text-center py-2 theme-bg-dark">
        <small class="copyright">
            Designed by
            <a href="https://themes.3rdwavemedia.com" target="_blank">Xiaoying Riley</a>
            <br>Copyright @Warita Astra 2025</br>
        </small>
    </footer>
</body>

</html>