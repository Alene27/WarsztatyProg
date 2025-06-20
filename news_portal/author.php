<?php
// author.php
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$user = new User($db);
$category = new Category($db);

$author_id = $_GET['id'] ?? 0;
$author = $user->getById($author_id);

if (!$author) {
    redirect('index.php');
}

$articles = $article->getByAuthor($author_id, 12);
$categories = $category->getAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artykuły autora <?= htmlspecialchars($author['username']) ?> - Portal Informacyjny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .article-card { margin-bottom: 2rem; }
        .article-image { width: 100%; height: 200px; object-fit: cover; }
        footer { background-color: #f8f9fa; margin-top: 3rem; }
        .author-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
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

    <!-- Author Info Section -->
    <div class="author-info py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4">Artykuły autora: <?= htmlspecialchars($author['username']) ?></h1>
                    <p class="lead">
                        <?= ucfirst($author['role']) ?> w naszym portalu od <?= formatDate($author['created_at']) ?>
                    </p>
                    <p>Łącznie opublikowano: <strong><?= count($articles) ?></strong> artykułów</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Strona główna</a></li>
                <li class="breadcrumb-item active">Autor: <?= htmlspecialchars($author['username']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <?php if (empty($articles)): ?>
                    <div class="alert alert-info">
                        <p>Ten autor nie opublikował jeszcze żadnych artykułów.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($articles as $art): ?>
                        <div class="col-md-6 article-card">
                            <div class="card h-100">
                                <?php if ($art['image_path']): ?>
                                <img src="<?= UPLOAD_PATH . $art['image_path'] ?>" 
                                     class="card-img-top article-image" 
                                     alt="<?= htmlspecialchars($art['title']) ?>">
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <a href="article.php?id=<?= $art['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($art['title']) ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text">
                                        <?= htmlspecialchars(createExcerpt($art['content'])) ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <small class="text-muted">
                                            <?= formatDate($art['published_at']) ?>
                                            | Kategoria: <a href="category.php?slug=<?= $art['category_slug'] ?>" 
                                                           class="text-decoration-none">
                                                <?= htmlspecialchars($art['category_name']) ?>
                                            </a>
                                        </small>
                                        
                                        <div class="mt-2">
                                            <a href="article.php?id=<?= $art['id'] ?>" 
                                               class="btn btn-primary btn-sm">
                                                Czytaj dalej
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Informacje o autorze</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nazwa użytkownika:</strong> <?= htmlspecialchars($author['username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($author['email']) ?></p>
                        <p><strong>Rola:</strong> <?= ucfirst($author['role']) ?></p>
                        <p><strong>Data dołączenia:</strong> <?= formatDate($author['created_at']) ?></p>
                        <p><strong>Liczba artykułów:</strong> <?= count($articles) ?></p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Kategorie</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($categories as $cat): ?>
                        <a href="category.php?slug=<?= $cat['slug'] ?>" 
                           class="list-group-item list-group-item-action">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                        <?php endforeach; ?>
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