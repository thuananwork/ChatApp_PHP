<div class="profile-page">
    <div class="profile-container">
        <div class="profile-card">
            <!-- Hiển thị avatar ở đầu trang với kích thước cố định và hình tròn -->
            <div class="profile-avatar-container" style="text-align:center; margin-bottom:20px;">
                <?php if(!empty($user['avatar'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="profile-avatar-large" style="width:150px; height:150px; border-radius:50%; object-fit:cover;">
                <?php else: ?>
                    <img src="assets/img/default-avatar.png" alt="Avatar" class="profile-avatar-large" style="width:150px; height:150px; border-radius:50%; object-fit:cover;">
                <?php endif; ?>
            </div>
            <h2>Chỉnh sửa Hồ sơ</h2>
            <?php if(isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if(isset($message)): ?>
                <p class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form method="post" action="index.php?action=editProfile" enctype="multipart/form-data">
                <div class="profile-field">
                    <label for="username">Tên:</label>
                    <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="profile-field">
                    <label for="new_password">Mật khẩu mới:</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Nhập mật khẩu mới (nếu muốn thay đổi)">
                </div>
                <div class="profile-field">
                    <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Xác nhận mật khẩu mới">
                </div>
                <!-- Cho phép cập nhật Avatar nếu muốn -->
                <div class="profile-field">
                    <label for="avatar">Thay đổi Avatar:</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*">
                </div>
                <!-- Các trường bổ sung -->
                <div class="profile-field">
                    <label for="birth_date">Ngày sinh:</label>
                    <input type="date" name="birth_date" id="birth_date" value="<?= isset($user['birth_date']) ? htmlspecialchars($user['birth_date']) : ''; ?>">
                </div>
                <div class="profile-field">
                    <label for="gender">Giới tính:</label>
                    <select name="gender" id="gender">
                        <option value="">Chọn giới tính</option>
                        <option value="male" <?= (isset($user['gender']) && $user['gender'] === 'male') ? 'selected' : ''; ?>>Nam</option>
                        <option value="female" <?= (isset($user['gender']) && $user['gender'] === 'female') ? 'selected' : ''; ?>>Nữ</option>
                        <option value="other" <?= (isset($user['gender']) && $user['gender'] === 'other') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                <div class="profile-field">
                    <label for="phone">Số điện thoại:</label>
                    <!-- Dữ liệu không cho phép chỉnh sửa -->
                    <input type="tel" name="phone_display" id="phone" value="<?= isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" disabled>
                    <!-- Giá trị thực gửi qua hidden field -->
                    <input type="hidden" name="phone" value="<?= isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>">
                    <label>
                        <input type="checkbox" name="hide_phone" value="1" <?= (isset($user['hide_phone']) && $user['hide_phone'] == 1) ? 'checked' : ''; ?>>
                        Ẩn số điện thoại khỏi công khai
                    </label>
                </div>
                <div class="profile-field">
                    <label for="contact_email">Email liên hệ:</label>
                    <!-- Hiển thị email nhưng không cho phép chỉnh sửa -->
                    <input type="email" name="contact_email_display" id="contact_email" value="<?= isset($user['contact_email']) ? htmlspecialchars($user['contact_email']) : ''; ?>" disabled>
                    <!-- Giá trị thực gửi qua hidden field -->
                    <input type="hidden" name="contact_email" value="<?= isset($user['contact_email']) ? htmlspecialchars($user['contact_email']) : ''; ?>">
                    <label>
                        <input type="checkbox" name="hide_email" value="1" <?= (isset($user['hide_email']) && $user['hide_email'] == 1) ? 'checked' : ''; ?>>
                        Ẩn email khỏi công khai
                    </label>
                </div>
                <div class="profile-field">
                    <label for="location">Nơi sống:</label>
                    <input type="text" name="location" id="location" value="<?= isset($user['location']) ? htmlspecialchars($user['location']) : ''; ?>">
                </div>
                <button type="submit" class="auth-btn">Cập nhật Hồ sơ</button>
            </form>
        </div>
    </div>
</div>