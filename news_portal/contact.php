<?php
// contact.php
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$message = new Message($db);
$category = new Category($db);

$categories = $category->getAll();
$success_message = '';
$error_message = '';

if ($_POST) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $msg_content = sanitizeInput($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $msg_content) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message_data = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $msg_content
            ];
            
            if ($message->create($message_data)) {
                $success_message = 'Wiadomość została wysłana pomyślnie! Odpowiemy tak szybko jak to możliwe.';
                // Wyczyść formularz po udanym wysłaniu
                $_POST = [];
            } else {
                $error_message = 'Błąd podczas wysyłania wiadomości. Spróbuj ponownie.';
            }
        } else {
            $error_message = 'Proszę podać poprawny adres email.';
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
    <title>Kontakt - Portal Informacyjny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        footer { background-color: #f8f9fa; margin-top: 3rem; }
        .contact-icon { font-size: 2rem; color: #007bff; }
        .contact-card { transition: transform 0.2s; }
        .contact-card:hover { transform: translateY(-5px); }
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
                        <a class="nav-link" href="index.php">Strona główna</a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="category.php?slug=<?= $cat['slug'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Kontakt</a>
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

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Strona główna</a></li>
                <li class="breadcrumb-item active">Kontakt</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Skontaktuj się z nami</h1>
                <p class="lead">Masz pytania, sugestie lub propozycje nowych tematów? Napisz do nas!</p>
            </div>
        </div>

        <!-- Contact Info Cards -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card contact-card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-envelope contact-icon mb-3"></i>
                        <h5 class="card-title">Email</h5>
                        <p class="card-text">kontakt@portal.pl</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card contact-card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-phone contact-icon mb-3"></i>
                        <h5 class="card-title">Telefon</h5>
                        <p class="card-text">+48 123 456 789</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card contact-card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-clock contact-icon mb-3"></i>
                        <h5 class="card-title">Godziny pracy</h5>
                        <p class="card-text">Pn-Pt: 9:00-17:00</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
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
                        <h5><i class="fas fa-paper-plane me-2"></i>Formularz kontaktowy</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-1"></i>Imię i nazwisko *
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-1"></i>Adres email *
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Temat wiadomości *
                                </label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Wybierz temat...</option>
                                    <option value="Propozycja nowego tematu" <?= ($_POST['subject'] ?? '') === 'Propozycja nowego tematu' ? 'selected' : '' ?>>
                                        Propozycja nowego tematu
                                    </option>
                                    <option value="Współpraca" <?= ($_POST['subject'] ?? '') === 'Współpraca' ? 'selected' : '' ?>>
                                        Współpraca
                                    </option>
                                    <option value="Błąd na stronie" <?= ($_POST['subject'] ?? '') === 'Błąd na stronie' ? 'selected' : '' ?>>
                                        Błąd na stronie
                                    </option>
                                    <option value="Pytanie ogólne" <?= ($_POST['subject'] ?? '') === 'Pytanie ogólne' ? 'selected' : '' ?>>
                                        Pytanie ogólne
                                    </option>
                                    <option value="Inne" <?= ($_POST['subject'] ?? '') === 'Inne' ? 'selected' : '' ?>>
                                        Inne
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    <i class="fas fa-comment me-1"></i>Treść wiadomości *
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="6" 
                                          placeholder="Opisz szczegółowo swoją sprawę..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Wyślij wiadomość
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Informacje</h5>
                    </div>
                    <div class="card-body">
                        <p>Skontaktuj się z nami, jeśli:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Masz propozycję nowego tematu</li>
                            <li><i class="fas fa-check text-success me-2"></i>Chcesz nawiązać współpracę</li>
                            <li><i class="fas fa-check text-success me-2"></i>Znalazłeś błąd na stronie</li>
                            <li><i class="fas fa-check text-success me-2"></i>Masz pytania dotyczące portalu</li>
                        </ul>
                        <hr>
                        <p><strong>Czas odpowiedzi:</strong> Do 24 godzin w dni robocze</p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-tags me-2"></i>Popularne kategorie</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($categories as $cat): ?>
                        <a href="category.php?slug=<?= $cat['slug'] ?>" 
                           class="list-group-item list-group-item-action">
                            <i class="fas fa-arrow-right me-2"></i><?= htmlspecialchars($cat['name']) ?>
                        </a>
                        <?php endforeach; ?>
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