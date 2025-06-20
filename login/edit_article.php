<?php

include 'function.php';

// Cek login
if (!isset($_SESSION['author_id'])) {
    header("Location: login.php");
    exit();
}

$author_id = $_SESSION['author_id'];
$id = $_GET['id'] ?? 0;

$article_q = mysqli_query($conn, "SELECT * FROM article WHERE id = $id");
$article = mysqli_fetch_assoc($article_q);
if (!$article) die("Artikel tidak ditemukan.");

// Ambil kategori terhubung
$category_q = mysqli_query($conn, "SELECT category_id FROM article_category WHERE article_id = $id");
$selected_category = mysqli_fetch_assoc($category_q)['category_id'] ?? null;

// Ambil semua kategori
$categories = mysqli_query($conn, "SELECT * FROM category");

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date = $_POST['date'];
    $content = $_POST['content']; // tidak perlu escape karena CKEditor menghasilkan HTML
    $category_id = $_POST['category'];

    $picture = $article['picture'];
    if (!empty($_FILES['picture']['name'])) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $picture = uniqid('img_', true) . '.' . $ext;
        $target = "uploads/" . $picture;
        move_uploaded_file($_FILES['picture']['tmp_name'], $target);
    }

    $query = "UPDATE article SET title='$title', date='$date', content='$content', picture='$picture' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        mysqli_query($conn, "DELETE FROM article_author WHERE article_id=$id");
        mysqli_query($conn, "DELETE FROM article_category WHERE article_id=$id");

        mysqli_query($conn, "INSERT INTO article_author (article_id, author_id) VALUES ($id, $author_id)");
        mysqli_query($conn, "INSERT INTO article_category (article_id, category_id) VALUES ($id, $category_id)");

        $success = "Artikel berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui artikel: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Article</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
</head>
<body class="bg-light">
<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark"><h4>Edit Article</h4></div>
                <div class="card-body">
                    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-floating mb-3">
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($article['title']) ?>" required>
                            <label>Title</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="date" name="date" class="form-control" value="<?= $article['date'] ?>" required>
                            <label>Date</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea id="content" name="content"><?= htmlspecialchars($article['content']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Picture</label>
                            <input type="file" name="picture" class="form-control">
                            <?php if ($article['picture']): ?>
                                <img src="uploads/<?= $article['picture'] ?>" width="120" class="mt-2 rounded">
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <?php while ($c = mysqli_fetch_assoc($categories)) : ?>
                                    <option value="<?= $c['id'] ?>" <?= ($c['id'] == $selected_category) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="artikel.php" class="btn btn-secondary">Back</a>
                            <button type="submit" name="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- CKEditor Init -->
<script>
CKEDITOR.ClassicEditor.create(document.getElementById("content"), {
    toolbar: {
        items: [
            'exportPDF', 'exportWord', '|',
            'findAndReplace', 'selectAll', '|',
            'heading', '|',
            'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
            'bulletedList', 'numberedList', 'todoList', '|',
            'outdent', 'indent', '|',
            'undo', 'redo',
            '-',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
            'alignment', '|',
            'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
            'specialCharacters', 'horizontalLine', 'pageBreak', '|',
            'textPartLanguage', '|',
            'sourceEditing'
        ],
        shouldNotGroupWhenFull: true
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        }
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
            { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
        ]
    },
    placeholder: 'Article Content',
    fontFamily: {
        options: [
            'default',
            'Arial, Helvetica, sans-serif',
            'Courier New, Courier, monospace',
            'Georgia, serif',
            'Lucida Sans Unicode, Lucida Grande, sans-serif',
            'Tahoma, Geneva, sans-serif',
            'Times New Roman, Times, serif',
            'Trebuchet MS, Helvetica, sans-serif',
            'Verdana, Geneva, sans-serif'
        ],
        supportAllValues: true
    },
    fontSize: {
        options: [10, 12, 14, 'default', 18, 20, 22],
        supportAllValues: true
    },
    htmlSupport: {
        allow: [{ name: /.*/, attributes: true, classes: true, styles: true }]
    },
    htmlEmbed: { showPreviews: false },
    link: {
        decorators: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
            toggleDownloadable: {
                mode: 'manual',
                label: 'Downloadable',
                attributes: { download: 'file' }
            }
        }
    },
    mention: {
        feeds: [
            {
                marker: '@',
                feed: ['@artikel', '@wisata', '@penulis'],
                minimumCharacters: 1
            }
        ]
    },
    removePlugins: [
        'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
        'MultiLevelList', 'RealTimeCollaborativeComments',
        'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory',
        'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
        'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
        'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
        'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
    ]
});
</script>
</body>
</html>
