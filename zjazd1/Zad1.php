<?php 
$fruits = array('apple', 'banana', 'orange', 'pineapple', "pear");

foreach ($fruits as $fruit) {
    $reverse = '';
    for ($i = strlen($fruit) - 1; $i >= 0; $i--) {
        $reverse .= $fruit[$i];
    }
    echo $reverse . "\n";

    $firstChar = $fruit[0]; 

    if ($firstChar === 'p') {
        echo "Fruit starts with 'p': " . $fruit . "\n";
    }
}
?>
