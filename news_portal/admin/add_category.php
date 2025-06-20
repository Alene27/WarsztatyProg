<?php
// admin/add_category.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$database = new Database();
$db = $database->getConnection();
$category = new Category($db);
$user = new User($db);

$users = $user->getAll();
$success_message = '';
$error_message = '';

if ($_POST) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $slug = sanitizeInput($_POST['slug'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $author_id = intval($_POST['author_id'] ?? 0);
    
    if ($name && $slug && $description && $author_id) {
        // Sprawdź czy slug jest unikalny
        $existing = $category->getBySlug($slug);
        if (!$existing) {
            $category_data = [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'author_id' => $author_id
            ];
            
            if ($category->create($category_data)) {
                $success_message = 'Kategoria została dodana pomyślnie!';
                $_POST = []; // Wyczyść formularz
            } else {
                $error_message = 'Błąd podczas dodawania kategorii.';
            }
        } else {
            $error_message = 'Kategoria o podanym slug już istnieje. Wybierz inny slug.';
        }
    } else {
        $error_message = 'Proszę wypełnić wszystkie pola.';
    }
}

// Funkcja do generowania slug z nazwy
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj kategorię - Panel Administracyjny</title>
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
                        <i class="fas fa-plus me-2"></i>Dodaj nową kategorię
                    </h1>
                    <a href="categories.php" class="btn btn-outline-secondary">
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

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>Formularz kategorii
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-tag me-1"></i>Nazwa kategorii *
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="name" 
                                               name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                               placeholder="np. Technologia, Sport, Kultura..." required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">
                                            <i class="fas fa-link me-1"></i>Slug (URL) *
                                        </label>
                                        <input type="text" class="form-control" id="slug" 
                                               name="slug" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" 
                                               placeholder="np. technologia, sport, kultura..." required>
                                        <div class="form-text">
                                            Slug będzie używany w adresach URL. Używaj tylko małych liter, cyfr i myślników.
                                            <br>Przykład URL: /category.php?slug=<strong id="slug-preview">twoj-slug</strong>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">
                                            <i class="fas fa-align-left me-1"></i>Opis kategorii *
                                        </label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="4" placeholder="Krótki opis tej kategorii..." 
                                                  required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="author_id" class="form-label">
                                            <i class="fas fa-user me-1"></i>Właściciel kategorii *
                                        </label>
                                        <select class="form-select" id="author_id" name="author_id" required>
                                            <option value="">Wybierz właściciela...</option>
                                            <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" 
                                                    <?= ($_POST['author_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($u['username']) ?> 
                                                (<?= ucfirst($u['role']) ?>)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">
                                            Właściciel będzie mógł dodawać artykuły do tej kategorii.
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Utwórz kategorię
                                        </button>
                                        <a href="categories.php" class="btn btn-outline-danger btn-lg">
                                            <i class="fas fa-times me-2"></i>Anuluj
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Pomoc
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>Co to jest slug?</h6>
                                <p class="small">Slug to przyjazna dla URL wersja nazwy kategorii. Będzie używany w adresach stron.</p>
                                
                                <h6>Przykłady dobrych slugów:</h6>
                                <ul class="small">
                                    <li>technologia</li>
                                    <li>sport-i-rekreacja</li>
                                    <li>kultura-i-sztuka</li>
                                </ul>
                                
                                <h6>Właściciel kategorii:</h6>
                                <p class="small">Użytkownik przypisany jako właściciel będzie mógł zarządzać artykułami w tej kategorii.</p>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-eye me-2"></i>Podgląd
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="preview">
                                    <h6 id="preview-name">Nazwa kategorii</h6>
                                    <p id="preview-description" class="text-muted">Opis kategorii...</p>
                                    <small class="text-muted">URL: /category.php?slug=<span id="preview-slug">slug</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-generowanie slug z nazwy
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = generateSlug(name);
            
            // Aktualizuj pole slug tylko jeśli jest puste lub zostało wygenerowane automatycznie
            const slugField = document.getElementById('slug');
            if (!slugField.dataset.manuallyEdited) {
                slugField.value = slug;
            }
            
            updatePreview();
        });

        // Oznacz slug jako ręcznie edytowany
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.manuallyEdited = 'true';
            updatePreview();
        });

        // Aktualizuj opis w podglądzie
        document.getElementById('description').addEventListener('input', updatePreview);

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[ąćęłńóśźż]/g, function(match) {
                    const map = {
                        'ą': 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n',
                        'ó': 'o', 'ś': 's', 'ź': 'z', 'ż': 'z'
                    };
                    return map[match] || match;
                })
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function updatePreview() {
            const name = document.getElementById('name').value || 'Nazwa kategorii';
            const description = document.getElementById('description').value || 'Opis kategorii...';
            const slug = document.getElementById('slug').value || 'slug';
            
            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-description').textContent = description;
            document.getElementById('preview-slug').textContent = slug;
            document.getElementById('slug-preview').textContent = slug;
        }

        // Walidacja slug w czasie rzeczywistym
        document.getElementById('slug').addEventListener('input', function() {
            const slug = this.value;
            const isValid = /^[a-z0-9-]+$/.test(slug) && slug.length > 0;
            
            if (isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });

        // Inicjalizacja podglądu
        updatePreview();
    </script>
</body>
</html>