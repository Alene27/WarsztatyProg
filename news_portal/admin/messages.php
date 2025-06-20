<?php
// admin/messages.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$message = new Message($db);

// Oznacz jako przeczytane
if (isset($_GET['mark_read'])) {
    $message->markAsRead($_GET['mark_read']);
    redirect('messages.php');
}

$messages = $message->getAll();
$unread_count = count(array_filter($messages, fn($m) => !$m['is_read']));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiadomości - Panel Administracyjny</title>
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
        .message-unread {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .message-read {
            background-color: #ffffff;
            border-left: 4px solid #dee2e6;
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
                            <a class="nav-link active" href="messages.php">
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-envelope me-2"></i>Wiadomości kontaktowe
                        <?php if ($unread_count > 0): ?>
                        <span class="badge bg-danger"><?= $unread_count ?> nowych</span>
                        <?php endif; ?>
                    </h1>
                    <div class="btn-toolbar">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filterMessages('all')">
                                Wszystkie (<?= count($messages) ?>)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterMessages('unread')">
                                Nieprzeczytane (<?= $unread_count ?>)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="filterMessages('read')">
                                Przeczytane (<?= count($messages) - $unread_count ?>)
                            </button>
                        </div>
                    </div>
                </div>

                <?php if (empty($messages)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-envelope fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Brak wiadomości</h3>
                        <p class="text-muted">Nie otrzymałeś jeszcze żadnych wiadomości kontaktowych.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($messages as $msg): ?>
                        <div class="col-12 mb-3 message-item" data-status="<?= $msg['is_read'] ? 'read' : 'unread' ?>">
                            <div class="card <?= $msg['is_read'] ? 'message-read' : 'message-unread' ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="card-title d-flex align-items-center">
                                                <?php if (!$msg['is_read']): ?>
                                                    <span class="badge bg-primary me-2">NOWE</span>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($msg['subject']) ?>
                                            </h5>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?= htmlspecialchars($msg['name']) ?>
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-envelope me-1"></i>
                                                <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($msg['email']) ?>
                                                </a>
                                            </h6>
                                            <p class="card-text">
                                                <?= nl2br(htmlspecialchars(substr($msg['message'], 0, 200))) ?>
                                                <?php if (strlen($msg['message']) > 200): ?>
                                                    <span class="text-muted">...</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-end">
                                                <small class="text-muted d-block mb-2">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= formatDate($msg['created_at']) ?>
                                                </small>
                                                
                                                <div class="btn-group-vertical w-100">
                                                    <button class="btn btn-sm btn-outline-primary mb-1" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#messageModal<?= $msg['id'] ?>">
                                                        <i class="fas fa-eye me-1"></i>Pokaż pełną treść
                                                    </button>
                                                    
                                                    <?php if (!$msg['is_read']): ?>
                                                    <a href="?mark_read=<?= $msg['id'] ?>" 
                                                       class="btn btn-sm btn-outline-success mb-1">
                                                        <i class="fas fa-check me-1"></i>Oznacz jako przeczytane
                                                    </a>
                                                    <?php else: ?>
                                                    <span class="btn btn-sm btn-outline-secondary mb-1 disabled">
                                                        <i class="fas fa-check me-1"></i>Przeczytane
                                                    </span>
                                                    <?php endif; ?>
                                                    
                                                    <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= urlencode($msg['subject']) ?>" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-reply me-1"></i>Odpowiedz
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal dla pełnej treści wiadomości -->
                        <div class="modal fade" id="messageModal<?= $msg['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-envelope me-2"></i>
                                            <?= htmlspecialchars($msg['subject']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Od:</strong> <?= htmlspecialchars($msg['name']) ?>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Email:</strong> 
                                                <a href="mailto:<?= htmlspecialchars($msg['email']) ?>">
                                                    <?= htmlspecialchars($msg['email']) ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Data:</strong> <?= formatDate($msg['created_at']) ?>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Status:</strong> 
                                                <?php if ($msg['is_read']): ?>
                                                    <span class="badge bg-success">Przeczytane</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Nowe</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="message-content">
                                            <strong>Treść wiadomości:</strong>
                                            <div class="mt-2 p-3 bg-light rounded">
                                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re: <?= urlencode($msg['subject']) ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-reply me-2"></i>Odpowiedz emailem
                                        </a>
                                        <?php if (!$msg['is_read']): ?>
                                        <a href="?mark_read=<?= $msg['id'] ?>" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Oznacz jako przeczytane
                                        </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Zamknij
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    Łącznie: <?= count($messages) ?> wiadomości 
                                    (<?= $unread_count ?> nieprzeczytanych)
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    Ostatnia aktualizacja: <?= date('d.m.Y H:i') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filtrowanie wiadomości
        function filterMessages(filter) {
            const messages = document.querySelectorAll('.message-item');
            
            messages.forEach(message => {
                const status = message.getAttribute('data-status');
                
                if (filter === 'all') {
                    message.style.display = 'block';
                } else if (filter === 'unread' && status === 'unread') {
                    message.style.display = 'block';
                } else if (filter === 'read' && status === 'read') {
                    message.style.display = 'block';
                } else {
                    message.style.display = 'none';
                }
            });
            
            // Aktualizuj aktywny przycisk
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Auto-refresh co 30 sekund dla nowych wiadomości
        setInterval(() => {
            const unreadCount = <?= $unread_count ?>;
            if (unreadCount > 0) {
                document.title = `(${unreadCount}) Wiadomości - Panel Administracyjny`;
            }
        }, 30000);

        // Oznacz wiadomość jako przeczytaną przy otwarciu modalu
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', function() {
                const messageId = this.getAttribute('data-bs-target').replace('#messageModal', '');
                // Tutaj można dodać AJAX call do oznaczenia jako przeczytane
            });
        });
    </script>
</body>
</html>