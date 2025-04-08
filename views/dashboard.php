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
                <div id="chat-window" class="chat-window d-none">
                    <div class="chat-header">
                        <img src="" alt="Avatar" class="chat-avatar" id="chat-user-avatar">
                        <span id="chat-user-name"></span>
                    </div>
                    <div class="chat-messages" id="chat-messages">
                        <!-- Messages will be loaded here -->
                    </div>
                    <div class="chat-input">
                        <form id="chat-form" class="d-flex">
                            <input type="text" id="message-input" class="form-control" placeholder="Nhập tin nhắn...">
                            <button type="submit" class="btn btn-primary ml-2">Gửi</button>
                        </form>
                    </div>
                </div>
                <div id="welcome-message" class="text-center mt-5">
                    <h4>Chọn một người bạn để bắt đầu trò chuyện</h4>
                </div>
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript xử lý chat -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatWindow = document.getElementById('chat-window');
            const welcomeMessage = document.getElementById('welcome-message');
            const chatMessages = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const chatUserName = document.getElementById('chat-user-name');
            const chatUserAvatar = document.getElementById('chat-user-avatar');
            let currentChatId = null;

            // Xử lý khi click vào bạn bè
            document.querySelectorAll('.friend-list a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const friendId = this.href.split('to_id=')[1];
                    const friendName = this.querySelector('span').textContent;
                    const friendAvatar = this.querySelector('img').src;

                    // Hiển thị chat window
                    welcomeMessage.classList.add('d-none');
                    chatWindow.classList.remove('d-none');
                    chatUserName.textContent = friendName;
                    chatUserAvatar.src = friendAvatar;
                    currentChatId = friendId;

                    // Load tin nhắn
                    loadMessages(friendId);
                });
            });

            // Hàm load tin nhắn
            function loadMessages(friendId) {
                fetch(`index.php?action=get_messages&friend_id=${friendId}`)
                    .then(response => response.json())
                    .then(data => {
                        chatMessages.innerHTML = '';
                        data.messages.forEach(message => {
                            const messageElement = document.createElement('div');
                            messageElement.className = `message ${message.from_id == <?= $user['id'] ?> ? 'sent' : 'received'}`;
                            messageElement.innerHTML = `
                                <div class="message-content">
                                    ${message.content}
                                </div>
                                <div class="message-time">
                                    ${message.created_at}
                                </div>
                            `;
                            chatMessages.appendChild(messageElement);
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
            }

            // Xử lý gửi tin nhắn
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!messageInput.value.trim() || !currentChatId) return;

                fetch('index.php?action=send_message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `to_id=${currentChatId}&message=${encodeURIComponent(messageInput.value)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageInput.value = '';
                            loadMessages(currentChatId);
                        }
                    });
            });

            // Auto refresh messages every 5 seconds
            setInterval(() => {
                if (currentChatId) {
                    loadMessages(currentChatId);
                }
            }, 5000);
        });
    </script>

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