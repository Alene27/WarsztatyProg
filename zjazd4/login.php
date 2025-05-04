<?php
session_start();

// Dane logowania "na sztywno"
$valid_users = [
    'admin' => 'admin123',
    'user' => 'user123'
];

// Sprawdzenie czy użytkownik jest już zalogowany
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Ustawienie ciasteczka z loginem użytkownika jeśli jeszcze nie istnieje
    if (!isset($_COOKIE['user_login'])) {
        setcookie('user_login', $_SESSION['username'], time() + (86400 * 30), "/"); // 30 dni
    }
    
    // Przekierowanie do strony głównej
    header('Location: index.php');
    exit;
}

// Obsługa próby logowania
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($valid_users[$username]) && $valid_users[$username] === $password) {
        // Ustawienie sesji
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        
        // Ustawienie ciasteczka z loginem użytkownika
        setcookie('user_login', $username, time() + (86400 * 30), "/"); // 30 dni
        
        // Przekierowanie do strony głównej
        header('Location: index.php');
        exit;
    } else {
        $error = 'Nieprawidłowy login lub hasło';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .login-form {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Rezerwacja Hotelu - Logowanie</h1>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="login-form">
        <form method="post" action="login.php">
            <label for="username">Login:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Zaloguj</button>
        </form>
    </div>
    
    <p>Dane do logowania:<br>
       Login: admin, Hasło: admin123<br>
       Login: user, Hasło: user123</p>
</body>
</html>