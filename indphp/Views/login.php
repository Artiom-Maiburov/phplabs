<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach($errors as $err) echo "<li>".e($err)."</li>"; ?></ul>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
    <div>
        <h3>Вход в систему</h3>
        <form action="index.php?page=auth" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Имя пользователя:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Войти</button>
        </form>
    </div>

    <div style="border-left: 1px solid #ddd; padding-left: 40px;">
        <h3>Регистрация аккаунта</h3>
        <form action="index.php?page=auth" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label>Придумайте имя:</label>
                <input type="text" name="username" required minlength="3">
            </div>
            <div class="form-group">
                <label>Придумайте сильный пароль:</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <button type="submit" style="background: #2ecc71;">Зарегистрироваться</button>
        </form>
    </div>
</div>