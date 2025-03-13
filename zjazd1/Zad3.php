<?php
function fibonacciSeries($n) {
    $fibonacci = array(); 

    $fibonacci[0] = 0;
    $fibonacci[1] = 1;

    for ($i = 2; $i < $n; $i++) {
        $fibonacci[$i] = $fibonacci[$i - 1] + $fibonacci[$i - 2];
    }

    return $fibonacci;
}

$n = 10; 

$fibonacciNumbers = fibonacciSeries($n);

$lineNumber = 1;
foreach ($fibonacciNumbers as $num) {
    if ($num % 2 != 0) { 
        echo "$lineNumber. $num\n";
        $lineNumber++;
    }
}
?>
