<?php
require_once "auth_check.php";
require_once __DIR__ . "/../backend/db.php";

$user_id = $_SESSION['user_id'];

// Kullanıcının postları
$stmt = $pdo->prepare("
  SELECT 
    posts.*, 
    categories.name AS category_name
  FROM posts
  LEFT JOIN categories ON posts.category_id = categories.id
  WHERE posts.user_id = :user_id
  ORDER BY posts.created_at DESC
");
$stmt->execute(['user_id' => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | MyBlog</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<!-- ADMIN NAVBAR -->
<header class="admin-navbar">
  <div class="admin-nav-wrapper">

    <div class="admin-logo">
      MyBlog<span class="dot">.</span>
    </div>

    <nav class="admin-links">
      <a href="add_post.php">➕ New Post</a>
      <a href="../index.php" target="_blank">🌍 View Site</a>
      <a href="../auth/logout.php" class="logout">🚪 Logout</a>
    </nav>

  </div>
</header>

<!-- CONTENT -->
<div class="dashboard-container">

  <div class="dashboard-hero">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h1>
    <p>Ready to share your thoughts?</p>

    <a href="add_post.php" class="cta-btn">✍️ Start Writing</a>
  </div>

  <div class="cards">

    <div class="card full">
      <h3>Your Posts</h3>

      <?php if (count($posts) === 0): ?>
        <p class="empty">You haven't written any posts yet ✨</p>
      <?php else: ?>
        <div class="posts-list">

        <?php foreach ($posts as $post): ?>
          <article class="post-item">
  <header class="post-header">
    <h3><?= htmlspecialchars($post['title']) ?></h3>
    <div class="post-meta">
      <span class="post-category">📂 <?= htmlspecialchars($post['category_name'] ?? 'General') ?></span>
      <span class="post-date">🗓 <?= date("d M Y", strtotime($post['created_at'])) ?></span>
    </div>
  </header>

<div class="post-body">
    <div class="post-text">
        <?= nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 250, '...'))) ?>
    </div>
    <?php if (!empty($post['image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="post-image">
    <?php endif; ?>
</div>




  <footer class="post-actions">
    <a href="edit_post.php?id=<?= $post['id'] ?>">✏️ Edit</a>
    <a href="delete_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?')">🗑 Delete</a>
  </footer>
</article>

        <?php endforeach; ?>

        </div>
      <?php endif; ?>
    </div>

  </div>
</div>
<script src="/assets/js/main.js"></script>
</body>
</html>
