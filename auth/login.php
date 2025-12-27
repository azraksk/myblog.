<?php
session_set_cookie_params([
    'path' => '/',   // 🔥 EN ÖNEMLİ SATIR
]);
session_start();

require_once __DIR__ . "/../backend/db.php";

$error = "";
$success = "";

/* LOGIN */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_email"] = $user["email"];
    $_SESSION["user_name"] = $user["name"];
    header("Location: ../admin/dashboard.php");
    exit;
  } else {
    $error = "Wrong email or password.";
  }
}

/* SIGN UP */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signup"])) {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  if ($name === "" || $email === "" || $password === "") {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email address.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } else {
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
      $error = "Email already exists.";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);

      $insert = $pdo->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
      );
      $insert->execute([$name, $email, $hashed]);

      $success = "Account created. You can log in.";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | MyBlog</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="auth-wrapper">

  <h1 class="logo">
    <a href="../index.php">MyBlog<span>.</span></a>
  </h1>

  <?php if ($error): ?>
    <p class="error"><?= $error ?></p>
  <?php endif; ?>

  <?php if ($success): ?>
    <p class="success"><?= $success ?></p>
  <?php endif; ?>

  <!-- LOGIN FORM -->
  <form method="POST" id="loginForm" class="auth-form">
    <h2>Welcome back</h2>

    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="login">Login</button>

    <p class="switch">
      Don’t have an account?
      <span onclick="showSignup()">Sign up</span>
    </p>
  </form>

  <!-- SIGN UP FORM -->
  <form method="POST" id="signupForm" class="auth-form hidden">
  <h2>Create account</h2>

  <input type="text" name="name" placeholder="Full name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>

  <button type="submit" name="signup">Create Account</button>

  <p class="switch">
    Already have an account?
    <span onclick="showLogin()">Login</span>
  </p>
  </form>

</div>

<script>
function showSignup() {
  document.getElementById("loginForm").classList.add("hidden");
  document.getElementById("signupForm").classList.remove("hidden");
}

function showLogin() {
  document.getElementById("signupForm").classList.add("hidden");
  document.getElementById("loginForm").classList.remove("hidden");
}
</script>

</body>
</html>
