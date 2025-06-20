<?php
include 'function.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['author_id'])) {
    header("Location: login.php");
    exit();
}

$author_id = $_SESSION['author_id'];
$categories = mysqli_query($conn, "SELECT * FROM category");

$success = '';
$error = '';

// Fungsi untuk membersihkan konten dari karakter tak terlihat & emoji
function clean_utf8($string) {
    $string = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $string); // karakter tak terlihat
    $string = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $string); // emotikon
    $string = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $string); // simbol
    $string = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $string); // transport
    $string = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $string);   // misc
    return $string;
}

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, clean_utf8($_POST['title']));
    $date = $_POST['date'];
    $content = mysqli_real_escape_string($conn, clean_utf8($_POST['content']));
    $category_id = isset($_POST['category']) ? (int) $_POST['category'] : 0;

    if (empty($title) || empty($content) || $category_id === 0) {
        $error = "Judul, konten, dan kategori harus diisi.";
    } else {
        // Proses gambar
        $picture = '';
        if (!empty($_FILES['picture']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['picture']['type'], $allowed_types)) {
                $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
                $picture = uniqid('img_', true) . '.' . $ext;
                $target = "uploads/" . $picture;
                move_uploaded_file($_FILES['picture']['tmp_name'], $target);
            } else {
                $error = "File harus berupa gambar (jpg, png, gif, webp).";
            }
        }

        if (!$error) {
            $query = "INSERT INTO article (title, date, content, picture) VALUES ('$title', '$date', '$content', '$picture')";
            if (mysqli_query($conn, $query)) {
                $article_id = mysqli_insert_id($conn);

                // Relasi ke tabel author dan kategori
                mysqli_query($conn, "INSERT INTO article_author (article_id, author_id) VALUES ($article_id, $author_id)");
                mysqli_query($conn, "INSERT INTO article_category (article_id, category_id) VALUES ($article_id, $category_id)");

                $success = "Artikel berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan artikel: " . mysqli_error($conn);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

<head>
    <meta charset="UTF-8">
    <title>Add Article</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow rounded">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Add New Article</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-floating mb-3">
                                <input type="text" name="title" class="form-control" placeholder="Judul Artikel" required>
                                <label>Title</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" name="date" class="form-control" required>
                                <label>Date</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea name="content" id="content" class="form-control" placeholder="Konten artikel" style="height: 150px;"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Picture</label>
                                <input type="file" name="picture" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    <option disabled selected>-- Pilih Kategori --</option>
                                    <?php while ($c = mysqli_fetch_assoc($categories)) : ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="artikel.php" class="btn btn-secondary">Back</a>
                                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // This sample still does not showcase all CKEditor&nbsp;5 features (!)
            // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
            CKEDITOR.ClassicEditor.create(document.getElementById("content"), {
                // https://ckeditor.com/docs/ckeditor5/latest/getting-started/setup/toolbar/toolbar.html#extended-toolbar-configuration-format
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
                // Changing the language of the interface requires loading the language file using the <script> tag.
                // language: 'es',
                list: {
                    properties: {
                        styles: true,
                        startIndex: true,
                        reversed: true
                    }
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
                heading: {
                    options: [{
                            model: 'paragraph',
                            title: 'Paragraph',
                            class: 'ck-heading_paragraph'
                        },
                        {
                            model: 'heading1',
                            view: 'h1',
                            title: 'Heading 1',
                            class: 'ck-heading_heading1'
                        },
                        {
                            model: 'heading2',
                            view: 'h2',
                            title: 'Heading 2',
                            class: 'ck-heading_heading2'
                        },
                        {
                            model: 'heading3',
                            view: 'h3',
                            title: 'Heading 3',
                            class: 'ck-heading_heading3'
                        },
                        {
                            model: 'heading4',
                            view: 'h4',
                            title: 'Heading 4',
                            class: 'ck-heading_heading4'
                        },
                        {
                            model: 'heading5',
                            view: 'h5',
                            title: 'Heading 5',
                            class: 'ck-heading_heading5'
                        },
                        {
                            model: 'heading6',
                            view: 'h6',
                            title: 'Heading 6',
                            class: 'ck-heading_heading6'
                        }
                    ]
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
                placeholder: 'Article Content',
                // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
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
                // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
                fontSize: {
                    options: [10, 12, 14, 'default', 18, 20, 22],
                    supportAllValues: true
                },
                // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
                // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                },
                // Be careful with enabling previews
                // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
                htmlEmbed: {
                    showPreviews: false
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
                link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
                mention: {
                    feeds: [{
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }]
                },
                // The "superbuild" contains more premium features that require additional configuration, disable them below.
                // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
                removePlugins: [
                    // These two are commercial, but you can try them out without registering to a trial.
                    // 'ExportPdf',
                    // 'ExportWord',
                    'AIAssistant',
                    'CKBox',
                    'CKFinder',
                    'EasyImage',
                    // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                    // Storing images as Base64 is usually a very bad idea.
                    // Replace it on production website with other solutions:
                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                    // 'Base64UploadAdapter',
                    'MultiLevelList',
                    'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory',
                    'PresenceList',
                    'Comments',
                    'TrackChanges',
                    'TrackChangesData',
                    'RevisionHistory',
                    'Pagination',
                    'WProofreader',
                    // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                    // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                    'MathType',
                    // The following features require additional license.
                    'SlashCommand',
                    'Template',
                    'DocumentOutline',
                    'FormatPainter',
                    'TableOfContents',
                    'PasteFromOfficeEnhanced',
                    'CaseChange'
                ]
            });
        </script>
    </main>
</body>

</html>