<?php
// article.php
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$comment = new Comment($db);
$category = new Category($db);

// Pobierz ID artykułu
$article_id = $_GET['id'] ?? 0;

if (!$article_id) {
    redirect('index.php');
}

// Pobierz artykuł
$art = $article->getById($article_id);

if (!$art) {
    redirect('index.php');
}

// Pobierz komentarze
$comments = $comment->getByArticle($article_id);
$categories = $category->getAll();

// Przetwarzanie dodawania komentarza
$comment_message = '';
if ($_POST && isset($_POST['add_comment'])) {
    $nickname = sanitizeInput($_POST['nickname'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    
    if ($nickname && $content) {
        $comment_data = [
            'article_id' => $article_id,
            'nickname' => $nickname,
            'content' => $content
        ];
        
        if ($comment->create($comment_data)) {
            $comment_message = '<div class="alert alert-success">Komentarz został dodany!</div>';
            // Odśwież komentarze
            $comments = $comment->getByArticle($article_id);
        } else {
            $comment_message = '<div class="alert alert-danger">Błąd podczas dodawania komentarza.</div>';
        }
    } else {
        $comment_message = '<div class="alert alert-warning">Proszę wypełnić wszystkie pola.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($art['title']) ?> - Portal Informacyjny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .article-image { width: 100%; max-height: 400px; object-fit: cover; }
        .comment-box { background-color: #f8f9fa; border-left: 4px solid #007bff; }
        footer { background-color: #f8f9fa; margin-top: 3rem; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Portal Informacyjny</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Strona główna</a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="category.php?slug=<?= $cat['slug'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Kontakt</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-bs-toggle="dropdown">
                                <?= htmlspecialchars($_SESSION['username']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="admin/index.php">Panel administracyjny</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Wyloguj</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Zaloguj</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Strona główna</a></li>
                        <li class="breadcrumb-item">
                            <a href="category.php?slug=<?= $art['category_slug'] ?>">
                                <?= htmlspecialchars($art['category_name']) ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($art['title']) ?></li>
                    </ol>
                </nav>

                <!-- Article -->
                <article>
                    <h1 class="mb-3"><?= htmlspecialchars($art['title']) ?></h1>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            Autor: <a href="author.php?id=<?= $art['author_id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($art['author_name']) ?>
                            </a>
                            | <?= formatDate($art['published_at']) ?>
                            | Kategoria: <a href="category.php?slug=<?= $art['category_slug'] ?>" 
                                           class="text-decoration-none">
                                <?= htmlspecialchars($art['category_name']) ?>
                            </a>
                        </small>
                    </div>

                    <?php if ($art['image_path']): ?>
                    <div class="mb-4">
                        <img src="<?= UPLOAD_PATH . $art['image_path'] ?>" 
                             class="article-image rounded" 
                             alt="<?= htmlspecialchars($art['title']) ?>">
                    </div>
                    <?php endif; ?>

                    <div class="article-content">
                        <?= nl2br(htmlspecialchars($art['content'])) ?>
                    </div>
                </article>

                <hr class="my-5">

                <!-- Comments Section -->
                <section>
                    <h3>Komentarze (<?= count($comments) ?>)</h3>
                    
                    <?= $comment_message ?>
                    
                    <!-- Add Comment Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Dodaj komentarz</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="nickname" class="form-label">Twój nick</label>
                                    <input type="text" class="form-control" id="nickname" name="nickname" 
                                           value="<?= htmlspecialchars($_POST['nickname'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Treść komentarza</label>
                                    <textarea class="form-control" id="content" name="content" rows="4" 
                                              required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                                </div>
                                <button type="submit" name="add_comment" class="btn btn-primary">
                                    Dodaj komentarz
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Display Comments -->
                    <?php if (empty($comments)): ?>
                        <div class="alert alert-info">
                            <p>Brak komentarzy. Bądź pierwszy!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comm): ?>
                        <div class="comment-box p-3 mb-3 rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?= htmlspecialchars($comm['nickname']) ?></strong>
                                    <small class="text-muted ms-2"><?= formatDate($comm['created_at']) ?></small>
                                </div>
                                <?php if (isAdmin()): ?>
                                <a href="admin/delete_comment.php?id=<?= $comm['id'] ?>&article_id=<?= $article_id ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Czy na pewno chcesz usunąć ten komentarz?')">
                                    Usuń
                                </a>
                                <?php endif; ?>
                            </div>
                            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($comm['content'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Kategorie</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($categories as $cat): ?>
                        <a href="category.php?slug=<?= $cat['slug'] ?>" 
                           class="list-group-item list-group-item-action <?= $cat['slug'] === $art['category_slug'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Więcej od autora</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="author.php?id=<?= $art['author_id'] ?>" class="btn btn-outline-primary">
                                Zobacz wszystkie artykuły autora <?= htmlspecialchars($art['author_name']) ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2025 Portal Informacyjny. Wszystkie prawa zastrzeżone.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="contact.php" class="text-decoration-none">Kontakt</a> | 
                    <a href="admin/index.php" class="text-decoration-none">Panel admin</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>