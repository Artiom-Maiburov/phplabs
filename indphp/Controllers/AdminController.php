<?php
declare(strict_types=1);

class AdminController {
    public function __construct() {
        // Жесткая проверка прав доступа (Защищенный компонент)
        if (($_SESSION['role'] ?? '') !== 'admin') {
            die("Доступ заблокирован: Требуются права Администратора.");
        }
    }

    public function index(): void {
        $db = getDBConnection();
        $adminErrors = [];

        // Функция 1: Создание новых администраторов
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_admin'])) {
            $user = trim($_POST['username'] ?? '');
            $pass = $_POST['password'] ?? '';
            if (strlen($user) >= 3 && strlen($pass) >= 6) {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
                $stmt->execute([$user, $hash]);
            } else {
                $adminErrors[] = "Ошибка данных нового администратора.";
            }
        }

        // Функция 2, 3, 4: CRUD операции управления базой данных (Одобрение/Удаление)
        if (isset($_GET['approve'])) {
            $stmt = $db->prepare("UPDATE facts SET is_verified = 1 WHERE id = ?");
            $stmt->execute([(int)$_GET['approve']]);
        }
        if (isset($_GET['delete'])) {
            $stmt = $db->prepare("DELETE FROM facts WHERE id = ?");
            $stmt->execute([(int)$_GET['delete']]);
        }

        // Получение списков для вывода
        $allFacts = $db->query("SELECT * FROM facts ORDER BY is_verified ASC, created_at DESC")->fetchAll();
        $admins = $db->query("SELECT id, username, created_at FROM users WHERE role = 'admin'")->fetchAll();

        $view = 'Views/admin.php';
        include 'Views/layout.php';
    }
}