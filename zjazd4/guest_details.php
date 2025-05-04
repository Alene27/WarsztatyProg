<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Przekierowanie do strony logowania z informacją o braku dostępu
    header('Location: access_denied.php');
    exit;
}

$num_guests = isset($_GET['num_guests']) ? intval($_GET['num_guests']) : 0;

if ($num_guests < 1 || $num_guests > 4) {
    die("Nieprawidłowa liczba osób");
}

// Pobranie nazwy użytkownika z ciasteczka
$username = $_COOKIE['user_login'] ?? $_SESSION['username'];

// Funkcja do pobrania danych z ciasteczek
function getCookieValue($name, $index = null, $default = '') {
    if (!isset($_COOKIE[$name])) {
        return $default;
    }
    
    $value = json_decode($_COOKIE[$name], true);
    
    if ($index !== null) {
        return $value[$index] ?? $default;
    }
    
    return $value;
}

// Funkcja do czyszczenia ciasteczek formularza
if (isset($_GET['clear_cookies'])) {
    $cookie_names = ['guest_first_name', 'guest_last_name', 'guest_age', 'guest_gender', 'contact_email', 'arrival_date', 'departure_date'];
    
    foreach ($cookie_names as $name) {
        setcookie($name, '', time() - 3600, '/');
    }
    
    // Przekierowanie, aby uniknąć ponownego przesłania parametru clear_cookies
    header('Location: guest_details.php?num_guests=' . $num_guests);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dane Gości</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .form-group { 
            background-color: #f4f4f4; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 5px; 
        }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        .user-panel {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .logout-btn {
            background-color: #f44336;
            color: white;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        .clear-btn {
            background-color: #ff9800;
            color: white;
        }
        .clear-btn:hover {
            background-color: #fb8c00;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="user-panel">
        <div>Witaj, <strong><?php echo htmlspecialchars($username); ?></strong>!</div>
        <a href="logout.php" class="btn logout-btn">Wyloguj</a>
    </div>

    <h1>Dane Gości (<?php echo $num_guests; ?> osoby)</h1>
    
    <div class="btn-container">
        <a href="index.php" class="btn" style="background-color: #2196F3; color: white;">Powrót</a>
        <a href="?num_guests=<?php echo $num_guests; ?>&clear_cookies=1" class="btn clear-btn">Wyczyść formularz (usuń ciasteczka)</a>
    </div>
    
    <form action="reservation_process.php" method="post" id="reservationForm">
        <input type="hidden" name="num_guests" value="<?php echo $num_guests; ?>">
        
        <?php for ($i = 0; $i < $num_guests; $i++): ?>
        <div class="form-group">
            <h3>Dane osoby <?php echo $i + 1; ?></h3>
            
            <label>Imię:</label>
            <input type="text" name="guest_first_name[]" value="<?php echo getCookieValue('guest_first_name', $i); ?>" required>
            
            <label>Nazwisko:</label>
            <input type="text" name="guest_last_name[]" value="<?php echo getCookieValue('guest_last_name', $i); ?>" required>
            
            <label>Wiek:</label>
            <input type="number" name="guest_age[]" min="0" max="120" value="<?php echo getCookieValue('guest_age', $i); ?>" required>
            
            <label>Płeć:</label>
            <select name="guest_gender[]" required>
                <option value="">Wybierz</option>
                <option value="male" <?php echo getCookieValue('guest_gender', $i) === 'male' ? 'selected' : ''; ?>>Mężczyzna</option>
                <option value="female" <?php echo getCookieValue('guest_gender', $i) === 'female' ? 'selected' : ''; ?>>Kobieta</option>
            </select>
        </div>
        <?php endfor; ?>

        <h2>Dane rezerwacji</h2>
        <label>Email kontaktowy:</label>
        <input type="email" name="contact_email" value="<?php echo getCookieValue('contact_email'); ?>" required>
        
        <label>Data przyjazdu:</label>
        <input type="date" name="arrival_date" value="<?php echo getCookieValue('arrival_date'); ?>" required>
        
        <label>Data wyjazdu:</label>
        <input type="date" name="departure_date" value="<?php echo getCookieValue('departure_date'); ?>" required>
        
        <div class="btn-container">
            <button type="submit" style="background-color: #4CAF50; color: white;">Zarezerwuj</button>
        </div>
    </form>

    <script>
        // Automatyczne zapisywanie formularza w ciasteczkach
        document.getElementById('reservationForm').addEventListener('change', function(e) {
            if (e.target.name.endsWith('[]')) {
                // Dla pól tablicowych (dane gości)
                const fieldName = e.target.name.slice(0, -2);
                const inputs = document.getElementsByName(e.target.name);
                const values = [];
                
                for (let i = 0; i < inputs.length; i++) {
                    values.push(inputs[i].value);
                }
                
                // Zapisz w ciasteczku jako JSON
                document.cookie = `${fieldName}=${JSON.stringify(values)}; max-age=${60*60*24*30}; path=/`;
            } else {
                // Dla pojedynczych pól
                document.cookie = `${e.target.name}=${e.target.value}; max-age=${60*60*24*30}; path=/`;
            }
        });
    </script>
</body>
</html>