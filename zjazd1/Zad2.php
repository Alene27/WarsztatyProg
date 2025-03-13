<?php
function isPrime($num) {
    if ($num < 2) return false; 
    for ($i = 2; $i * $i <= $num; $i++) {
        if ($num % $i == 0) {
            return false;
        }
    }
    return true;
}

echo "Podaj początek zakresu: ";
$start = (int) trim(fgets(STDIN));

echo "Podaj koniec zakresu: ";
$end = (int) trim(fgets(STDIN));

echo "Liczby pierwsze w zakresie $start - $end:\n";
for ($num = $start; $num <= $end; $num++) {
    if (isPrime($num)) {
        echo $num . " ";
    }
}
echo "\n";
?>
