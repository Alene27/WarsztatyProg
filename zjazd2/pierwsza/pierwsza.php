<?php
function is_prime($number, &$iterations) {
    if ($number < 2) return false;
    if ($number == 2 || $number == 3) return true;
    if ($number % 2 == 0 || $number % 3 == 0) return false;
    
    $iterations = 0;
    for ($i = 5; $i * $i <= $number; $i += 6) {
        $iterations++;
        if ($number % $i == 0 || $number % ($i + 2) == 0) return false;
    }
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number = filter_input(INPUT_POST, 'number', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $iterations = 0;

    if ($number === false) {
        $result = "Podaj poprawną liczbę całkowitą większą od 0.";
    } else {
        $isPrime = is_prime($number, $iterations);
        $result = $isPrime ? "$number jest liczbą pierwszą." : "$number nie jest liczbą pierwszą.";
        $result .= "<br>Liczba iteracji: $iterations";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprawdzanie liczby pierwszej</title>
</head>
<body>
    <form method="post">
        <label>Podaj liczbę: <input type="number" name="number" required></label>
        <button type="submit">Sprawdź</button>
    </form>
    <?php if (isset($result)) echo "<p>$result</p>"; ?>
</body>
</html>
