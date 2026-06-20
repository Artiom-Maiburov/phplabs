<?php
declare(strict_types=1);

// ============================================================================
// ШАГ 5 (Задания 1 и 2). ООП Архитектура и Интерфейсы Валидации
// ============================================================================

// Интерфейс для валидаторов данных
interface ValidatorInterface {
    public function validate(mixed $value): bool;
    public function getErrorMessage(): string;
}

// Валидатор обязательного текстового поля
class RequiredTextValidator implements ValidatorInterface {
    public function __construct(private string $fieldName, private int $minLength = 3) {}
    public function validate(mixed $value): bool {
        return is_string($value) && mb_strlen(trim($value)) >= $this->minLength;
    }
    public function getErrorMessage(): string {
        return "Поле '{$this->fieldName}' должно содержать не менее {$this->minLength} символов.";
    }
}

// Валидатор даты
class DateValidator implements ValidatorInterface {
    public function __construct(private string $fieldName) {}
    public function validate(mixed $value): bool {
        if (!is_string($value) || empty($value)) return false;
        $d = DateTime::createFromFormat('Y-m-d', $value);
        return $d && $d->format('Y-m-d') === $value;
    }
    public function getErrorMessage(): string {
        return "Поле '{$this->fieldName}' должно содержать корректную дату в формате ГГГГ-ММ-ДД.";
    }
}

// Класс сущности "Странный факт" (Модель данных - ШАГ 1)
class StrangeFact {
    public function __construct(
        private string $title,        // string (минимум 6 полей всего)
        private string $source,       // string
        private string $description,  // text (длинный текст)
        private string $category,     // enum (категория)
        private string $discoveryDate,// date (дата)
        private int $weirdnessLevel   // число
    ) {}

