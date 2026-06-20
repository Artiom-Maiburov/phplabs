<?php
declare(strict_types=1);

class AuthController {
    public function handleRequest(): void {
        $errors = [];
        $db = getDBConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (strlen($username) < 3) $errors[] = "Имя пользователя слишком короткое.";
            if (strlen($password) < 6) $errors[] = "Пароль должен быть не менее 6 символов.";

            if (empty($errors)) {
                if ($action === 'register') {
                    // Хеширование паролей по требованиям безопасности
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    try {
                        $stmt = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'user')");
                        $stmt->execute([$username, $hash]);
                        $_SESSION['success'] = "Регистрация успешна! Теперь вооидите.";
                    } catch (PDOException $e) {
                        $errors[] = "Такой пользователь уже существует.";
                    }
                } elseif ($action === 'login') {
                    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password_hash'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        header("Location: index.php");
                        exit;
                    } else {
                        $errors[] = "Неверное имя пользователя или пароль.";
                    }
                }
            }
        }

        $view = 'Views/login.php';
        include 'Views/layout.php';
    }

    public function logout(): void {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}