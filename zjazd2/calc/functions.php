<?php
function dodawanie($number1, $number2) {
    return $number1 + $number2;
}

function odejmowanie($number1, $number2) {
    return $number1 - $number2;
}

function mnozenie($number1, $number2) {
    return $number1 * $number2;
}

function dzielenie($number1, $number2) {
    if ($number2 == 0) {
        return "Nie można dzielić przez zero!";
    }
    return $number1 / $number2;
}
?>