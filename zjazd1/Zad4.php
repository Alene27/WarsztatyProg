<?php
$text = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
been the industry's standard dummy text ever since the 1500s, when an unknown printer took a
galley of type and scrambled it to make a type specimen book. It has survived not only five
centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was
popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages,
and more recently with desktop publishing software like Aldus PageMaker including versions of
Lorem Ipsum.";

$words = explode(" ", $text);
$cleanWords = [];

$interpunction = array('.', ',', '!', '?', ':', ';', '"', "'", '(', ')', '[', ']', '{', '}');

foreach ($words as $word) {
    if (in_array($word, $interpunction)) {
        continue;
    }
    
    $lastChar = substr($word, -1);
    if (in_array($lastChar, $interpunction)) {
        $word = substr($word, 0, -1);
    }
    
    if (!empty($word)) {
        $cleanWords[] = $word;
    }
}

$assocArray = [];
for ($i = 0; $i < count($cleanWords) - 1; $i += 2) {
    $assocArray[$cleanWords[$i]] = $cleanWords[$i + 1];
}

foreach ($assocArray as $key => $value) {
    echo "$key $value\n";
}
?>