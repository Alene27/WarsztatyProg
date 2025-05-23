<?php
require_once "config.php";


$sql = "SELECT * FROM samochody ORDER BY rok DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Samochodowy - Wszystkie Samochody</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="nav">
        <a href="index.php">Strona główna</a>
        <a href="all_cars.php" class="active">Wszystkie samochody</a>
        <a href="add_car.php">Dodaj samochód</a>
    </div>
    
    <div class="container">
        <h1>Wszystkie Samochody</h1>
        <h2>Posortowane według rocznika (od najnowszych)</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Marka</th>
                <th>Model</th>
                <th>Cena (PLN)</th>
                <th>Rok</th>
                <th>Akcje</th>
            </tr>
            
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["marka"] . "</td>";
                    echo "<td>" . $row["model"] . "</td>";
                    echo "<td>" . number_format($row["cena"], 2, ',', ' ') . "</td>";
                    echo "<td>" . $row["rok"] . "</td>";
                    echo "<td><a href='car_details.php?id=" . $row["id"] . "' class='btn'>Szczegóły</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Brak samochodów w bazie danych</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>