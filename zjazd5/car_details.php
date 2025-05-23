<?php

require_once "config.php";

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];
    

    $sql = "SELECT * FROM samochody WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $car = $result->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }
    
    $stmt->close();
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Samochodowy - Szczegóły Samochodu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="nav">
        <a href="index.php">Strona główna</a>
        <a href="all_cars.php">Wszystkie samochody</a>
        <a href="add_car.php">Dodaj samochód</a>
    </div>
    
    <div class="container">
        <h1>Szczegóły Samochodu</h1>
        
        <div class="car-details">
            <h2><?php echo $car["marka"] . " " . $car["model"]; ?></h2>
            
            <p><span class="label">ID:</span> <?php echo $car["id"]; ?></p>
            <p><span class="label">Marka:</span> <?php echo $car["marka"]; ?></p>
            <p><span class="label">Model:</span> <?php echo $car["model"]; ?></p>
            <p><span class="label">Cena:</span> <?php echo number_format($car["cena"], 2, ',', ' '); ?> PLN</p>
            <p><span class="label">Rok produkcji:</span> <?php echo $car["rok"]; ?></p>
            <p><span class="label">Opis:</span> <?php echo nl2br($car["opis"]); ?></p>
            
            <a href="index.php" class="btn btn-back">Powrót do strony głównej</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>