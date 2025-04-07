<div class="user-info-page" style="max-width:600px; margin:30px auto; padding:0 15px;">
    <div class="user-info-card" style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 5px rgba(0,0,0,0.1);">
        <div class="user-info-avatar" style="text-align:center; margin-bottom:20px;">
            <?php if(!empty($user['avatar'])): ?>
                <img src="<?= htmlspecialchars($user['avatar']); ?>" alt="Avatar" style="width:150px; height:150px; border-radius:50%; object-fit:cover;">
            <?php else: ?>
                <img src="assets/img/default-avatar.png" alt="Avatar" style="width:150px; height:150px; border-radius:50%; object-fit:cover;">
            <?php endif; ?>
        </div>
        <h2 style="text-align:center;"><?= htmlspecialchars($user['username']); ?></h2>
        <p><strong>Ngày sinh:</strong> <?= isset($user['birth_date']) && $user['birth_date'] ? htmlspecialchars($user['birth_date']) : 'Chưa cập nhật'; ?></p>
        <p><strong>Giới tính:</strong> <?= isset($user['gender']) && $user['gender'] ? htmlspecialchars($user['gender']) : 'Chưa cập nhật'; ?></p>
        <?php if(isset($user['phone']) && !$user['hide_phone']): ?>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($user['phone']); ?></p>
        <?php else: ?>
            <p><strong>Số điện thoại:</strong> Ẩn</p>
        <?php endif; ?>
        <?php if(isset($user['contact_email']) && !$user['hide_email']): ?>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['contact_email']); ?></p>
        <?php else: ?>
            <p><strong>Email:</strong> Ẩn</p>
        <?php endif; ?>
        <p><strong>Nơi sống:</strong> <?= isset($user['location']) && $user['location'] ? htmlspecialchars($user['location']) : 'Chưa cập nhật'; ?></p>
        <div style="text-align:center; margin-top:20px;">
            <a href="javascript:history.back()" style="background: var(--secondary); color:#fff; padding:10px 15px; text-decoration:none; border-radius:4px;">Quay lại</a>
        </div>
    </div>
</div>