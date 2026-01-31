<?php
require_once '../config/session.php';
require_once '../config/csrf.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard.php");
    exit;
}

verify_csrf();

if (!isset($_SESSION['user_id'], $_POST['post_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$post_id = (int) $_POST['post_id'];
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM posts WHERE id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);
mysqli_stmt_execute($stmt);

header("Location: ../dashboard.php");
exit;
