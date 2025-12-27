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

$success = '';
$error = '';

if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $about = $_POST['about'] ?? '';

    // Avatar yükleme
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['avatar']['name']);
        $target = __DIR__ . '/uploads/' . $file_name;

        if (move_uploaded_file($file_tmp, $target)) {
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$file_name, $user_id]);
            $_SESSION['user_avatar'] = $file_name;
        } else {
            $error = "Avatar upload failed.";
        }
    }

    // Name ve About update
    $stmt = $pdo->prepare("UPDATE users SET name = ?, about = ? WHERE id = ?");
    if ($stmt->execute([$name, $about, $user_id])) {
        $success = "Profile updated successfully!";
        $_SESSION['user_name'] = $name;
    } else {
        $error = "Profile update failed!";
    }

    // Refresh user info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/blog-project/assets/css/profile.css">
</head>
<body>

<?php include "../includes/header.php"; ?>

<div class="profile-container">
    <h1>Edit Profile</h1>

    <?php if($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="profile-form">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>

        <label>About Me:</label>
        <textarea name="about"><?= htmlspecialchars($user['about'] ?? '') ?></textarea>

        <label>Profile Avatar:</label>
        <?php if(!empty($user['avatar'])): ?>
            <img src="uploads/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar-preview">
        <?php endif; ?>
        <input type="file" name="avatar">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>

</body>
</html>
