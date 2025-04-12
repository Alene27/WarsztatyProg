<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz zapisu do pliku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Formularz zapisu do pliku</h1>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Pobierz dane z formularza
        $imie = $_POST["imie"] ?? "";
        $email = $_POST["email"] ?? "";
        $wiadomosc = $_POST["wiadomosc"] ?? "";
        
        if (!empty($imie) && !empty($email) && !empty($wiadomosc)) {
            $plik = "dane.txt";
            
            $linia = date("Y-m-d H:i:s") . " | Imię: " . $imie . " | Email: " . $email . " | Wiadomość: " . $wiadomosc . "\n";
            
            if (file_put_contents($plik, $linia, FILE_APPEND | LOCK_EX)) {
                echo '<div class="message success">Dane zostały pomyślnie zapisane do pliku!</div>';
            } else {
                echo '<div class="message error">Wystąpił błąd podczas zapisu do pliku!</div>';
            }
        } else {
            echo '<div class="message error">Wszystkie pola formularza są wymagane!</div>';
        }
    }
    ?>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="wiadomosc">Wiadomość:</label>
            <input type="text" id="wiadomosc" name="wiadomosc" required>
        </div>
        
        <button type="submit">Zapisz dane</button>
    </form>
</body>
</html>