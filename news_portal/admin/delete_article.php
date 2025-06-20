<?php
// admin/delete_article.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);

$article_id = $_GET['id'] ?? 0;

if ($article_id) {
    // Pobierz artykuł do sprawdzenia uprawnień
    $art = $article->getById($article_id);
    
    if ($art && (isAdmin() || $art['author_id'] == getUserId())) {
        // Usuń plik obrazka jeśli istnieje
        if ($art['image_path']) {
            $image_file = '../' . UPLOAD_PATH . $art['image_path'];
            if (file_exists($image_file)) {
                unlink($image_file);
            }
        }
        
        // Usuń artykuł
        if ($article->delete($article_id)) {
            redirect('articles.php?deleted=1');
        }
    }
}

redirect('articles.php');
?>