    public function toArray(): array {
        return [
            'title' => $this->title,
            'source' => $this->source,
            'description' => $this->description,
            'category' => $this->category,
            'discovery_date' => $this->discoveryDate,
            'weirdness_level' => $this->weirdnessLevel,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
}

// Класс для работы с файловым хранилищем JSON (ШАГ 3 и 4)
class JsonStorage {
    private string $filePath;

    public function __construct(string $fileName = 'data.json') {
        $this->filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    }

    public function save(StrangeFact $fact): void {
        $data = $this->loadAll();
        $data[] = $fact->toArray();
        file_put_contents($this->filePath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function loadAll(): array {
        if (!file_exists($this->filePath)) {
            return [];
        }
        $content = file_get_contents($this->filePath);
        return json_decode($content, true) ?? [];
    }
}

// ============================================================================
// ОБРАБОТКА ДАННЫХ И ЛОГИКА СТРАНИЦЫ
// ============================================================================

$storage = new JsonStorage();
$errors = [];
$successMessage = "";

// ШАГ 3. Обработка POST-запроса и серверная валидация
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $source = trim($_POST['source'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $discoveryDate = trim($_POST['discovery_date'] ?? '');
    $weirdnessLevel = (int)($_POST['weirdness_level'] ?? 5);

    // Настройка цепочки валидаторов через интерфейс
    $validators = [
        'title' => new RequiredTextValidator('Название факта', 5),
        'source' => new RequiredTextValidator('Источник информации', 3),
        'description' => new RequiredTextValidator('Полное описание', 15),
        'discovery_date' => new DateValidator('Дата обнаружения/фиксации'),
    ];

    // Проверка лимитов для ENUM (категории)
    $allowedCategories = ['Природа', 'Космос', 'История', 'Анатомия', 'Наука'];
    if (!in_array($category, $allowedCategories, true)) {
        $errors[] = "Выбрана недопустимая категория факта.";
    }

    // Запуск валидации
    foreach ($validators as $field => $validator) {
        $valueToValidate = $_POST[$field] ?? '';
        if (!$validator->validate($valueToValidate)) {
            $errors[] = $validator->getErrorMessage();
        }
    }

    // Если ошибок нет — сохраняем объект
    if (empty($errors)) {
        $newFact = new StrangeFact($title, $source, $description, $category, $discoveryDate, $weirdnessLevel);
        $storage->save($newFact);
        $successMessage = "Ура! Странный факт успешно проверен сервером и сохранен в каталог.";
    }
}

// ШАГ 4. Чтение данных и сортировка
$facts = $storage->loadAll();
$sortField = $_GET['sort'] ?? 'created_at';

if (!empty($facts)) {
    usort($facts, function (array $a, array $b) use ($sortField) {
        return strcmp((string)$a[$sortField], (string)$b[$sortField]);
    });
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог Странных Фактов</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; padding: 30px; color: #333; }
        .container { max-width: 1100px; margin: 0 auto; }
        h1, h2 { color: #2c3e50; }
        .form-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 40px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #34495e; }
        input[type="text"], input[type="date"], select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        .radio-group { display: flex; gap: 15px; margin-top: 5px; }
        button { background: #3498db; color: white; border: none; padding: 12px 20px; font-size: 16px; border-radius: 4px; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #2980b9; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        
        /* Стили таблицы */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eceff1; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f9fbfb; }
        .sort-links { margin-bottom: 15px; background: #eaeded; padding: 10px; border-radius: 4px; }
        .sort-links a { margin-right: 15px; color: #2980b9; text-decoration: none; font-weight: bold; }
        .sort-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h1>🪐 Вселенский Каталог Странных Фактов</h1>
    <p>Лабораторная работа: Хранение данных, валидация по интерфейсам и ООП-подход.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Ошибка отправки:</strong>
            <ul>
                <?php foreach ($errors as $error): echo "<li>$error</li>"; endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>

    <div class="form-box">
        <h2>Добавить новый странный факт</h2>
        <form action="index.php" method="POST">
            
            <div class="form-group">
                <label for="title">Название факта (Краткая суть):</label>
                <input type="text" id="title" name="title" required minlength="5" maxlength="100" placeholder="Например: Дождь из алмазов на Сатурне">
            </div>

            <div class="form-group">
                <label for="category">Категория (Поле типа ENUM):</label>
                <select id="category" name="category" required>
                    <option value="">-- Выберите категорию --</option>
                    <option value="Природа">Природа</option>
                    <option value="Космос">Космос</option>
                    <option value="История">История</option>
                    <option value="Анатомия">Анатомия</option>
                    <option value="Наука">Наука</option>
                </select>
            </div>

            <div class="form-group">
                <label for="discovery_date">Дата фиксации/открытия (Поле типа DATE):</label>
                <input type="date" id="discovery_date" name="discovery_date" required>
            </div>

            <div class="form-group">
                <label>Уровень безумия/странности (Радио-кнопки):</label>
                <div class="radio-group">
                    <label><input type="radio" name="weirdness_level" value="1"> 1 (Слегка необычно)</label>
                    <label><input type="radio" name="weirdness_level" value="5" checked> 5 (Нормально)</label>
                    <label><input type="radio" name="weirdness_level" value="10"> 10 (Взорвет вам мозг)</label>
                </div>
            </div>

            <div class="form-group">
                <label for="source">Источник информации (String):</label>
                <input type="text" id="source" name="source" required minlength="3" placeholder="Например: Журнал Nature, Документы NASA">
            </div>

            <div class="form-group">
                <label for="description">Полное подробное описание факта (Поле типа TEXT):</label>
                <textarea id="description" name="description" required minlength="15" placeholder="Напишите развернутый и удивительный текст факта во всех подробностях..."></textarea>
            </div>

            <button type="submit">Зафиксировать факт в системе</button>
        </form>
    </div>

    <h2>🗂 Хранилище зафиксированных фактов</h2>
    
    <div class="sort-links">
        <strong>Сортировать по:</strong>
        <a href="index.php?sort=created_at">Дате добавления</a>
        <a href="index.php?sort=category">Категории</a>
        <a href="index.php?sort=discovery_date">Дате открытия</a>
        <a href="index.php?sort=title">Алфавиту (Названию)</a>
    </div>

    <?php if (empty($facts)): ?>
        <p>Каталог пуст. Добавьте первый факт с помощью формы выше!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Название факта</th>
                    <th>Категория</th>
                    <th>Дата открытия</th>
                    <th>Уровень</th>
                    <th>Источник</th>
                    <th>Подробное описание (Text)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facts as $fact): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($fact['title']) ?></strong></td>
                        <td><span style="background:#e0f2f1; padding:3px 8px; border-radius:10px; font-size:13px;"><?= htmlspecialchars($fact['category']) ?></span></td>
                        <td><?= htmlspecialchars($fact['discovery_date']) ?></td>
                        <td>🔥 <?= htmlspecialchars((string)$fact['weirdness_level']) ?>/10</td>
                        <td><em><?= htmlspecialchars($fact['source']) ?></em></td>
                        <td><?= nl2br(htmlspecialchars($fact['description'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>