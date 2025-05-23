<?php
require_once "config.php";

$sql = "SELECT * FROM samochody ORDER BY cena ASC LIMIT 5";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Samochodowy - Strona Główna</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="nav">
        <a href="index.php" class="active">Strona główna</a>
        <a href="all_cars.php">Wszystkie samochody</a>
        <a href="add_car.php">Dodaj samochód</a>
    </div>
    
    <div class="container">
        <h1>Portal Samochodowy</h1>
        <h2>5 najtańszych samochodów</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Marka</th>
                <th>Model</th>
                <th>Cena (PLN)</th>
                <th>Rok</th>
            </tr>
            
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><a href='car_details.php?id=" . $row["id"] . "'>" . $row["id"] . "</a></td>";
                    echo "<td>" . $row["marka"] . "</td>";
                    echo "<td>" . $row["model"] . "</td>";
                    echo "<td>" . number_format($row["cena"], 2, ',', ' ') . "</td>";
                    echo "<td>" . $row["rok"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Brak samochodów w bazie danych</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>