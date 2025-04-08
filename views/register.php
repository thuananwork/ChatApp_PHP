<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h2>Đăng ký</h2>
            <?php if (isset($error)) : ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($success)) : ?>
                <p class="success" style="color: #28a745; font-weight: bold;"><?= htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form method="post" action="index.php?action=register">
                <label>Tên hiển thị:</label>
                <input type="text" name="username" placeholder="Nhập tên của bạn" value="<?= !isset($success) ? htmlspecialchars($_POST['username'] ?? '') : ''; ?>">
                <?php if (isset($errors['username'])) : ?>
                    <p class="error"><?= htmlspecialchars($errors['username']); ?></p>
                <?php endif; ?>

                <label>Email:</label>
                <input type="text" name="email" placeholder="Nhập email" value="<?= !isset($success) ? htmlspecialchars($_POST['email'] ?? '') : ''; ?>">
                <?php if (isset($errors['email'])) : ?>
                    <p class="error"><?= htmlspecialchars($errors['email']); ?></p>
                <?php endif; ?>

                <label>Số điện thoại:</label>
                <input type="tel" name="phone" placeholder="Nhập số điện thoại" value="<?= !isset($success) ? htmlspecialchars($_POST['phone'] ?? '') : ''; ?>">
                <?php if (isset($errors['phone'])) : ?>
                    <p class="error"><?= htmlspecialchars($errors['phone']); ?></p>
                <?php endif; ?>

                <label>Mật khẩu:</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu">
                <?php if (isset($errors['password'])) : ?>
                    <p class="error"><?= htmlspecialchars($errors['password']); ?></p>
                <?php endif; ?>

                <label>Xác nhận mật khẩu:</label>
                <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu">
                <?php if (isset($errors['confirm_password'])) : ?>
                    <p class="error"><?= htmlspecialchars($errors['confirm_password']); ?></p>
                <?php endif; ?>

                <button type="submit" class="auth-btn">Đăng ký</button>
            </form>
            <p class="auth-switch">Đã có tài khoản? <a href="index.php?action=login">Đăng nhập ngay</a></p>
        </div>
    </div>
</div>

<style>
    .error {
        color: #ff0000;
        font-size: 14px;
        margin: 5px 0;
        padding: 5px;
        background-color: #ffe6e6;
        border: 1px solid #ff0000;
        border-radius: 4px;
    }
</style>