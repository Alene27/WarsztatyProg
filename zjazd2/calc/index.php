<?php
    $number1 = $_POST['liczba1'];
    $number2 = $_POST['liczba2'];

    $action = $_POST['dzialanie'];

    if ($action == 'dodawanie') {
        echo '<div class="result">';
        $wynik = $number1 + $number2;
        echo "$wynik";
        echo '</div>';
    }
    if ($action == 'odejmowanie') {
        echo '<div class="result">';
        $wynik = $number1 - $number2;
        echo "$wynik";
        echo '</div>';
    }
    if ($action == 'mnozenie') {
        echo '<div class="result">';
        $wynik = $number1 * $number2;
        echo "$wynik";
        echo '</div>';
    }
    if ($action == 'dzielenie') {
        echo '<div class="result">';
        $wynik = $number1 / $number2;
        echo "$wynik";
        echo '</div>';
    }
?>