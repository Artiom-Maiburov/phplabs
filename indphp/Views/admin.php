<h2>Панель администратора системы</h2>

<?php if (!empty($adminErrors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach($adminErrors as $err) echo "<li>".e($err)."</li>"; ?></ul>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <div>
        <h3>Модерация и управление данными (CRUD / Списки)</h3>
        <table>
            <thead>
                <tr>
                    <th>Ресурс</th>
                    <th>Категория</th>
                    <th>Статус</th>
                    <th>Действия администратора</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allFacts as $f): ?>
                    <tr>
                        <td><strong><?php echo e($f['title']); ?></strong></td>
                        <td><?php echo e($f['category']); ?></td>
                        <td>
                            <?php echo $f['is_verified'] ? '<span style="color:green;">Активен</span>' : '<span style="color:orange;">В обработке</span>'; ?>
                        </td>
                        <td>
                            <?php if(!$f['is_verified']): ?>
                                <a href="index.php?page=admin&approve=<?php echo $f['id']; ?>" style="color: green; font-weight: bold; margin-right: 10px;">Одобрить публикацию</a>
                            <?php endif; ?>
                            <a href="index.php?page=admin&delete=<?php echo $f['id']; ?>" style="color: red;" onclick="return confirm('Вы уверены, что хотите безвозвратно удалить этот ресурс?')">Удалить из БД</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="background: #fdfefe; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <h3>Создать учетную запись Администратора</h3>
        <form action="index.php?page=admin" method="POST">
            <input type="hidden" name="new_admin" value="1">
            <div class="form-group">
                <label>Имя нового админа:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" style="width: 100%;">Назначить администратором</button>
        </form>

        <h4 style="margin-top: 30px;">Действующие администраторы:</h4>
        <ul>
            <?php foreach($admins as $a): ?>
                <li><strong><?php echo e($a['username']); ?></strong> (Добавлен: <?php echo $a['created_at']; ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>