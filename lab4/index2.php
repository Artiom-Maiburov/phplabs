<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея изображений</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh; }
        header { background: #333; color: #fff; padding: 20px; text-align: center; }
        nav { background: #444; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        
        .container { flex: 1; padding: 20px; max-width: 1200px; margin: 0 auto; }
        
        /* Стили для сетки галереи */
        .gallery { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 15px; 
            margin-top: 20px; 
        }
        .gallery-item { 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            overflow: hidden; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .gallery-item:hover { transform: scale(1.03); }
        .gallery-item img { width: 100%; height: 150px; object-fit: cover; display: block; }
        .gallery-item p { margin: 5px; font-size: 12px; text-align: center; color: #666; }
        
        footer { background: #222; color: #aaa; text-align: center; padding: 15px; margin-top: auto; }
    </style>
</head>
<body>

    <header>
        <h1>Автоматическая Фотогалерея</h1>
    </header>

    <nav>
        <a href="#">Главная</a>
        <a href="#">Галерея</a>
        <a href="#">О нас</a>
        <a href="#">Контакты</a>
    </nav>

    <div class="container">
        <h2>Наши изображения</h2>
        <p>Ниже представлены файлы, автоматически считанные сервером из папки <code>image/</code>:</p>
        
        <div class="gallery">
            <?php
            $dir = 'image/';

            // Сканируем папку
            $files = scandir($dir);

            if ($files !== false) {
                for ($i = 0; $i < count($files); $i++) {
                    // Исключаем системные точки и берем только файлы с расширением .jpg
                    if ($files[$i] != "." && $files[$i] != "..") {
                        
                        // Проверка на расширение .jpg (чтобы не выводить лишние файлы)
                        $extension = strtolower(pathinfo($files[$i], PATHINFO_EXTENSION));
                        if ($extension === 'jpg') {
                            $path = $dir . $files[$i]; 
                            ?>
                            <div class="gallery-item">
                                <img src="<?= htmlspecialchars($path) ?>" alt="Изображение">
                                <p><?= htmlspecialchars($files[$i]) ?></p>
                            </div>
                            <?php
                        }
                    }
                }
            } else {
                echo "<p style='color:red;'>Ошибка: Не удалось прочитать директорию 'image/'</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Лабораторная работа по PHP. Все права защищены.</p>
    </footer>

</body>
</html>