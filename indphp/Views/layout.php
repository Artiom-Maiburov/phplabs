<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Портал Аномальных Явлений</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f3f5; margin: 0; padding: 0; }
        header { background: #2c3e50; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #ecf0f1; text-decoration: none; margin-left: 15px; font-weight: bold; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="password"], input[type="date"], select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; background: #e8f5e9; color: #2e7d32; }
        .alert-danger { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <header>
        <h2>🛸 StrangePortal</h2>
        <nav>
            <a href="index.php?page=home">Главная</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?page=admin" style="color: #e67e22;">Панель Администратора</a>
                <?php endif; ?>
                <span>(Привет, <?php echo e($_SESSION['username']); ?>)</span>
                <a href="index.php?page=logout">Выйти</a>
            <?php else: ?>
                <a href="index.php?page=auth">Войти / Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
        <?php include $view; ?>
    </div>
</body>
</html>