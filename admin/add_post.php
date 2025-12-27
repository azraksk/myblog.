<?php
session_start();
require_once __DIR__ . "/../backend/db.php";

/* LOGIN KONTROL */
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

/* KATEGORİLERİ ÇEK */
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $category_id = $_POST["category_id"];
    $imageName = null;

    // FORM KONTROL
    if ($title === "" || $content === "" || empty($category_id)) {
        $error = "All fields are required.";
    } else {

        /* IMAGE UPLOAD */
        // IMAGE UPLOAD
if (!empty($_FILES["image"]["name"])) {

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $fileName = $_FILES["image"]["name"];
    $tmpName  = $_FILES["image"]["tmp_name"];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        $error = "Only JPG, PNG or WEBP images are allowed.";
    } else {

        $imageName = time() . "_" . uniqid() . "." . $ext;

        // DOĞRU KLASÖR
        $uploadDir = __DIR__ . "/../uploads/";
        $uploadPath = $uploadDir . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($tmpName, $uploadPath)) {
            $error = "Image upload failed.";
        }
    }
}


        // VERİTABANINA KAYIT
        if (empty($error)) {
            $stmt = $pdo->prepare("
                INSERT INTO posts (user_id, title, content, category_id, image)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $_SESSION["user_id"],
                $title,
                $content,
                $category_id,
                $imageName
            ]);

            $success = "Post published successfully 🎉";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Post | MyBlog</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<header class="admin-navbar">
  <div class="admin-nav-wrapper">
    <div class="admin-logo">MyBlog<span class="dot">.</span></div>

    <nav class="admin-links">
      <a href="dashboard.php">Dashboard</a>
      <a href="add_post.php" class="active">New Post</a>
      <a href="../index.php" target="_blank">View Site</a>
      <a href="../auth/logout.php" class="logout">Logout</a>
    </nav>
  </div>
</header>

<main class="form-card">

  <h2>Create New Post</h2>
  <p class="subtitle">Share your thoughts with the world ✨</p>

  <?php if ($error): ?>
    <div class="alert error"><?= $error ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert success"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">

    <label>Title</label>
    <input type="text" name="title" required>

    <label>Category</label>
    <select name="category_id" required>
      <option value="">Select category</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Content</label>
    <textarea name="content" rows="8" placeholder="Write your post..." required></textarea>

    <label>Featured Image</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Publish</button>

  </form>

</main>

</body>
</html>
