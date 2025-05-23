<?php

$servername = "localhost";
$username = "root";  // Domyślny użytkownik, zmienić jeśli potrzeba
$password = "";      // Domyślne hasło, zmienić jeśli potrzeba


$conn = new mysqli($servername, $username, $password);


if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS mojaBaza";
if ($conn->query($sql) === TRUE) {
    echo "Baza danych utworzona pomyślnie<br>";
} else {
    echo "Błąd podczas tworzenia bazy danych: " . $conn->error;
}

$conn->select_db("mojaBaza");

// Utworzenie tabeli
$sql = "CREATE TABLE IF NOT EXISTS samochody (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    marka VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    cena FLOAT NOT NULL,
    rok INT(4) NOT NULL,
    opis TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabela 'samochody' utworzona pomyślnie<br>";
} else {
    echo "Błąd podczas tworzenia tabeli: " . $conn->error;
}

$result = $conn->query("SELECT COUNT(*) as count FROM samochody");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $sql = "INSERT INTO samochody (marka, model, cena, rok, opis) VALUES
    ('Toyota', 'Corolla', 65000, 2018, 'Niezawodny samochód miejski z silnikiem 1.6'),
    ('Volkswagen', 'Golf', 85000, 2020, 'Komfortowy hatchback, niski przebieg'),
    ('Ford', 'Focus', 45000, 2015, 'Ekonomiczny, idealny dla rodziny'),
    ('Škoda', 'Octavia', 55000, 2017, 'Przestronne wnętrze, bogate wyposażenie'),
    ('Honda', 'Civic', 80000, 2019, 'Sportowy wygląd, dynamiczny silnik'),
    ('Audi', 'A4', 120000, 2021, 'Luksusowe wnętrze, najnowsza technologia'),
    ('BMW', 'Seria 3', 130000, 2022, 'Elegancki sedan z mocnym silnikiem'),
    ('Renault', 'Clio', 35000, 2016, 'Ekonomiczny samochód miejski, niskie spalanie')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Przykładowe dane zostały dodane<br>";
    } else {
        echo "Błąd dodawania danych: " . $conn->error;
    }
}

$conn->close();

echo "<p>Konfiguracja zakończona. <a href='index.php'>Przejdź do strony głównej</a></p>";
?>