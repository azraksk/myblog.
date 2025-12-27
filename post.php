<?php
session_start();
require_once __DIR__ . "/backend/db.php";

$stmt = $pdo->query("
  SELECT 
    posts.*,
    categories.name AS category_name,
    users.name AS author_name
  FROM posts
  LEFT JOIN categories ON posts.category_id = categories.id
  LEFT JOIN users ON posts.user_id = users.id
  ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Posts | MyBlog</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/posts.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<div class="posts-bg"></div>

<main class="posts-wrapper">
    <h1 class="page-title">Latest Stories</h1>
    <p class="page-subtitle">Thoughts, feelings and words worth sharing.</p>

    <section class="posts-track">

        <!-- SABİT KARTLAR -->
        <article class="post-card">
            <img src="assets/images/first.jpg" alt="">
            <h3>Finding yourself through writing</h3>
            <p>Writing is more than words. It’s a way to listen to your own thoughts.</p>
        </article>

        <article class="post-card">
            <img src="assets/images/soft.jpg" alt="">
            <h3>Soft mornings & loud minds</h3>
            <p>Some mornings are quiet, yet your mind refuses to be.</p>
        </article>

        <article class="post-card">
            <img src="assets/images/blog.jpg" alt="">
            <h3>Why we need personal blogs</h3>
            <p>Because not everything belongs on social media.</p>
        </article>

        <!-- DİNAMİK POSTLAR -->
        <?php foreach ($posts as $post): ?>
        <article class="post-card">

            <div class="post-meta">
                <span class="category">
                    <?= htmlspecialchars($post['category_name'] ?? 'General') ?>
                </span>
                <span class="date">
                    | <?= date("d M Y", strtotime($post['created_at'])) ?>
                </span>
            </div>

            <?php if (!empty($post['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="">
            <?php else: ?>
                <img src="assets/images/default.jpg" alt="">
            <?php endif; ?>

            <h3><?= htmlspecialchars($post['title']) ?></h3>

            <p class="excerpt">
                <?= nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 160, '...'))) ?>
            </p>

            <p class="post-author">
                By <strong><?= htmlspecialchars($post['author_name']) ?></strong>
            </p>

            <a href="single.php?id=<?= $post['id'] ?>" class="read-more">
                Read more →
            </a>

        </article>
        <?php endforeach; ?>

    </section>

    <section class="cta-box">
        <h2>Ready to start writing?</h2>
        <p>Share your thoughts, stories and ideas with the world.</p>
        <a href="auth/login.php" class="cta-btn">Sign up now</a>
    </section>
</main>

<footer class="footer">
  <div class="footer-content">
    <p><strong>MyBlog.</strong></p>
    <p>Email: myblog@email.com</p>
    <p>Instagram: @myblog</p>
    <p>© 2025 MyBlog</p>
  </div>
</footer>

</body>
</html>
