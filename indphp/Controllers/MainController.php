<?php
declare(strict_types=1);

class MainController {
    public function index(): void {
        $db = getDBConnection();
        $search = trim($_GET['search'] ?? '');
        $successMessage = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);
        $errors = [];

        // Форма создания ресурса (POST) с валидацией (Форма 1)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_fact'])) {
            $title = trim($_POST['title'] ?? '');
            $category = $_POST['category'] ?? '';
            $date = $_POST['discovery_date'] ?? '';
            $level = (int)($_POST['weirdness_level'] ?? 0);
            $desc = trim($_POST['description'] ?? '');

            if (strlen($title) < 5) $errors[] = "Заголовок должен содержать от 5 символов.";
            if (empty($date)) $errors[] = "Укажите корректную дату.";
            if ($level < 1 || $level > 10) $errors[] = "Уровень странности должен быть от 1 до 10.";

            if (empty($errors)) {
                $stmt = $db->prepare("INSERT INTO facts (title, category, discovery_date, weirdness_level, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $category, $date, $level, $desc]);
                $successMessage = "Ресурс успешно отправлен на модерацию!";
            }
        }

        // Форма поиска (Форма 2) + Динамический вывод из БД
        if (!empty($search)) {
            $stmt = $db->prepare("SELECT * FROM facts WHERE is_verified = 1 AND (title LIKE ? OR description LIKE ?)");
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $db->query("SELECT * FROM facts WHERE is_verified = 1 ORDER BY created_at DESC");
        }
        $facts = $stmt->fetchAll();

        $view = 'Views/home.php';
        include 'Views/layout.php';
    }
}