<?php
declare(strict_types=1);

// ============================================================================
// Задание 7. Интерфейс TransactionStorageInterface
// ============================================================================
interface TransactionStorageInterface {
    public function addTransaction(Transaction $transaction): void;
    public function removeTransactionById(int $id): void;
    public function getAllTransactions(): array;
    public function findById(int $id): ?Transaction;
}

// ============================================================================
// Задание 2. Класс Transaction
// ============================================================================
class Transaction {
    private DateTime $dateObj;

    public function __construct(
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant
    ) {
        $this->dateObj = new DateTime($this->date);
    }

    // Метод подсчета дней с момента транзакции до текущей даты
    public function getDaysSinceTransaction(): int {
        $currentDate = new DateTime();
        
        // Сбрасываем время до полуночи, чтобы считать чистые дни
        $this->dateObj->setTime(0, 0, 0);
        $currentDate->setTime(0, 0, 0);
        
        $interval = $this->dateObj->diff($currentDate);
        return (int)$interval->format('%r%a'); 
    }

    // Геттеры
    public function getId(): int { return $this->id; }
    public function getDate(): string { return $this->date; }
    public function getAmount(): float { return $this->amount; }
    public function getDescription(): string { return $this->description; }
    public function getMerchant(): string { return $this->merchant; }
}

// ============================================================================
// Задание 3. Класс TransactionRepository
// ============================================================================
class TransactionRepository implements TransactionStorageInterface {
    /** @var Transaction[] */
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void {
        $this->transactions[] = $transaction;
    }

    public function removeTransactionById(int $id): void {
        $this->transactions = array_filter($this->transactions, function (Transaction $t) use ($id) {
            return $t->getId() !== $id;
        });
        // Сбрасываем ключи после удаления элементов
        $this->transactions = array_values($this->transactions);
    }

    public function getAllTransactions(): array {
        return $this->transactions;
    }

    public function findById(int $id): ?Transaction {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }
        return null;
    }
}

// ============================================================================
// Задание 4. Класс TransactionManager
// ============================================================================
class TransactionManager {
    // Внедрение зависимости через интерфейс (Задание 7)
    public function __construct(
        private TransactionStorageInterface $repository
    ) {}

    public function calculateTotalAmount(): float {
        $total = 0.0;
        foreach ($this->repository->getAllTransactions() as $t) {
            $total += $t->getAmount();
        }
        return $total;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $t) {
            $tDate = new DateTime($t->getDate());
            if ($tDate >= $start && $tDate <= $end) {
                $total += $t->getAmount();
            }
        }
        return $total;
    }

    public function countTransactionsByMerchant(string $merchant): int {
        $count = 0;
        foreach ($this->repository->getAllTransactions() as $t) {
            if (strcasecmp($t->getMerchant(), $merchant) === 0) {
                $count++;
            }
        }
        return $count;
    }

    /** @return Transaction[] */
    public function sortTransactionsByDate(): array {
        $list = $this->repository->getAllTransactions();
        usort($list, function (Transaction $a, Transaction $b) {
            return strtotime($a->getDate()) <=> strtotime($b->getDate());
        });
        return $list;
    }

    /** @return Transaction[] */
    public function sortTransactionsByAmountDesc(): array {
        $list = $this->repository->getAllTransactions();
        usort($list, function (Transaction $a, Transaction $b) {
            return $b->getAmount() <=> $a->getAmount();
        });
        return $list;
    }
}

