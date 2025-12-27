<?php
require_once __DIR__ . "/../backend/db.php";
require_once "auth_check.php";

/* ID kontrolü */
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];
$error = "";

/* Postu sadece sahibiyse getir */
$stmt = $pdo->prepare("
    SELECT * FROM posts 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

/* Güncelleme */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === "" || $content === "") {
        $error = "All fields are required.";
    } else {
        $update = $pdo->prepare("
            UPDATE posts 
            SET title = ?, content = ?
            WHERE id = ? AND user_id = ?
        ");
        $update->execute([$title, $content, $post_id, $user_id]);

        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post | MyBlog</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/edit_post.css">
</head>
<body>

<!-- ADMIN NAVBAR -->
<div class="admin-navbar">
    <div class="admin-nav-wrapper">
        <a href="dashboard.php" class="admin-logo">MyBlog<span>.</span></a>

        <div class="admin-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_post.php">New Post</a>
            <a href="../auth/logout.php" class="logout">Logout</a>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="form-wrapper">
    <div class="form-card">
        <h1>Edit Post ✏️</h1>
        <p class="subtitle">Update your blog content</p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Post Title</label>
            <input
                type="text"
                name="title"
                value="<?= htmlspecialchars($post['title']) ?>"
                required
            >

            <label>Post Content</label>
            <textarea
                name="content"
                rows="12"
                required
            ><?= htmlspecialchars($post['content']) ?></textarea>

            <div class="form-actions">
                <button type="submit">💾 Update Post</button>
                <a href="dashboard.php" class="cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script src="/assets/js/main.js"></script>
</body>
</html>
