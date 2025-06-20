<?php
// login.php
require_once 'config/config.php';
require_once 'config/database.php';

// Jeśli użytkownik jest już zalogowany, przekieruj do panelu
if (isLoggedIn()) {
    redirect('admin/index.php');
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error_message = '';
$info_message = '';

if ($_POST) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        if ($user->login($username, $password)) {
            redirect('admin/index.php');
        } else {
            $error_message = 'Nieprawidłowa nazwa użytkownika lub hasło.';
        }
    } else {
        $error_message = 'Proszę wypełnić wszystkie pola.';
    }
}

// Sprawdź czy jest informacja o wylogowaniu
if (isset($_GET['logout'])) {
    $info_message = 'Zostałeś pomyślnie wylogowany.';
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Portal Informacyjny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-form {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .demo-info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container">
                    <div class="login-header">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-newspaper me-2"></i>
                            Portal Informacyjny
                        </h1>
                        <p class="mb-0 mt-2">Panel Administracyjny</p>
                    </div>
                    
                    <div class="login-form">
                        <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= $error_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($info_message): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= $info_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Demo credentials info -->
                        <div class="demo-info">
                            <h6><i class="fas fa-key me-2"></i>Dane testowe:</h6>
                            <small>
                                <strong>Administrator:</strong> admin / password<br>
                                <strong>Redaktor:</strong> redaktor1 / password
                            </small>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nazwa użytkownika
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" 
                                       name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                                       placeholder="Wprowadź nazwę użytkownika" required autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Hasło
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" 
                                       name="password" placeholder="Wprowadź hasło" required>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Zaloguj się
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="index.php" class="text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i>Powrót do strony głównej
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Additional info -->
                <div class="text-center mt-4">
                    <small class="text-white">
                        <i class="fas fa-shield-alt me-1"></i>
                        Bezpieczne logowanie z szyfrowaniem SSL
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto-focus and form enhancement -->
    <script>
        // Auto-focus na pierwsze pole, jeśli nie ma błędów
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField && !usernameField.value) {
                usernameField.focus();
            }
            
            // Dodaj efekt ładowania do przycisku
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-login');
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logowanie...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>