// ============================================================================
// Задание 5. Класс TransactionTableRenderer
// ============================================================================
final class TransactionTableRenderer {
    public function render(array $transactions): string {
        $html = "
        <style>
            table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f4f6f7; color: #333; }
            tr:nth-child(even) { background-color: #f9f9f9; }
        </style>
        <table>
            <thead>
                <tr>
                    <th>ID транзакции</th>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Описание</th>
                    <th>Название получателя</th>
                    <th>Категория получателя</th>
                    <th>Количество дней прошло</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($transactions as $t) {
            // Фейковая простая логика для вывода категории получателя
            $category = $this->detectCategory($t->getMerchant());
            
            $html .= "<tr>
                <td>{$t->getId()}</td>
                <td>{$t->getDate()}</td>
                <td>$" . number_format($t->getAmount(), 2) . "</td>
                <td>" . htmlspecialchars($t->getDescription()) . "</td>
                <td>" . htmlspecialchars($t->getMerchant()) . "</td>
                <td>{$category}</td>
                <td>{$t->getDaysSinceTransaction()} дн.</td>
            </tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }

    private function detectCategory(string $merchant): string {
        $m = strtolower($merchant);
        if (str_contains($m, 'mart') || str_contains($m, 'market')) return 'Продукты';
        if (str_contains($m, 'restaurant') || str_contains($m, 'cafe')) return 'Рестораны';
        if (str_contains($m, 'tech') || str_contains($m, 'apple')) return 'Техника';
        if (str_contains($m, 'spotify') || str_contains($m, 'netflix')) return 'Развлечения';
        if (str_contains($m, 'gas') || str_contains($m, 'oil')) return 'Транспорт';
        return 'Разное';
    }
}

// ============================================================================
// Задание 6. Начальные данные и тестирование системы
// ============================================================================

$repository = new TransactionRepository();

// Создаем 10 уникальных объектов Transaction
$repository->addTransaction(new Transaction(1, "2025-01-10", 120.50, "Weekly groceries shopping", "SuperMart"));
$repository->addTransaction(new Transaction(2, "2025-05-14", 45.00, "Dinner with colleagues", "Local Restaurant"));
$repository->addTransaction(new Transaction(3, "2026-02-20", 1500.00, "MacBook Pro upgrade for coding", "TechZone"));
$repository->addTransaction(new Transaction(4, "2026-06-01", 14.99, "Family plan subscription", "Spotify"));
$repository->addTransaction(new Transaction(5, "2026-06-10", 65.20, "Full tank refueling", "GasStation"));
$repository->addTransaction(new Transaction(6, "2026-06-12", 210.00, "New running sneakers", "SportMarket"));
$repository->addTransaction(new Transaction(7, "2026-06-15", 35.00, "Business lunch", "Central Cafe"));
$repository->addTransaction(new Transaction(8, "2026-06-16", 12.50, "Morning espresso and croissant", "Central Cafe"));
$repository->addTransaction(new Transaction(9, "2026-06-17", 89.90, "Electric kettle for kitchen", "TechZone"));
$repository->addTransaction(new Transaction(10, "2026-06-18", 300.00, "Online PHP framework course", "Udemy Tech"));

// Инициализируем менеджер бизнес-логики и рендерер таблиц
$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ООП Управление Транзакциями</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        .block { background: #fcfcfc; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 25px; }
        h2 { color: #2c3e50; border-bottom: 2px solid #34495e; padding-bottom: 5px; }
        h3 { color: #16a085; }
    </style>
</head>
<body>

    <h1>Панель управления транзакциями (ООП)</h1>

    <h2>1. Все транзакции (в порядке добавления)</h2>
    <?= $renderer->render($repository->getAllTransactions()) ?>

    <h2>2. Сортировка данных</h2>
    
    <h3>Транзакции, отсортированные по дате:</h3>
    <?= $renderer->render($manager->sortTransactionsByDate()) ?>

    <h3>Транзакции, отсортированные по сумме (по убыванию):</h3>
    <?= $renderer->render($manager->sortTransactionsByAmountDesc()) ?>

    <h2>3. Бизнес-аналитика (TransactionManager)</h2>
    <div class="block">
        <p><strong>Общая сумма всех транзакций:</strong> $<?= number_format($manager->calculateTotalAmount(), 2) ?></p>
        
        <p><strong>Сумма транзакций за июнь 2026 года (2026-06-01 — 2026-06-15):</strong> 
           $<?= number_format($manager->calculateTotalAmountByDateRange("2026-06-01", "2026-06-15"), 2) ?></p>
        
        <p><strong>Количество транзакций в "Central Cafe":</strong> 
           <?= $manager->countTransactionsByMerchant("Central Cafe") ?></p>
    </div>

    <h2>4. Проверка удаления и поиска по ID</h2>
    <div class="block">
        <?php
        // Удалим транзакцию с ID = 5 (транспорт)
        $repository->removeTransactionById(5);
        echo "<p style='color: brown;'><em>Транзакция с ID = 5 успешно удалена из репозитория.</em></p>";
        
        // Пробуем найти удаленный объект
        $search = $repository->findById(5);
        echo "<p>Поиск ID 5 после удаления: " . ($search ? "Найдена" : "<strong>Не найдена (null)</strong>") . "</p>";
        
        // Пробуем найти существующий объект
        $searchValid = $repository->findById(3);
        echo "<p>Поиск ID 3: " . ($searchValid ? "Найдена (" . $searchValid->getDescription() . ")" : "Не найдена") . "</p>";
        ?>
    </div>

    <h2>5. Актуальная таблица после удаления ID 5</h2>
    <?= $renderer->render($repository->getAllTransactions()) ?>

</body>
</html>