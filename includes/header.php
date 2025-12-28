<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$BASE_URL = '/blog-project'; // Proje kök dizin yolu
?>

<link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/header.css">

<header class="site-header">
    <div class="logo">
        <a href="<?= $BASE_URL ?>/index.php">MyBlog<span class="dot">.</span></a>
    </div>

    <nav class="nav-center">
        <a href="<?= $BASE_URL ?>/index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php' ? 'active' : '' ?>">Home</a>
        <a href="<?= $BASE_URL ?>/post.php" class="<?= basename($_SERVER['PHP_SELF'])=='post.php' ? 'active' : '' ?>">Posts</a>
        <a href="<?= $BASE_URL ?>/categories.php" class="<?= basename($_SERVER['PHP_SELF'])=='categories.php' ? 'active' : '' ?>">Categories</a>
        <a href="<?= $BASE_URL ?>/about.php" class="<?= basename($_SERVER['PHP_SELF'])=='about.php' ? 'active' : '' ?>">About</a>
    </nav>

    <div class="nav-right">
        <?php if (isset($_SESSION["user_id"])): ?>
            <div class="profile-wrapper" id="profileToggle">
                <img src="<?= !empty($_SESSION['user_avatar']) ? $BASE_URL . '/admin/uploads/' . $_SESSION['user_avatar'] : $BASE_URL . '/assets/img/default-avatar.png' ?>" class="profile-avatar" alt="Profile">
                <div class="profile-dropdown" id="profileMenu">
                    <div class="profile-name"><?= htmlspecialchars($_SESSION["user_name"]) ?></div>
                    <a href="<?= $BASE_URL ?>/admin/profile.php">Profile</a>
                    <a href="<?= $BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                    <a href="<?= $BASE_URL ?>/auth/logout.php" class="logout">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= $BASE_URL ?>/auth/login.php" class="login-btn">Admin Login</a>
        <?php endif; ?>
    </div>
</header>

<script>
const profile = document.getElementById("profileToggle");
const menu = document.getElementById("profileMenu");

if(profile){
    profile.addEventListener("click", function(e){
        e.stopPropagation();
        menu.classList.toggle("active");
    });

    document.addEventListener("click", function(){
        menu.classList.remove("active");
    });
}
</script>