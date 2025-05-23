<?php
require_once "config.php";

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marka = $_POST["marka"];
    $model = $_POST["model"];
    $cena = $_POST["cena"];
    $rok = $_POST["rok"];
    $opis = $_POST["opis"];
    
    if (empty($marka) || empty($model) || empty($cena) || empty($rok)) {
        $error_message = "Wszystkie pola poza opisem są wymagane!";
    } else {
        $sql = "INSERT INTO samochody (marka, model, cena, rok, opis) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdis", $marka, $model, $cena, $rok, $opis);
        
        if ($stmt->execute()) {
            $success_message = "Samochód został dodany pomyślnie!";
            // Wyczyszczenie pól formularza po udanym dodaniu
            $marka = $model = $opis = "";
            $cena = $rok = "";
        } else {
            $error_message = "Błąd: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Samochodowy - Dodaj Samochód</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">Strona główna</a>
        <a href="all_cars.php">Wszystkie samochody</a>
        <a href="add_car.php" class="active">Dodaj samochód</a>
    </div>
    
    <div class="container">
        <h1>Dodaj nowy samochód</h1>
        
        <?php
        if (!empty($error_message)) {
            echo "<p class='error-message'>" . $error_message . "</p>";
        }
        if (!empty($success_message)) {
            echo "<p class='success-message'>" . $success_message . "</p>";
        }
        ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="marka">Marka:</label>
                <input type="text" id="marka" name="marka" value="<?php echo isset($marka) ? $marka : ''; ?>" required>
            </div>
            
            <div>
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" value="<?php echo isset($model) ? $model : ''; ?>" required>
            </div>
            
            <div>
                <label for="cena">Cena (PLN):</label>
                <input type="number" id="cena" name="cena" min="0" step="0.01" value="<?php echo isset($cena) ? $cena : ''; ?>" required>
            </div>
            
            <div>
                <label for="rok">Rok produkcji:</label>
                <input type="number" id="rok" name="rok" min="1900" max="<?php echo date("Y"); ?>" value="<?php echo isset($rok) ? $rok : ''; ?>" required>
            </div>
            
            <div>
                <label for="opis">Opis:</label>
                <textarea id="opis" name="opis"><?php echo isset($opis) ? $opis : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn">Dodaj samochód</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>