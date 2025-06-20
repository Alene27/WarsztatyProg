<?php
// admin/add_user.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$database = new Database();
$db = $database->getConnection();

$success_message = '';
$error_message = '';

if ($_POST) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? '');
    
    if ($username && $email && $password && $confirm_password && $role) {
        if ($password === $confirm_password) {
            if (strlen($password) >= 6) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Sprawdź czy username i email są unikalne
                    $check_query = "SELECT COUNT(*) as count FROM users WHERE username = ? OR email = ?";
                    $check_stmt = $db->prepare($check_query);
                    $check_stmt->execute([$username, $email]);
                    $existing = $check_stmt->fetch();
                    
                    if ($existing['count'] == 0) {
                        // Dodaj użytkownika
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $insert_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                        $insert_stmt = $db->prepare($insert_query);
                        
                        if ($insert_stmt->execute([$username, $email, $hashed_password, $role])) {
                            $success_message = 'Użytkownik został dodany pomyślnie!';
                            // Wyczyść formularz
                            $_POST = [];
                        } else {
                            $error_message = 'Błąd podczas dodawania użytkownika.';
                        }
                    } else {
                        $error_message = 'Nazwa użytkownika lub email już istnieje.';
                    }
                } else {
                    $error_message = 'Nieprawidłowy format adresu email.';
                }
            } else {
                $error_message = 'Hasło musi mieć co najmniej 6 znaków.';
            }
        } else {
            $error_message = 'Hasła nie są identyczne.';
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
    <title>Dodaj użytkownika - Panel Administracyjny</title>
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
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        .strength-weak { background-color: #dc3545; }
        .strength-medium { background-color: #ffc107; }
        .strength-strong { background-color: #28a745; }
        .role-card {
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .role-card:hover {
            border-color: #007bff;
            transform: translateY(-2px);
        }
        .role-card.selected {
            border-color: #007bff;
            background-color: #f8f9fa;
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
                            <a class="nav-link" href="categories.php">
                                <i class="fas fa-tags me-2"></i>Kategorie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">
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
                        <i class="fas fa-user-plus me-2"></i>Dodaj nowego użytkownika
                    </h1>
                    <a href="users.php" class="btn btn-outline-secondary">
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
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-edit me-2"></i>Formularz użytkownika
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="userForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">
                                                    <i class="fas fa-user me-1"></i>Nazwa użytkownika *
                                                </label>
                                                <input type="text" class="form-control" id="username" 
                                                       name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                                                       placeholder="np. jan_kowalski" required minlength="3" maxlength="50">
                                                <div class="form-text">
                                                    3-50 znaków, tylko litery, cyfry i podkreślniki
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">
                                                    <i class="fas fa-envelope me-1"></i>Adres email *
                                                </label>
                                                <input type="email" class="form-control" id="email" 
                                                       name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                                       placeholder="jan.kowalski@example.com" required>
                                                <div class="form-text">
                                                    Będzie używany do logowania i kontaktu
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">
                                                    <i class="fas fa-lock me-1"></i>Hasło *
                                                </label>
                                                <input type="password" class="form-control" id="password" 
                                                       name="password" placeholder="Minimum 6 znaków" 
                                                       required minlength="6">
                                                <div class="password-strength" id="passwordStrength"></div>
                                                <div class="form-text">
                                                    <span id="strengthText">Wprowadź hasło aby sprawdzić siłę</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label">
                                                    <i class="fas fa-check me-1"></i>Potwierdź hasło *
                                                </label>
                                                <input type="password" class="form-control" id="confirm_password" 
                                                       name="confirm_password" placeholder="Powtórz hasło" 
                                                       required minlength="6">
                                                <div class="form-text" id="passwordMatch">
                                                    Hasła muszą być identyczne
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-user-tag me-1"></i>Rola użytkownika *
                                        </label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card role-card" data-role="author">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-pen fa-2x text-success mb-2"></i>
                                                        <h5>Autor</h5>
                                                        <p class="small text-muted mb-0">
                                                            Może dodawać, edytować i usuwać własne artykuły
                                                        </p>
                                                        <input type="radio" class="form-check-input mt-2" 
                                                               name="role" value="author" id="role_author" 
                                                               <?= ($_POST['role'] ?? '') === 'author' ? 'checked' : '' ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card role-card" data-role="admin">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-crown fa-2x text-danger mb-2"></i>
                                                        <h5>Administrator</h5>
                                                        <p class="small text-muted mb-0">
                                                            Pełny dostęp do wszystkich funkcji systemu
                                                        </p>
                                                        <input type="radio" class="form-check-input mt-2" 
                                                               name="role" value="admin" id="role_admin"
                                                               <?= ($_POST['role'] ?? '') === 'admin' ? 'checked' : '' ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                            <i class="fas fa-user-plus me-2"></i>Utwórz użytkownika
                                        </button>
                                        <a href="users.php" class="btn btn-outline-danger btn-lg">
                                            <i class="fas fa-times me-2"></i>Anuluj
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Wskazówki
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>Nazwa użytkownika:</h6>
                                <ul class="small">
                                    <li>Musi być unikalna w systemie</li>
                                    <li>3-50 znaków</li>
                                    <li>Tylko litery, cyfry i podkreślniki</li>
                                    <li>Będzie używana do logowania</li>
                                </ul>
                                
                                <h6>Hasło:</h6>
                                <ul class="small">
                                    <li>Minimum 6 znaków</li>
                                    <li>Używaj silnych haseł</li>
                                    <li>Kombinacja liter, cyfr i symboli</li>
                                </ul>
                                
                                <h6>Role użytkowników:</h6>
                                <ul class="small">
                                    <li><strong>Autor:</strong> Zarządza własnymi artykułami</li>
                                    <li><strong>Administrator:</strong> Pełny dostęp do systemu</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-shield-alt me-2"></i>Bezpieczeństwo
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="small">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Hasła są automatycznie szyfrowane<br>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Walidacja unikalności danych<br>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Sprawdzanie siły hasła<br>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Ochrona przed SQL injection
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Walidacja formularza w czasie rzeczywistym
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const roleCards = document.querySelectorAll('.role-card');
        const roleRadios = document.querySelectorAll('input[name="role"]');

        // Sprawdzenie siły hasła
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthBar.className = 'password-strength';
                strengthText.textContent = 'Wprowadź hasło aby sprawdzić siłę';
                return;
            }
            
            if (strength <= 2) {
                strengthBar.style.width = '33%';
                strengthBar.className = 'password-strength strength-weak';
                strengthText.textContent = 'Słabe hasło';
                strengthText.style.color = '#dc3545';
            } else if (strength <= 3) {
                strengthBar.style.width = '66%';
                strengthBar.className = 'password-strength strength-medium';
                strengthText.textContent = 'Średnie hasło';
                strengthText.style.color = '#ffc107';
            } else {
                strengthBar.style.width = '100%';
                strengthBar.className = 'password-strength strength-strong';
                strengthText.textContent = 'Silne hasło';
                strengthText.style.color = '#28a745';
            }
            
            validateForm();
        });

        // Sprawdzenie zgodności haseł
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchText.textContent = 'Hasła muszą być identyczne';
                matchText.style.color = '#6c757d';
                this.classList.remove('is-valid', 'is-invalid');
            } else if (password === confirmPassword) {
                matchText.textContent = 'Hasła są zgodne ✓';
                matchText.style.color = '#28a745';
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                matchText.textContent = 'Hasła nie są zgodne ✗';
                matchText.style.color = '#dc3545';
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
            
            validateForm();
        });

        // Walidacja nazwy użytkownika
        usernameInput.addEventListener('input', function() {
            const username = this.value;
            const isValid = /^[a-zA-Z0-9_]{3,50}$/.test(username);
            
            if (username.length === 0) {
                this.classList.remove('is-valid', 'is-invalid');
            } else if (isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
            
            validateForm();
        });

        // Walidacja email
        emailInput.addEventListener('input', function() {
            const email = this.value;
            const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            
            if (email.length === 0) {
                this.classList.remove('is-valid', 'is-invalid');
            } else if (isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
            
            validateForm();
        });

        // Obsługa wyboru roli
        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                const role = this.dataset.role;
                const radio = document.getElementById(`role_${role}`);
                
                // Usuń zaznaczenie z innych kart
                roleCards.forEach(c => c.classList.remove('selected'));
                roleRadios.forEach(r => r.checked = false);
                
                // Zaznacz aktualną kartę
                this.classList.add('selected');
                radio.checked = true;
                
                validateForm();
            });
        });

        // Funkcja sprawdzająca siłę hasła
        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            return strength;
        }

        // Walidacja całego formularza
        function validateForm() {
            const username = usernameInput.value;
            const email = emailInput.value;
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const roleSelected = document.querySelector('input[name="role"]:checked');
            
            const isUsernameValid = /^[a-zA-Z0-9_]{3,50}$/.test(username);
            const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            const isPasswordValid = password.length >= 6;
            const isPasswordMatch = password === confirmPassword && confirmPassword.length > 0;
            const isRoleSelected = roleSelected !== null;
            
            if (isUsernameValid && isEmailValid && isPasswordValid && isPasswordMatch && isRoleSelected) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            }
        }

        // Inicjalizacja
        document.addEventListener('DOMContentLoaded', function() {
            // Zaznacz wybraną rolę z POST
            const checkedRole = document.querySelector('input[name="role"]:checked');
            if (checkedRole) {
                const roleCard = document.querySelector(`[data-role="${checkedRole.value}"]`);
                if (roleCard) {
                    roleCard.classList.add('selected');
                }
            }
            
            validateForm();
        });

        // Animacja submit
        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Dodawanie...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>