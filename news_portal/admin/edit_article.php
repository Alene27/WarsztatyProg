<?php
// admin/edit_article.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);
$category = new Category($db);

$article_id = $_GET['id'] ?? 0;
$art = $article->getById($article_id);

if (!$art || (!isAdmin() && $art['author_id'] != getUserId())) {
    redirect('articles.php');
}

$categories = $category->getAll();
$success_message = '';
$error_message = '';

if ($_POST) {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    
    if ($title && $content && $category_id) {
        $image_path = $art['image_path']; // Zachowaj istniejący obrazek
        
        // Obsługa nowego obrazu
        if ($_FILES['image']['size'] > 0) {
            $upload_dir = '../' . UPLOAD_PATH;
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                // Usuń stary obrazek
                if ($image_path && file_exists($upload_dir . $image_path)) {
                    unlink($upload_dir . $image_path);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_path = time() . '_' . uniqid() . '.' . $file_extension;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_path)) {
                    $image_path = $art['image_path']; // Przywróć stary jeśli upload się nie udał
                    $error_message = 'Błąd podczas uploadu nowego obrazu.';
                }
            } else {
                $error_message = 'Nieprawidłowy format obrazu. Dozwolone: JPG, PNG, GIF, WebP.';
            }
        }
        
        if (!$error_message) {
            $article_data = [
                'title' => $title,
                'content' => $content,
                'excerpt' => createExcerpt($content),
                'category_id' => $category_id,
                'image_path' => $image_path
            ];
            
            if ($article->update($article_id, $article_data)) {
                $success_message = 'Artykuł został zaktualizowany pomyślnie!';
                // Odśwież dane artykułu
                $art = $article->getById($article_id);
            } else {
                $error_message = 'Błąd podczas aktualizacji artykułu.';
            }
        }
    } else {
        $error_message = 'Proszę wypełnić wszystkie wymagane pola.';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj artykuł - Panel Administracyjny</title>
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
        .current-image {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
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
                        <i class="fas fa-edit me-2"></i>Edytuj artykuł
                    </h1>
                    <div class="btn-toolbar">
                        <div class="btn-group me-2">
                            <a href="../article.php?id=<?= $art['id'] ?>" class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-eye me-1"></i>Podgląd
                            </a>
                            <a href="articles.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Powrót
                            </a>
                        </div>
                    </div>
                </div>

                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $success_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edycja artykułu: <?= htmlspecialchars($art['title']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            <i class="fas fa-heading me-1"></i>Tytuł artykułu *
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="title" 
                                               name="title" value="<?= htmlspecialchars($_POST['title'] ?? $art['title']) ?>" 
                                               required maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">
                                            <i class="fas fa-tag me-1"></i>Kategoria *
                                        </label>
                                        <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" 
                                                    <?= (($_POST['category_id'] ?? $art['category_id']) == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    <i class="fas fa-image me-1"></i>Obrazek
                                </label>
                                
                                <?php if ($art['image_path']): ?>
                                <div class="mb-2">
                                    <p class="mb-1"><strong>Aktualny obrazek:</strong></p>
                                    <img src="../<?= UPLOAD_PATH . $art['image_path'] ?>" 
                                         class="current-image rounded border" 
                                         alt="Aktualny obrazek">
                                </div>
                                <?php endif; ?>
                                
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">
                                    <?php if ($art['image_path']): ?>
                                        Pozostaw puste aby zachować aktualny obrazek. 
                                    <?php endif; ?>
                                    Dozwolone formaty: JPG, PNG, GIF, WebP.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="content" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Treść artykułu *
                                </label>
                                <textarea class="form-control" id="content" name="content" rows="20" 
                                          required><?= htmlspecialchars($_POST['content'] ?? $art['content']) ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Zapisz zmiany
                                    </button>
                                    <a href="articles.php" class="btn btn-outline-secondary btn-lg ms-2">
                                        <i class="fas fa-times me-2"></i>Anuluj
                                    </a>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Ostatnia modyfikacja: <?= formatDate($art['updated_at']) ?>
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informacje o artykule -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info me-2"></i>Informacje o artykule
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID artykułu:</strong> <?= $art['id'] ?></p>
                                <p><strong>Autor:</strong> <?= htmlspecialchars($art['author_name']) ?></p>
                                <p><strong>Kategoria:</strong> <?= htmlspecialchars($art['category_name']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Data publikacji:</strong> <?= formatDate($art['published_at']) ?></p>
                                <p><strong>Ostatnia edycja:</strong> <?= formatDate($art['updated_at']) ?></p>
                                <p><strong>Długość treści:</strong> <?= strlen($art['content']) ?> znaków</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>