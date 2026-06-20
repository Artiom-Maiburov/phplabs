<?php
declare(strict_types=1);

// ============================================================================
// Задание 1.2. Создание исходного массива транзакций
// ============================================================================
$transactions = [
    [
        "id" => 1,
        "date" => "2024-03-01",
        "amount" => 120.50,
        "description" => "Payment for groceries in SuperMart",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2025-06-15",
        "amount" => 75.00,
        "description" => "Dinner with friends at weekend",
        "merchant" => "Local Restaurant",
    ],
    [
        "id" => 3,
        "date" => "2026-01-10",
        "amount" => 1200.00,
        "description" => "New laptop purchase for work",
        "merchant" => "TechZone",
    ],
    [
        "id" => 4,
        "date" => "2026-05-20",
        "amount" => 45.30,
        "description" => "Monthly Spotify premium subscription",
        "merchant" => "Spotify",
    ],
];

// ============================================================================
// Задание 1.4. Реализация функций
// ============================================================================

// 1. Расчет общей суммы всех транзакций
function calculateTotalAmount(array $transactions): float {
    $total = 0.0;
    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }
    return $total;
}

// 2. Поиск транзакций по части описания (возвращает массив совпадений)
function findTransactionByDescription(array $transactions, string $descriptionPart): array {
    $result = [];
    foreach ($transactions as $transaction) {
        if (str_contains(strtolower($transaction['description']), strtolower($descriptionPart))) {
            $result[] = $transaction;
        }
    }
    return $result;
}

// 3а. Поиск транзакции по ID с помощью цикла foreach
function findTransactionByIdForeach(array $transactions, int $id): ?array {
    foreach ($transactions as $transaction) {
        if ($transaction['id'] === $id) {
            return $transaction;
        }
    }
    return null;
}

// 3б. Поиск транзакции по ID с помощью array_filter (на высшую оценку)
function findTransactionById(array $transactions, int $id): ?array {
    $filtered = array_filter($transactions, function (array $transaction) use ($id) {
        return $transaction['id'] === $id;
    });
    // Возвращаем первый найденный элемент или null, если ничего не нашлось
    return !empty($filtered) ? array_values($filtered)[0] : null;
}

// 4. Подсчет количества дней между датой транзакции и сегодняшним днем
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime(); // Текущая дата и время
    
    // Сбрасываем время до 00:00:00, чтобы считать только чистые дни
    $transactionDate->setTime(0, 0, 0);
    $currentDate->setTime(0, 0, 0);
    
    $interval = $transactionDate->diff($currentDate);
    return (int)$interval->format('%r%a'); // %r вернет знак минус, если дата в будущем
}

// 5. Добавление новой транзакции (используем глобальный массив по условию)
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void {
    global $transactions;
    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant
    ];
}

// ============================================================================
// Тестирование функций добавления и сортировки
// ============================================================================

// Добавим одну трансляцию через функцию (Задание 1.4.5)
addTransaction(5, "2026-02-18", 320.00, "Gym membership renewal", "FitLife");

// --- Задание 1.5. Сортировка транзакций ---

// Вариант А: Сортировка по дате (от старых к новым)
usort($transactions, function (array $a, array $b) {
    return strtotime($a['date']) <=> strtotime($b['date']);
});

// Вариант Б: Сортировка по сумме (по убыванию) — раскомментируйте, если приоритетнее она:
/*
usort($transactions, function (array $a, array $b) {
    return $b['amount'] <=> $a['amount'];
});
*/
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление транзакциями</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dddddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #eaf2f8; }
        .search-box { background: #f9f9f9; padding: 15px; border: 1px solid #ccc; margin-top: 30px; }
    </style>
</head>
<body>

    <h2>Список банковских транзакций</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Дней прошло</th>
                <th>Сумма</th>
                <th>Описание</th>
                <th>Организация (Merchant)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction['id'] ?></td>
                    <td><?= $transaction['date'] ?></td>
                    <td><?= daysSinceTransaction($transaction['date']) ?> дн.</td>
                    <td>$<?= number_format($transaction['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($transaction['description']) ?></td>
                    <td><?= htmlspecialchars($transaction['merchant']) ?></td>
                </tr>
            <?php endforeach; ?>
            
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Итоговая сумма:</td>
                <td colspan="3">$<?= number_format(calculateTotalAmount($transactions), 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="search-box">
        <h3>Тестирование функций поиска (Демо)</h3>
        <?php
            // Поиск по ID (высший балл через array_filter)
            $searchId = 3;
            $foundById = findTransactionById($transactions, $searchId);
            echo "<p><strong>Поиск по ID ($searchId):</strong> " . 
                 ($foundById ? $foundById['description'] . " на сумму $" . $foundById['amount'] : "Не найдено") . "</p>";

            // Поиск по описанию
            $keyword = "groceries";
            $foundByDesc = findTransactionByDescription($transactions, $keyword);
            echo "<p><strong>Поиск по слову '$keyword' (найдено " . count($foundByDesc) . "):</strong></p><ul>";
            foreach ($foundByDesc as $t) {
                echo "<li>ID {$t['id']}: {$t['description']} ({$t['merchant']})</li>";
            }
            echo "</ul>";
        ?>
    </div>

</body>
</html>