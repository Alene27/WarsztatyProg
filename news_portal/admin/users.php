<?php
// admin/users.php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$users = $user->getAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie użytkownikami - Panel Administracyjny</title>
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
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
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
                        <i class="fas fa-users me-2"></i>Zarządzanie użytkownikami
                    </h1>
                    <a href="add_user.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i>Dodaj użytkownika
                    </a>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-primary"><?= count($users) ?></h3>
                                <p class="card-text">Wszystkich użytkowników</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-danger"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></h3>
                                <p class="card-text">Administratorów</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-success"><?= count(array_filter($users, fn($u) => $u['role'] === 'author')) ?></h3>
                                <p class="card-text">Autorów</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-info"><?= count(array_filter($users, fn($u) => strtotime($u['created_at']) > strtotime('-30 days'))) ?></h3>
                                <p class="card-text">Nowych (30 dni)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Użytkownik</th>
                                        <th>Email</th>
                                        <th>Rola</th>
                                        <th>Data dołączenia</th>
                                        <th>Akcje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                    <tr <?= $u['id'] == getUserId() ? 'class="table-warning"' : '' ?>>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($u['username']) ?></strong>
                                                    <?php if ($u['id'] == getUserId()): ?>
                                                        <span class="badge bg-warning text-dark ms-1">To ty</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-envelope me-1 text-muted"></i>
                                            <?= htmlspecialchars($u['email']) ?>
                                        </td>
                                        <td>
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-crown me-1"></i>Administrator
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-pen me-1"></i>Autor
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= formatDate($u['created_at']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit_user.php?id=<?= $u['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Edytuj">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="reset_password.php?id=<?= $u['id'] ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Resetuj hasło"
                                                   onclick="return confirm('Czy na pewno chcesz zresetować hasło tego użytkownika?')">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                <?php if ($u['id'] != getUserId()): ?>
                                                <a href="delete_user.php?id=<?= $u['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger" title="Usuń"
                                                   onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Wszystkie jego artykuły zostaną usunięte!')">
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
                    </div>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        Łącznie: <?= count($users) ?> użytkowników
                    </small>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>