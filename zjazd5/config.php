<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "mojaBaza";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>