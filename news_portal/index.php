<?php
// index.php
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$category = new Category($db);

// Pobierz najnowsze artykuły
$latest_articles = $article->getLatest(6);
$categories = $category->getAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Informacyjny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .article-card { margin-bottom: 2rem; }
        .article-image { width: 100%; height: 200px; object-fit: cover; }
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
                        <a class="nav-link active" href="index.php">Strona główna</a>
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

    <!-- Hero Section -->
    <div class="bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4">Witaj w naszym portalu!</h1>
                    <p class="lead">Najnowsze informacje z różnych dziedzin życia.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="mb-4">Najnowsze artykuły</h2>
                
                <?php if (empty($latest_articles)): ?>
                    <div class="alert alert-info">
                        <p>Brak artykułów do wyświetlenia.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($latest_articles as $art): ?>
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
                                            Autor: <a href="author.php?id=<?= $art['author_id'] ?>" 
                                                     class="text-decoration-none">
                                                <?= htmlspecialchars($art['author_name']) ?>
                                            </a>
                                            | <?= formatDate($art['published_at']) ?>
                                            | <a href="category.php?slug=<?= $art['category_slug'] ?>" 
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
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>O portalu</h5>
                    </div>
                    <div class="card-body">
                        <p>Nasz portal dostarcza najświeższe informacje z różnych dziedzin. 
                           Znajdziesz tu artykuły o technologii, sporcie, kulturze i wielu innych tematach.</p>
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