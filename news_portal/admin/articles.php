<?php
// admin/articles.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);

// Pobierz artykuły (wszystkie dla admina, tylko własne dla autora)
if (isAdmin()) {
    $articles = $article->getLatest(50);
} else {
    $articles = $article->getByAuthor(getUserId(), 50);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie artykułami - Panel Administracyjny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar .nav-link {
            color: #333;
            border-radius: 5px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-newspaper me-2"></i>Portal Informacyjny
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>
                    Witaj, <?= htmlspecialchars($_SESSION['username']) ?> 
                    <span class="badge bg-secondary"><?= $_SESSION['role'] ?></span>
                </span>
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Wyloguj
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="articles.php">
                                <i class="fas fa-newspaper me-2"></i>Artykuły
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="categories.php">
                                <i class="fas fa-tags me-2"></i>Kategorie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>Użytkownicy
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="messages.php">
                                <i class="fas fa-envelope me-2"></i>Wiadomości
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profil
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-newspaper me-2"></i>Zarządzanie artykułami
                    </h1>
                    <a href="add_article.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Dodaj artykuł
                    </a>
                </div>

                <?php if (empty($articles)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Brak artykułów</h3>
                        <p class="text-muted">Nie masz jeszcze żadnych artykułów.</p>
                        <a href="add_article.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Dodaj pierwszy artykuł
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Tytuł</th>
                                            <th>Autor</th>
                                            <th>Kategoria</th>
                                            <th>Data publikacji</th>
                                            <th>Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($articles as $art): ?>
                                        <tr>
                                            <td><?= $art['id'] ?></td>
                                            <td>
                                                <a href="../article.php?id=<?= $art['id'] ?>" 
                                                   class="text-decoration-none" target="_blank">
                                                    <?= htmlspecialchars($art['title']) ?>
                                                    <i class="fas fa-external-link-alt ms-1 text-muted"></i>
                                                </a>
                                                <?php if ($art['image_path']): ?>
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-image me-1"></i>Ma obrazek
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($art['author_name']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($art['category_name']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= formatDate($art['published_at']) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="edit_article.php?id=<?= $art['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edytuj">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if (isAdmin() || $art['author_id'] == getUserId()): ?>
                                                    <a href="delete_article.php?id=<?= $art['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       title="Usuń"
                                                       onclick="return confirm('Czy na pewno chcesz usunąć ten artykuł? Ta operacja jest nieodwracalna.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            Łącznie: <?= count($articles) ?> artykułów
                        </small>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>