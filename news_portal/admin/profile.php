<?php
// admin/profile.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$article = new Article($db);

$current_user = $user->getById(getUserId());
$user_articles = $article->getByAuthor(getUserId(), 10);

$success_message = '';
$error_message = '';

// Reset hasła
if ($_POST && isset($_POST['reset_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($current_password && $new_password && $confirm_password) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                // Sprawdź aktualne hasło
                if (password_verify($current_password, $current_user['password'])) {
                    if ($user->resetPassword(getUserId(), $new_password)) {
                        $success_message = 'Hasło zostało zmienione pomyślnie!';
                    } else {
                        $error_message = 'Błąd podczas zmiany hasła.';
                    }
                } else {
                    $error_message = 'Aktualne hasło jest nieprawidłowe.';
                }
            } else {
                $error_message = 'Nowe hasło musi mieć co najmniej 6 znaków.';
            }
        } else {
            $error_message = 'Nowe hasła nie są identyczne.';
        }
    } else {
        $error_message = 'Proszę wypełnić wszystkie pola.';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mój profil - Panel Administracyjny</title>
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
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
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
                            <a class="nav-link active" href="profile.php">
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
                        <i class="fas fa-user me-2"></i>Mój profil
                    </h1>
                    <a href="../index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home me-1"></i>Strona główna
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
                    <!-- Informacje o profilu -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="profile-avatar mx-auto mb-3">
                                    <?= strtoupper(substr($current_user['username'], 0, 2)) ?>
                                </div>
                                <h4><?= htmlspecialchars($current_user['username']) ?></h4>
                                <p class="text-muted"><?= htmlspecialchars($current_user['email']) ?></p>
                                <?php if ($current_user['role'] === 'admin'): ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-crown me-1"></i>Administrator
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-pen me-1"></i>Autor
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Statystyki
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h3 class="text-primary"><?= count($user_articles) ?></h3>
                                        <small class="text-muted">Artykułów</small>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="text-success"><?= date('j', strtotime($current_user['created_at'])) ?></h3>
                                        <small class="text-muted">Dni w portalu</small>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-1"><strong>Data dołączenia:</strong></p>
                                <small class="text-muted"><?= formatDate($current_user['created_at']) ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Zmiana hasła i ostatnie artykuły -->
                    <div class="col-md-8">
                        <!-- Zmiana hasła -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-key me-2"></i>Zmiana hasła
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label">
                                                    <i class="fas fa-lock me-1"></i>Aktualne hasło
                                                </label>
                                                <input type="password" class="form-control" id="current_password" 
                                                       name="current_password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">
                                                    <i class="fas fa-key me-1"></i>Nowe hasło
                                                </label>
                                                <input type="password" class="form-control" id="new_password" 
                                                       name="new_password" minlength="6" required>
                                                <div class="form-text">Minimum 6 znaków</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label">
                                                    <i class="fas fa-check me-1"></i>Potwierdź nowe hasło
                                                </label>
                                                <input type="password" class="form-control" id="confirm_password" 
                                                       name="confirm_password" minlength="6" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="submit" name="reset_password" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Zmień hasło
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Ostatnie artykuły -->
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-newspaper me-2"></i>Moje ostatnie artykuły
                                </h5>
                                <a href="articles.php" class="btn btn-sm btn-outline-primary">Zobacz wszystkie</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($user_articles)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Nie masz jeszcze żadnych artykułów.</p>
                                        <a href="add_article.php" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Dodaj pierwszy artykuł
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach (array_slice($user_articles, 0, 5) as $art): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="../article.php?id=<?= $art['id'] ?>" 
                                                           class="text-decoration-none" target="_blank">
                                                            <?= htmlspecialchars($art['title']) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= formatDate($art['published_at']) ?>
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-tag me-1"></i>
                                                        <?= htmlspecialchars($art['category_name']) ?>
                                                    </small>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <a href="edit_article.php?id=<?= $art['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../article.php?id=<?= $art['id'] ?>" 
                                                       class="btn btn-sm btn-outline-info" target="_blank">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (count($user_articles) > 5): ?>
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            Wyświetlono 5 z <?= count($user_articles) ?> artykułów
                                        </small>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informacje o sesji -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informacje o sesji
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>ID użytkownika:</strong></p>
                                        <small class="text-muted"><?= getUserId() ?></small>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Rola:</strong></p>
                                        <small class="text-muted"><?= ucfirst($_SESSION['role']) ?></small>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Zalogowany jako:</strong></p>
                                        <small class="text-muted"><?= htmlspecialchars($_SESSION['username']) ?></small>
                                    </div>
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
        // Walidacja hasła w czasie rzeczywistym
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword && confirmPassword) {
                if (newPassword === confirmPassword) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // Sprawdzenie siły hasła
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            // Usuń poprzednie klasy
            this.classList.remove('is-valid', 'is-invalid');
            
            if (password.length >= 6) {
                if (strength >= 3) {
                    this.classList.add('is-valid');
                } else {
                    this.classList.add('is-invalid');
                }
            }
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            return strength;
        }
    </script>
</body>
</html>