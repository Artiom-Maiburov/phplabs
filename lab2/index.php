<?php
// Первая программа
echo "Привет, мир!";
echo "<br />"; 
// Разные способы вывода "Привет мир"
echo "Hello, World with echo!";
echo "<br />";

print "Hello, World with print!";
echo "<br />";

//Конкатенация строк

$days = 288;
$message = "Все возвращаются на работу!";

echo "Способ 1 (конкатенация): " . $days . " дней. " . $message . "<br />";

echo "Способ 2 (двойные кавычки): $days дней. $message <br />";

?>