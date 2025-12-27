<?php
session_start();
require_once "auth_check.php";
require_once __DIR__ . "/../backend/db.php";

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

/* Sadece kendi postunu silebilir */
$stmt = $pdo->prepare("
    DELETE FROM posts 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$post_id, $user_id]);

header("Location: dashboard.php");
exit;
