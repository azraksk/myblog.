<?php
session_start();
require_once "backend/db.php";


// Kategorileri çek
$categories = $pdo->query("
  SELECT * FROM categories ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Categories | MyBlog</title>

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/categories.css">
</head>

<body>

<?php include "includes/header.php"; ?>

<section class="categories-hero">
  <h1>Categories</h1>
  <p>Explore thoughts, ideas and stories — organized by what matters.</p>
</section>

<main class="categories-grid">

<?php foreach ($categories as $cat): ?>

  <article class="category-card">
    <div class="category-header">
      <span class="category-icon">📁</span>
      <h2><?= htmlspecialchars($cat['name']) ?></h2>
    </div>

    <p class="category-desc">
      Posts related to <?= htmlspecialchars($cat['name']) ?>.
    </p>

    <ul class="category-posts">
      <?php
      $posts = $pdo->prepare("
        SELECT id, title 
        FROM posts 
        WHERE category_id = ?
        ORDER BY created_at DESC
        LIMIT 5
      ");
      $posts->execute([$cat['id']]);

      foreach ($posts as $post):
      ?>
        <li>
          <a href="single.php?id=<?= $post['id'] ?>">
            <?= htmlspecialchars($post['title']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </article>

<?php endforeach; ?>

</main>

<footer class="footer">
  <div class="footer-content">
    <p><strong>MyBlog.</strong></p>
    <p>Email: myblog@email.com</p>
    <p>Instagram: @myblog</p>
    <p>© 2025 MyBlog</p>
  </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>