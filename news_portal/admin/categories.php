<?php
// admin/categories.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$database = new Database();
$db = $database->getConnection();
$category = new Category($db);

$categories = $category->getAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie kategoriami - Panel Administracyjny</title>
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
                            <a class="nav-link" href="articles.php">
                                <i class="fas fa-newspaper me-2"></i>Artykuły
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="categories.php">
                                <i class="fas fa-tags me-2"></i>Kategorie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>Użytkownicy
                            </a>
                        </li>
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
                        <i class="fas fa-tags me-2"></i>Zarządzanie kategoriami
                    </h1>
                    <a href="add_category.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Dodaj kategorię
                    </a>
                </div>

                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Brak kategorii</h3>
                        <p class="text-muted">Nie masz jeszcze żadnych kategorii.</p>
                        <a href="add_category.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Dodaj pierwszą kategorię
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($categories as $cat): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?= htmlspecialchars($cat['name']) ?></h5>
                                    <span class="badge bg-primary"><?= $cat['slug'] ?></span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <?= htmlspecialchars($cat['description']) ?>
                                    </p>
                                    <hr>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        Właściciel: <?= htmlspecialchars($cat['author_name'] ?? 'Brak') ?><br>
                                        <i class="fas fa-calendar me-1"></i>
                                        Utworzona: <?= formatDate($cat['created_at']) ?>
                                    </small>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group w-100" role="group">
                                        <a href="../category.php?slug=<?= $cat['slug'] ?>" 
                                           class="btn btn-outline-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_category.php?id=<?= $cat['id'] ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_category.php?id=<?= $cat['id'] ?>" 
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Czy na pewno chcesz usunąć tę kategorię? Wszystkie artykuły z tej kategorii też zostaną usunięte!')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            Łącznie: <?= count($categories) ?> kategorii
                        </small>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>