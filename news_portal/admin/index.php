<?php
// admin/index.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();

$article = new Article($db);
$message = new Message($db);
$user = new User($db);
$category = new Category($db);

// Pobierz statystyki
$latest_articles = $article->getLatest(5);
$messages = $message->getAll();
$users = $user->getAll();
$categories = $category->getAll();

// Pobierz artykuły użytkownika jeśli nie jest adminem
if (!isAdmin()) {
    $user_articles = $article->getByAuthor(getUserId(), 10);
} else {
    $user_articles = $latest_articles;
}

$unread_count = count(array_filter($messages, fn($m) => !$m['is_read']));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny - Portal Informacyjny</title>
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
        .stats-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }
        .chart-container {
            position: relative;
            height: 200px;
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
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="articles.php">
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
                                <?php if ($unread_count > 0): ?>
                                <span class="badge bg-danger ms-1"><?= $unread_count ?></span>
                                <?php endif; ?>
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
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="add_article.php" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>Nowy artykuł
                            </a>
                            <a href="../index.php" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Zobacz stronę
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Welcome Card -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card welcome-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="card-title mb-2">
                                            Witaj ponownie, <?= htmlspecialchars($_SESSION['username']) ?>! 👋
                                        </h4>
                                        <p class="card-text mb-0">
                                            Miło Cię widzieć w panelu administracyjnym. 
                                            <?php if (isAdmin()): ?>
                                                Jako administrator masz pełny dostęp do wszystkich funkcji.
                                            <?php else: ?>
                                                Możesz zarządzać swoimi artykułami i przeglądać wiadomości.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <i class="fas fa-user-circle fa-4x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-primary stats-card" onclick="location.href='articles.php'">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= count($user_articles) ?></h4>
                                        <p class="card-text"><?= isAdmin() ? 'Wszystkie artykuły' : 'Moje artykuły' ?></p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-newspaper fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-primary bg-opacity-75">
                                <small>
                                    <i class="fas fa-arrow-right me-1"></i>Zobacz wszystkie
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-success stats-card" onclick="location.href='messages.php'">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= count($messages) ?></h4>
                                        <p class="card-text">Wszystkie wiadomości</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-envelope fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-success bg-opacity-75">
                                <small>
                                    <i class="fas fa-arrow-right me-1"></i>Sprawdź wiadomości
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-warning stats-card" onclick="location.href='messages.php'">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= $unread_count ?></h4>
                                        <p class="card-text">Nieprzeczytane</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-bell fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-warning bg-opacity-75">
                                <small>
                                    <i class="fas fa-arrow-right me-1"></i>Sprawdź teraz
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-info stats-card" <?= isAdmin() ? "onclick=\"location.href='users.php'\"" : '' ?>>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?= count($categories) ?></h4>
                                        <p class="card-text">Kategorie</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-tags fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-info bg-opacity-75">
                                <small>
                                    <?php if (isAdmin()): ?>
                                        <i class="fas fa-arrow-right me-1"></i>Zarządzaj
                                    <?php else: ?>
                                        <i class="fas fa-info me-1"></i>Dostępne kategorie
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row">
                    <!-- Recent Articles -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-newspaper me-2"></i>
                                    <?= isAdmin() ? 'Najnowsze artykuły' : 'Moje ostatnie artykuły' ?>
                                </h5>
                                <a href="articles.php" class="btn btn-sm btn-outline-primary">Zobacz wszystkie</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($user_articles)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">
                                            <?= isAdmin() ? 'Brak artykułów w systemie.' : 'Nie masz jeszcze żadnych artykułów.' ?>
                                        </p>
                                        <a href="add_article.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            <?= isAdmin() ? 'Dodaj pierwszy artykuł' : 'Napisz pierwszy artykuł' ?>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tytuł</th>
                                                    <th>Autor</th>
                                                    <th>Kategoria</th>
                                                    <th>Data</th>
                                                    <th>Akcje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($user_articles as $art): ?>
                                                <tr>
                                                    <td>
                                                        <a href="../article.php?id=<?= $art['id'] ?>" 
                                                           class="text-decoration-none fw-semibold" target="_blank">
                                                            <?= htmlspecialchars($art['title']) ?>
                                                        </a>
                                                        <?php if ($art['image_path']): ?>
                                                            <i class="fas fa-image text-muted ms-1" title="Ma obrazek"></i>
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
                                                        <small class="text-muted">
                                                            <?= formatDate($art['published_at']) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="edit_article.php?id=<?= $art['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary" title="Edytuj">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="../article.php?id=<?= $art['id'] ?>" 
                                                               class="btn btn-sm btn-outline-info" target="_blank" title="Zobacz">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                            <?php if (isAdmin() || $art['author_id'] == getUserId()): ?>
                                                            <a href="delete_article.php?id=<?= $art['id'] ?>" 
                                                               class="btn btn-sm btn-outline-danger" title="Usuń"
                                                               onclick="return confirm('Czy na pewno chcesz usunąć ten artykuł?')">
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Content -->
                    <div class="col-lg-4">
                        <!-- Recent Messages -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-envelope me-2"></i>Najnowsze wiadomości
                                </h5>
                                <a href="messages.php" class="btn btn-sm btn-outline-primary">Zobacz wszystkie</a>
                            </div>
                            <div class="card-body recent-activity">
                                <?php if (empty($messages)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Brak wiadomości.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach (array_slice($messages, 0, 5) as $msg): ?>
                                    <div class="mb-3 p-3 border-start border-3 <?= $msg['is_read'] ? 'border-secondary bg-light' : 'border-primary bg-primary bg-opacity-10' ?>">
                                        <h6 class="mb-1">
                                            <?= htmlspecialchars(substr($msg['subject'], 0, 30)) ?>
                                            <?php if (strlen($msg['subject']) > 30): ?>...<?php endif; ?>
                                            <?php if (!$msg['is_read']): ?>
                                                <span class="badge bg-primary">Nowe</span>
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($msg['name']) ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= formatDate($msg['created_at']) ?>
                                        </small>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($messages) > 5): ?>
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Wyświetlono 5 z <?= count($messages) ?> wiadomości
                                        </small>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>Szybkie akcje
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="add_article.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Dodaj artykuł
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="add_category.php" class="btn btn-success">
                                        <i class="fas fa-tags me-2"></i>Dodaj kategorię
                                    </a>
                                    <a href="users.php" class="btn btn-info">
                                        <i class="fas fa-users me-2"></i>Zarządzaj użytkownikami
                                    </a>
                                    <?php endif; ?>
                                    <a href="messages.php" class="btn btn-warning">
                                        <i class="fas fa-envelope me-2"></i>
                                        Sprawdź wiadomości
                                        <?php if ($unread_count > 0): ?>
                                        <span class="badge bg-light text-dark ms-1"><?= $unread_count ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a href="profile.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-user me-2"></i>Mój profil
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informacje o systemie
                                </h5>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <strong>Zalogowany jako:</strong> <?= htmlspecialchars($_SESSION['username']) ?><br>
                                    <strong>Rola:</strong> <?= ucfirst($_SESSION['role']) ?><br>
                                    <strong>Sesja:</strong> Aktywna<br>
                                    <strong>Ostatnia aktywność:</strong> <?= date('H:i') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh dla powiadomień o nowych wiadomościach
        setInterval(() => {
            const unreadCount = <?= $unread_count ?>;
            if (unreadCount > 0) {
                document.title = `(${unreadCount}) Panel Administracyjny - Portal Informacyjny`;
            } else {
                document.title = 'Panel Administracyjny - Portal Informacyjny';
            }
        }, 30000);

        // Animacja hover dla kart statystyk
        document.addEventListener('DOMContentLoaded', function() {
            const statsCards = document.querySelectorAll('.stats-card[onclick]');
            statsCards.forEach(card => {
                card.style.cursor = 'pointer';
            });
        });

        // Powitalny efekt
        document.addEventListener('DOMContentLoaded', function() {
            const welcomeCard = document.querySelector('.welcome-card');
            welcomeCard.style.opacity = '0';
            welcomeCard.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                welcomeCard.style.transition = 'all 0.5s ease';
                welcomeCard.style.opacity = '1';
                welcomeCard.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
