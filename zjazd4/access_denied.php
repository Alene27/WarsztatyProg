<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Brak dostępu</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Brak dostępu</h1>
    
    <div class="alert">
        <h2>Nie masz dostępu do tej części strony</h2>
        <p>Aby móc korzystać z systemu rezerwacji hotelu, musisz być zalogowany.</p>
        <p>Zalogowanie jest wymagane ze względów bezpieczeństwa oraz w celu identyfikacji osoby dokonującej rezerwacji.</p>
    </div>
    
    <a href="login.php" class="btn">Przejdź do strony logowania</a>
</body>
</html>