<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Przekierowanie do strony logowania z informacją o braku dostępu
    header('Location: access_denied.php');
    exit;
}

// Pobranie nazwy użytkownika z ciasteczka
$username = $_COOKIE['user_login'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rezerwacja Hotelu - Liczba Osób</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .form-group { 
            background-color: #f4f4f4; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 5px; 
        }
        .user-panel {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="user-panel">
        <div>Witaj, <strong><?php echo htmlspecialchars($username); ?></strong>!</div>
        <a href="logout.php" class="logout-btn">Wyloguj</a>
    </div>

    <h1>Rezerwacja Hotelu</h1>
    <form action="guest_details.php" method="get">
        <label>Wybierz liczbę osób:</label>
        <select name="num_guests" required>
            <option value="">Wybierz liczbę osób</option>
            <option value="1">1 osoba</option>
            <option value="2">2 osoby</option>
            <option value="3">3 osoby</option>
            <option value="4">4 osoby</option>
        </select>
        <button type="submit">Dalej</button>
    </form>
</body>
</html>