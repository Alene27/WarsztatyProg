<?php
// admin/add_article.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);
$category = new Category($db);

$categories = $category->getAll();
$success_message = '';
$error_message = '';

if ($_POST) {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    
    if ($title && $content && $category_id) {
        // Obsługa uploadu obrazu
        $image_path = null;
        if ($_FILES['image']['size'] > 0) {
            $upload_dir = '../' . UPLOAD_PATH;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_path = time() . '_' . uniqid() . '.' . $file_extension;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_path)) {
                    $image_path = null;
                    $error_message = 'Błąd podczas uploadu obrazu.';
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
                'author_id' => getUserId(),
                'category_id' => $category_id,
                'image_path' => $image_path
            ];
            
            if ($article->create($article_data)) {
                $success_message = 'Artykuł został dodany pomyślnie!';
                // Wyczyść formularz
                $_POST = [];
            } else {
                $error_message = 'Błąd podczas dodawania artykułu.';
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
    <title>Dodaj artykuł - Panel Administracyjny</title>
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
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .char-counter {
            font-size: 0.875rem;
            color: #6c757d;
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
                        <i class="fas fa-plus me-2"></i>Dodaj nowy artykuł
                    </h1>
                    <a href="articles.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Powrót do listy
                    </a>
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
                            <i class="fas fa-edit me-2"></i>Formularz artykułu
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
                                               name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                                               placeholder="Wprowadź tytuł artykułu..." required maxlength="255">
                                        <div class="char-counter">
                                            <span id="title-counter">0</span>/255 znaków
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">
                                            <i class="fas fa-tag me-1"></i>Kategoria *
                                        </label>
                                        <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                                            <option value="">Wybierz kategorię...</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" 
                                                    <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    <i class="fas fa-image me-1"></i>Obrazek (opcjonalnie)
                                </label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">
                                    Dozwolone formaty: JPG, PNG, GIF, WebP. Maksymalny rozmiar: 5MB.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="content" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Treść artykułu *
                                </label>
                                <textarea class="form-control" id="content" name="content" rows="20" 
                                          placeholder="Wprowadź pełną treść artykułu..." required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                                <div class="char-counter">
                                    <span id="content-counter">0</span> znaków
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Opublikuj artykuł
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg ms-2" 
                                            onclick="saveDraft()">
                                        <i class="fas fa-file-alt me-2"></i>Zapisz jako szkic
                                    </button>
                                </div>
                                <a href="articles.php" class="btn btn-outline-danger btn-lg">
                                    <i class="fas fa-times me-2"></i>Anuluj
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>Podgląd skrótu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="preview">
                            <p class="text-muted">Podgląd pojawi się po wpisaniu treści artykułu...</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Liczniki znaków
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('title-counter').textContent = this.value.length;
        });

        document.getElementById('content').addEventListener('input', function() {
            document.getElementById('content-counter').textContent = this.value.length;
            updatePreview();
        });

        // Podgląd artykułu
        function updatePreview() {
            const title = document.getElementById('title').value;
            const content = document.getElementById('content').value;
            const preview = document.getElementById('preview');
            
            if (title || content) {
                const excerpt = content.substring(0, 200) + (content.length > 200 ? '...' : '');
                preview.innerHTML = `
                    <h5>${title || 'Tytuł artykułu'}</h5>
                    <p>${excerpt || 'Skrót treści artykułu...'}</p>
                    <small class="text-muted">Skrót: ${excerpt.length} znaków</small>
                `;
            } else {
                preview.innerHTML = '<p class="text-muted">Podgląd pojawi się po wpisaniu treści artykułu...</p>';
            }
        }

        // Funkcja zapisz jako szkic (placeholder)
        function saveDraft() {
            alert('Funkcja zapisywania szkiców będzie dostępna w przyszłej wersji.');
        }

        // Inicjalizacja liczników
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('title-counter').textContent = document.getElementById('title').value.length;
            document.getElementById('content-counter').textContent = document.getElementById('content').value.length;
            updatePreview();
        });

        // Ostrzeżenie przed opuszczeniem strony
        let formChanged = false;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', () => formChanged = true);
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>