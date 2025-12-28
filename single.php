<?php
session_start();
require_once __DIR__ . "/backend/db.php";

$BASE_URL = '/blog-project';

// ID kontrolü
if (!isset($_GET['id'])) {
    header("Location: posts.php");
    exit;
}

$id = (int) $_GET['id'];

// Postu çek
$stmt = $pdo->prepare("
    SELECT 
        posts.*, 
        categories.name AS category_name,
        users.name AS author_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    LEFT JOIN users ON posts.user_id = users.id
    WHERE posts.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/single.css?">

</head>
<body>

<?php include_once __DIR__ . "/includes/header.php"; ?>

<div class="post-wrapper">

    <!-- Başlık -->
    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>

    <!-- Resim -->
    <?php if (!empty($post['image'])): ?>
        <div class="post-image">
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>
    <?php endif; ?>

    <!-- Yazı kutusu -->
    <div class="post-content-box">
        <div class="post-meta">
            <span><?= htmlspecialchars($post['category_name']) ?></span>
            <span> | <?= date("d M Y", strtotime($post['created_at'])) ?></span>
        </div>

        <p class="post-author">
            By <strong><?= htmlspecialchars($post['author_name']) ?></strong>
        </p>

        <div class="post-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
    </div>

</div>

<script src="/assets/js/main.js"></script>
</body>
</html>
