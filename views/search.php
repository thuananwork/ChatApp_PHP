<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm người dùng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Tìm kiếm người dùng</h2>
        <form method="get" action="index.php" class="search-form">
            <input type="hidden" name="action" value="search">
            <input type="text" name="q" placeholder="Nhập username" value="<?= htmlspecialchars($keyword); ?>" required>
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
        <?php if (!empty($results)): ?>
            <ul class="search-results">
                <?php foreach ($results as $result): ?>
                    <li>
                        <div class="user-info">
                            <?php if (!empty($result['avatar'])): ?>
                                <img src="<?= htmlspecialchars($result['avatar']); ?>" alt="Avatar">
                            <?php else: ?>
                                <img src="assets/img/default-avatar.png" alt="Avatar">
                            <?php endif; ?>
                            <?php if ($result['id'] == $_SESSION['user']['id']): ?>
                                <!-- Nếu là chính người dùng thì chuyển hướng đến trang profile -->
                                <a class="view-user" href="index.php?action=editProfile">
                                    <?= htmlspecialchars($result['username']); ?>
                                </a>
                            <?php else: ?>
                                <a class="view-user" href="index.php?action=viewUser&user_id=<?= htmlspecialchars($result['id']); ?>">
                                    <?= htmlspecialchars($result['username']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="actions">
                            <?php if ($result['id'] == $_SESSION['user']['id']): ?>
                                <!-- Không hiển thị nút kết bạn đối với chính người dùng -->
                                <span class="self"></span>
                            <?php elseif (isset($result['is_friend']) && ((int)$result['is_friend'] === 1)): ?>
                                <span class="friend">Đã là bạn bè</span>
                            <?php else: ?>
                                <a class="add-friend" href="index.php?action=sendFriendRequest&receiver_id=<?= htmlspecialchars($result['id']); ?>">
                                    Kết bạn
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php if ($keyword != ''): ?>
                <p class="no-results">Không tìm thấy kết quả nào.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
