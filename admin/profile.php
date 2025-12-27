<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) { exit("User not found."); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="/blog-project/assets/css/profile.css">
</head>
<body>

<?php include "../includes/header.php"; ?>

<div class="profile-container">
    <h1>My Profile</h1>

    <div class="profile-box">
        <div class="avatar-section">
            <img src="<?= !empty($user['avatar']) ? 'uploads/' . htmlspecialchars($user['avatar']) : '/blog-project/assets/img/default-avatar.png' ?>" class="avatar" alt="Avatar">
        </div>
        <div class="info-section">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>About Me:</strong> <?= htmlspecialchars($user['about'] ?? '-') ?></p>
            <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
        </div>
    </div>
</div>

</body>
</html>
