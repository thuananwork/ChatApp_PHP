<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h2>Đăng nhập</h2>
            <?php if(isset($_GET['msg'])): ?>
                <p class="success"><?= htmlspecialchars($_GET['msg']); ?></p>
            <?php endif; ?>
            <?php if(isset($error)) : ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="post" action="index.php?action=login">
                <label>Email:</label>
                <input type="email" name="email" placeholder="Nhập email" required>
                <label>Mật khẩu:</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                <button type="submit" class="auth-btn">Đăng nhập</button>
            </form>
            <p class="auth-switch">Chưa có tài khoản? <a href="index.php?action=register">Đăng ký ngay</a></p>
        </div>
    </div>
</div>