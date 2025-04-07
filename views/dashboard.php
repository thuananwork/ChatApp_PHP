<?php
// dashboard.php
//session_start();
require_once __DIR__ . '/../models/User.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}

// Lấy thông tin người dùng và danh sách bạn bè
$user = $_SESSION['user'];
$userModel = new User();
$friends = $userModel->getFriends($user['id']);
$msg = $_GET['msg'] ?? ''; // Thông báo (nếu có)
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- CSS tùy chỉnh (nếu cần) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Tiêu đề -->
        <div class="dashboard-header">
            <h2><i class="fas fa-home"></i> Chào, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        </div>

        <!-- Hiển thị thông báo (nếu có) -->
        <?php if ($msg): ?>
            <div class="alert alert-info" role="alert">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <!-- Layout chính -->
        <div class="dashboard-layout row">
            <!-- Sidebar -->
            <aside class="sidebar col-md-4">
                <!-- Danh sách bạn bè -->
                <div class="sidebar-section friend-section">
                    <h3><i class="fas fa-users"></i> Bạn bè</h3>
                    <?php if (count($friends) > 0): ?>
                        <ul class="friend-list list-unstyled">
                            <?php foreach ($friends as $friend): 
                                // Xử lý avatar
                                if (!empty($friend['avatar'])) {
                                    if (strpos($friend['avatar'], 'uploads/avatars/') === 0 || strpos($friend['avatar'], 'assets/img/') === 0) {
                                        $avatarUrl = htmlspecialchars($friend['avatar']);
                                    } else {
                                        $avatarUrl = 'uploads/avatars/' . htmlspecialchars($friend['avatar']);
                                    }
                                } else {
                                    $avatarUrl = 'assets/img/default-avatar.png';
                                }
                            ?>
                            <li class="d-flex align-items-center">
                                <a href="index.php?action=chat&to_id=<?= $friend['id']; ?>" class="d-flex align-items-center text-decoration-none mr-2">
                                    <img src="<?= $avatarUrl; ?>" alt="Avatar" class="avatar">
                                    <span class="ml-2"><?= htmlspecialchars($friend['username']); ?></span>
                                </a>
                                <button class="unfriend-btn btn btn-sm btn-outline-danger" 
                                        data-friend-id="<?= $friend['id']; ?>" 
                                        data-friend-name="<?= htmlspecialchars($friend['username']); ?>">
                                    <i class="fas fa-user-times"></i>
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Chưa có bạn bè.</p>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Nội dung chính -->
            <section class="content col-md-8">
    <div class="sidebar-section">
        <h3><i class="fas fa-info-circle"></i> Hãy chiêm ngưỡng vẽ đẹp của Van Gogh</h3>
    </div>
    <div class="box">
        <span style="--i:1;"><img src="../images/a1.jpg" alt=""></span>
        <span style="--i:2;"><img src="../images/a2.jpg" alt=""></span>
        <span style="--i:3;"><img src="../images/a3.jpg" alt=""></span>
        <span style="--i:4;"><img src="../images/a4.jpg" alt=""></span>
        <span style="--i:5;"><img src="../images/a5.jpg" alt=""></span>
        <span style="--i:6;"><img src="../images/a6.jpg" alt=""></span>
        <span style="--i:7;"><img src="../images/a7.jpg" alt=""></span>
        <span style="--i:8;"><img src="../images/a8.jpg" alt=""></span>
    </div>
    <script type="text/javascript">
        let box = document.querySelector('.box');
        window.onmousemove = function(e){
            let x = e.clientX/3;
            box.style.transform = "perspective(1000px) rotateY("+x+"deg)";
        }
    </script>
</section>

        </div>
    </div>

    <!-- Modal xác nhận hủy kết bạn -->
    <div class="modal fade" id="unfriendModal" tabindex="-1" role="dialog" aria-labelledby="unfriendModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unfriendModalLabel">Xác nhận hủy kết bạn</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn hủy kết bạn với <span id="friendName"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmUnfriend">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Thư viện JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript xử lý hủy kết bạn -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const unfriendButtons = document.querySelectorAll('.unfriend-btn');
        const modal = document.getElementById('unfriendModal');
        const friendNameSpan = document.getElementById('friendName');
        const confirmUnfriendBtn = document.getElementById('confirmUnfriend');
        let currentFriendId;

        // Xử lý khi nhấp vào nút "Hủy kết bạn"
        unfriendButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentFriendId = this.getAttribute('data-friend-id');
                const friendName = this.getAttribute('data-friend-name');
                friendNameSpan.textContent = friendName;
                $(modal).modal('show');
            });
        });

        // Xử lý khi xác nhận hủy kết bạn trong modal
        confirmUnfriendBtn.addEventListener('click', function() {
            fetch('index.php?action=unfriend&friend_id=' + currentFriendId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const friendItem = document.querySelector(`.unfriend-btn[data-friend-id="${currentFriendId}"]`).parentElement;
                    friendItem.remove();
                    $(modal).modal('hide');
                } else {
                    alert('Hủy kết bạn thất bại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi.');
            });
        });
    });
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>