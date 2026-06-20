<?php if (!empty($successMessage)): ?>
    <div class="alert"><?php echo e($successMessage); ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach($errors as $err) echo "<li>".e($err)."</li>"; ?></ul>
    </div>
<?php endif; ?>

<h3>Поиск аномалий</h3>
<form action="index.php" method="GET" style="display: flex; gap: 10px; margin-bottom: 30px;">
    <input type="hidden" name="page" value="home">
    <input type="text" name="search" placeholder="Введите ключевое слово для поиска факта..." value="<?php echo e($_GET['search'] ?? ''); ?>">
    <button type="submit">Найти</button>
</form>

<h2>Архив подтвержденных явлений</h2>
<?php if(empty($facts)): ?>
    <p>Записи не найдены или еще не прошли проверку модератора.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Название ресурса</th>
                <th>Категория</th>
                <th>Дата фиксации</th>
                <th>Уровень странности</th>
                <th>Подробное описание</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($facts as $fact): ?>
                <tr>
                    <td><strong><?php echo e($fact['title']); ?></strong></td>
                    <td><?php echo e($fact['category']); ?></td>
                    <td><?php echo e($fact['discovery_date']); ?></td>
                    <td>🔥 <?php echo e((string)$fact['weirdness_level']); ?>/10</td>
                    <td><?php echo nl2br(e($fact['description'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr style="margin: 40px 0;">

<h3>Зафиксировать новое явление в системе</h3>
<form action="index.php?page=home" method="POST">
    <input type="hidden" name="create_fact" value="1">
    
    <div class="form-group">
        <label>Заголовок происшествия (Тип: Text):</label>
        <input type="text" name="title" required minlength="5" placeholder="Минимум 5 символов">
    </div>

    <div class="form-group">
        <label>Категория (Тип: Select/Enum):</label>
        <select name="category" required>
            <option value="Космос">Космос</option>
            <option value="Природа">Природа</option>
            <option value="Наука">Наука</option>
            <option value="История">История</option>
        </select>
    </div>

    <div class="form-group">
        <label>Дата происшествия (Тип: Date):</label>
        <input type="date" name="discovery_date" required>
    </div>

    <div class="form-group">
        <label>Уровень аномальности от 1 до 10 (Тип: Number/Range):</label>
        <input type="number" name="weirdness_level" min="1" max="10" value="5" required>
    </div>

    <div class="form-group">
        <label>Полное развернутое описание сути явления (Тип: Textarea):</label>
        <textarea name="description" required minlength="15" placeholder="Опишите все детали происшествия..."></textarea>
    </div>

    <button type="submit">Отправить на модерацию</button>
</form>