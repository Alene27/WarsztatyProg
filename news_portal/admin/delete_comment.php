<?php
// admin/delete_comment.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$comment = new Comment($db);

$comment_id = $_GET['id'] ?? 0;
$article_id = $_GET['article_id'] ?? 0;

if ($comment_id && $comment->delete($comment_id)) {
    if ($article_id) {
        redirect("../article.php?id=$article_id");
    } else {
        redirect('index.php');
    }
} else {
    redirect('index.php');
}
?>