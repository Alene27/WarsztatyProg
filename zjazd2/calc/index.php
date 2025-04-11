<?php
require_once 'functions.php';

if (isset($_POST['oblicz'])) {
    $number1 = $_POST['liczba1'];
    $number2 = $_POST['liczba2'];
    $action = $_POST['dzialanie'];
    
    // Use switch instead of multiple if statements
    switch ($action) {
        case 'dodawanie':
            $wynik = dodawanie($number1, $number2);
            break;
        case 'odejmowanie':
            $wynik = odejmowanie($number1, $number2);
            break;
        case 'mnozenie':
            $wynik = mnozenie($number1, $number2);
            break;
        case 'dzielenie':
            $wynik = dzielenie($number1, $number2);
            break;
        default:
            $wynik = "Nieznane działanie";
    }
    
    // Display the result
    echo '<div class="result">' . $wynik . '</div>';
}
?>
