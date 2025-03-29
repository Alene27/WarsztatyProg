<?php
$num_guests = isset($_GET['num_guests']) ? intval($_GET['num_guests']) : 0;

if ($num_guests < 1 || $num_guests > 4) {
    die("Nieprawidłowa liczba osób");
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
        input { width: 100%; padding: 8px; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>Dane Gości (<?php echo $num_guests; ?> osoby)</h1>
    <form action="reservation_process.php" method="post">
        <input type="hidden" name="num_guests" value="<?php echo $num_guests; ?>">
        
        <?php for ($i = 1; $i <= $num_guests; $i++): ?>
        <div class="form-group">
            <h3>Dane osoby <?php echo $i; ?></h3>
            
            <label>Imię:</label>
            <input type="text" name="guest_first_name[]" required>
            
            <label>Nazwisko:</label>
            <input type="text" name="guest_last_name[]" required>
            
            <label>Wiek:</label>
            <input type="number" name="guest_age[]" min="0" max="120" required>
            
            <label>Płeć:</label>
            <select name="guest_gender[]" required>
                <option value="">Wybierz</option>
                <option value="male">Mężczyzna</option>
                <option value="female">Kobieta</option>
            </select>
        </div>
        <?php endfor; ?>

        <h2>Dane rezerwacji</h2>
        <label>Email kontaktowy:</label>
        <input type="email" name="contact_email" required>
        
        <label>Data przyjazdu:</label>
        <input type="date" name="arrival_date" required>
        
        <label>Data wyjazdu:</label>
        <input type="date" name="departure_date" required>
        
        <button type="submit">Zarezerwuj</button>
    </form>
</body>
</html>