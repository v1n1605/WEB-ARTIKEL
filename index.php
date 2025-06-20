<?php
$host = "localhost:3307";
$user = "root";
$pass = "kzoinucd";
$db = "dbcms";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Warita Astra Jalan-jalan</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Blog Template">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">
    <link rel="shortcut icon" href="location.png">
    <script defer src="assets/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="assets/css/theme-1.css">
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

                    <div class="bio mb-3">Temukan pesona dunia lewat setiap langkah bersama kami! Blog ini hadir sebagai sumber inspirasi bagi kamu yang haus akan petualangan dan keindahan tempat-tempat wisata, baik di dalam negeri maupun mancanegara. Dari berita terkini seputar destinasi hits hingga ulasan mendalam tentang surga tersembunyi yang jarang dijamah, kami siap membawamu menjelajah lewat tulisan yang informatif dan menggugah rasa ingin tahu.
                        <br><br>Jangan lewatkan update terbaru setiap minggunya — karena setiap tempat punya cerita, dan kami siap menceritakannya untukmu.<br>
                    </div>
                </div>
        </nav>

    </header>

    <div class="main-wrapper">

        <section class="cta-section theme-bg-light py-5">
            <div class="container text-center single-col-max-width">
                <h2 class="heading">Menjelajah Nusantara dan Dunia Bersama: Di Balik Setiap Tempat, Ada Cerita</h2>
                <div class="intro">Selamat Datang di Blog Kami!</div>

                <form class="search-form row g-2 g-lg-2 align-items-center" action="search.php" method="GET">
                    <div class="col-md-6">
                        <input type="text" name="query" class="form-control" placeholder="Cari judul berita...">
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </form>

            </div>
        </section>

        <section class="blog-list px-3 py-5 p-md-5">
            <div class="container single-col-max-width">
                <?php
                $query = "SELECT * FROM article ORDER BY id DESC";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $picture = !empty($row['picture']) ? 'login/uploads/' . $row['picture'] : 'assets/images/blog/default.jpg';

                    echo '<div class="item mb-5">';
                    echo '  <div class="row g-3 g-xl-0">';
                    echo '      <div class="col-12 col-xl-3">';
                    echo '          <img class="img-fluid post-thumb" src="' . htmlspecialchars($picture) . '" alt="image">';
                    echo '      </div>';
                    echo '      <div class="col">';
                    echo '          <h3 class="title mb-1"><a class="text-link" href="blog-post.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';
                    echo '          <div class="meta mb-1"><span class="date">' . htmlspecialchars($row['date']) . '</span></div>';
                    echo '          <div class="intro">' . substr(strip_tags($row['content']), 0, 200) . '...</div>';
                    echo '          <a class="text-link" href="blog-post.php?id=' . $row['id'] . '">Read more &rarr;</a>';
                    echo '      </div>';
                    echo '  </div>';
                    echo '</div>';
                }

                ?>
            </div>
        </section>

        <footer class="footer text-center py-2 theme-bg-dark">
            <small class="copyright">
                Designed with <i class="fas fa-heart" style="color: #fb866a;"></i> by
                <a href="https://themes.3rdwavemedia.com" target="_blank">Xiaoying Riley</a>
                <br>Copyright @Warita Astra 2025</br>
            </small>
        </footer>

    </div>

    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>