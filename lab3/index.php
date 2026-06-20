<?php

// Графики работы
$currentDay = (int)date('N'); 

// Логика для John Styles (Пн, Ср, Пт -> 1, 3, 5)
if ($currentDay === 1 || $currentDay === 3 || $currentDay === 5) {
    $johnSchedule = "8:00-12:00";
} else {
    $johnSchedule = "Нерабочий день";
}

// Логика для Jane Doe (Вт, Чт, Сб -> 2, 4, 6)
if ($currentDay === 2 || $currentDay === 4 || $currentDay === 6) {
    $janeSchedule = "12:00-16:00";
} else {
    $janeSchedule = "Нерабочий день";
}
?>

<h2>График работы сотрудников</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>№</th>
            <th>Фамилия Имя</th>
            <th>График работы</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>John Styles</td>
            <td><strong><?php echo $johnSchedule; ?></strong></td>
        </tr>
        <tr>
            <td>2</td>
            <td>Jane Doe</td>
            <td><strong><?php echo $janeSchedule; ?></strong></td>
        </tr>
    </tbody>
</table>

<hr />

<?php
// Циклы

echo "<h2>Работа с циклами</h2>";

// for
echo "<h3>1. Цикл FOR (с промежуточными шагами):</h3>";
$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
   $a += 10;
   $b += 5;

   echo "Шаг $i: a = $a, b = $b<br />"; 
}
echo "<strong>End of the loop (FOR): a = $a, b = $b</strong><br />";


// while
echo "<h3>2. Реализация через WHILE:</h3>";
$a = 0;
$b = 0;
$i = 0; 

while ($i <= 5) {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b<br />";
    $i++; 
}
echo "<strong>End of the loop (WHILE): a = $a, b = $b</strong><br />";


// do while
echo "<h3>3. Реализация через DO-WHILE:</h3>";
$a = 0;
$b = 0;
$i = 0; 

do {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b<br />";
    $i++;
} while ($i <= 5);
echo "<strong>End of the loop (DO-WHILE): a = $a, b = $b</strong><br />";

?>