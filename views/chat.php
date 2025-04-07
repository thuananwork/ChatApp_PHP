<?php
// views/chat.php

// Hàm chuyển đổi URL thành thẻ liên kết
function linkify($text) {
    // Escape dữ liệu để đảm bảo an toàn trước khi chuyển đổi
    $text = htmlspecialchars($text);
    return preg_replace('~(https?://[^\s]+)~i', '<a href="$1" target="_blank">$1</a>', $text);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Sử dụng FontAwesome cho icon -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        /* Style cho header chat */
        .chat-header {
            background: #f1f1f1;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        /* Căn chỉnh vị trí của tin nhắn để dành chỗ cho các nút hành động bên trái */
        .chat-message {
            position: relative;
            padding-left: 50px;
            margin-bottom: 15px;
        }
        /* Các nút hành động được định vị bên trái tin nhắn */
        .message-actions {
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            flex-direction: column;
            gap: 5px;
        }
        /* Hiển thị các nút hành động khi hover vào tin nhắn */
        .chat-message:hover .message-actions {
            display: flex;
        }
        /* Style cho các nút hành động: icon và hover hiệu ứng */
        .message-actions button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #888;
            transition: color 0.3s;
        }
        .message-actions button:hover {
            color: #333;
        }
        /* Style cho form chỉnh sửa inline */
        .edit-form {
            display: none;
            margin-top: 5px;
        }
        .edit-form textarea {
            width: 100%;
            box-sizing: border-box;
        }
        /* Style cho form gửi tin nhắn */
        .chat-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        .chat-form textarea {
            flex: 1;
            padding: 10px;
            font-size: 14px;
            resize: none;
        }
        .chat-form button.send-btn {
            background: #0084ff;
            border: none;
            color: #fff;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }
        .chat-form button.send-btn:hover {
            background: #006bbd;
        }
    </style>
</head>
<body>
    <!-- Nút quay về dashboard -->
    <div class="chat-back" style="margin: 20px;">
        <a href="index.php?action=dashboard" class="back-arrow" style="text-decoration: none; color: var(--primary); font-size: 18px; display: inline-flex; align-items: center; transition: color 0.3s;">
            <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
        </a>
    </div>

    <!-- Header hiển thị tên người dùng chat (đảm bảo biến $to_user được truyền từ controller) -->
    <div class="chat-header">
        <?= isset($to_user) ? $to_user : 'Người dùng'; ?>
    </div>

    <div class="chat-container">
        <!-- Hiển thị tin nhắn -->
        <div class="chat-box" id="chatBox">
            <?php if(!empty($history)): ?>
                <?php foreach($history as $msg): ?>
                    <div class="chat-message <?= ($msg['from_id'] == $_SESSION['user']['id']) ? 'sent' : 'received'; ?>" data-msgid="<?= $msg['id']; ?>">
                        <div class="message-content">
                            <!-- Hiển thị nội dung tin nhắn -->
                            <p class="message-text"><?= linkify($msg['message']); ?></p>
                            <!-- Form chỉnh sửa tin nhắn (chỉ hiển thị cho người gửi) -->
                            <?php if($msg['from_id'] == $_SESSION['user']['id']): ?>
                                <form class="edit-form" id="editForm-<?= $msg['id']; ?>" action="index.php?action=editMessage" method="post">
                                    <input type="hidden" name="message_id" value="<?= $msg['id']; ?>">
                                    <input type="hidden" name="to_id" value="<?= $to_id; ?>">
                                    <textarea name="message"><?= htmlspecialchars($msg['message']); ?></textarea>
                                    <button type="submit">Lưu</button>
                                    <button type="button" onclick="toggleEditForm(<?= $msg['id']; ?>)">Hủy</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <span class="time"><?= $msg['sent_at']; ?></span>
                        <?php if($msg['from_id'] == $_SESSION['user']['id']): ?>
                            <div class="message-actions">
                                <!-- Nút xóa tin nhắn với icon thùng rác -->
                                <form method="post" action="index.php?action=deleteMessage" style="display:inline;">
                                    <input type="hidden" name="message_id" value="<?= $msg['id']; ?>">
                                    <input type="hidden" name="to_id" value="<?= $to_id; ?>">
                                    <button type="submit" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                                <!-- Nút sửa tin nhắn với icon bút chỉnh sửa -->
                                <button type="button" onclick="toggleEditForm(<?= $msg['id']; ?>)" title="Sửa"><i class="fas fa-edit"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p id="noMessages">Không có tin nhắn nào.</p>
            <?php endif; ?>
        </div>

        <!-- Form gửi tin nhắn -->
        <form id="sendMessageForm" method="post" action="index.php?action=sendMessage" class="chat-form">
            <input type="hidden" name="to_id" value="<?= isset($to_id) ? $to_id : ''; ?>">
            <textarea name="message" placeholder="Nhập tin nhắn của bạn..." required></textarea>
            <button type="submit" class="send-btn" title="Gửi">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <script>
        // Hàm chuyển đổi URL thành thẻ <a> trong JS cho các tin nhắn mới
        function linkify(text) {
            return text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        }

        // Endpoint lấy tin nhắn mới cho chat cá nhân
        var chatEndpoint = 'index.php?action=fetchChat&other_id=<?= $to_id ?>';

        // Lấy phần tử chatBox
        const chatBox = document.getElementById('chatBox');

        // Biến theo dõi id tin nhắn cuối cùng đã hiển thị
        let lastMessageId = 0;
        document.querySelectorAll('#chatBox .chat-message').forEach(function(msg) {
            let msgId = parseInt(msg.getAttribute('data-msgid'));
            if(msgId > lastMessageId) {
                lastMessageId = msgId;
            }
        });

        // Hàm cuộn khung chat xuống dưới
        function scrollChat() {
            chatBox.scroll({
                top: chatBox.scrollHeight,
                behavior: 'smooth'
            });
        }

        // Hàm thêm tin nhắn mới vào chatBox
        function appendMessages(messages) {
            // Nếu có thông báo "Không có tin nhắn nào", xóa nó
            const noMsgElem = document.getElementById('noMessages');
            if(noMsgElem) {
                noMsgElem.remove();
            }
            messages.forEach(function(message) {
                let div = document.createElement('div');
                let cls = (message.from_id == <?= $_SESSION['user']['id'] ?>) ? 'sent' : 'received';
                div.className = 'chat-message ' + cls;
                // Gán thuộc tính data-msgid để theo dõi tin nhắn mới
                div.setAttribute('data-msgid', message.id);
                
                let innerHTML = `<div class="message-content">`;
                innerHTML += `<p class="message-text">${linkify(message.message)}</p>`;
                if(message.from_id == <?= $_SESSION['user']['id'] ?>) {
                    innerHTML += `<form class="edit-form" id="editForm-${message.id}" action="index.php?action=editMessage" method="post">
                        <input type="hidden" name="message_id" value="${message.id}">
                        <input type="hidden" name="to_id" value="<?= $to_id; ?>">
                        <textarea name="message">${message.message}</textarea>
                        <button type="submit">Lưu</button>
                        <button type="button" onclick="toggleEditForm(${message.id})">Hủy</button>
                    </form>`;
                }
                innerHTML += `</div>`;
                innerHTML += `<span class="time">${message.sent_at}</span>`;
                if(message.from_id == <?= $_SESSION['user']['id'] ?>) {
                    innerHTML += `<div class="message-actions">
                        <form method="post" action="index.php?action=deleteMessage" style="display:inline;">
                            <input type="hidden" name="message_id" value="${message.id}">
                            <input type="hidden" name="to_id" value="<?= $to_id; ?>">
                            <button type="submit" title="Xóa"><i class="fas fa-trash"></i></button>
                        </form>
                        <button type="button" onclick="toggleEditForm(${message.id})" title="Sửa"><i class="fas fa-edit"></i></button>
                    </div>`;
                }
                div.innerHTML = innerHTML;
                chatBox.appendChild(div);
                lastMessageId = message.id;
            });
        }

        // Hàm lấy tin nhắn mới từ server
        function fetchChat() {
            fetch(chatEndpoint)
              .then(res => res.json())
              .then(data => {
                  // Lọc tin nhắn có id > lastMessageId
                  let newMessages = data.filter(message => parseInt(message.id) > lastMessageId);
                  if(newMessages.length) {
                      appendMessages(newMessages);
                      scrollChat();
                  }
              })
              .catch(err => console.error('Error fetching chat:', err));
        }
        setInterval(fetchChat, 3000);

        // Hàm bật/tắt form chỉnh sửa inline cho từng tin nhắn
        function toggleEditForm(msgId) {
            var form = document.getElementById('editForm-' + msgId);
            if(form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        // Gửi tin nhắn bằng AJAX
        document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(this.action, { method: 'POST', body: new FormData(this) })
              .then(() => {
                  this.querySelector('textarea[name="message"]').value = '';
                  fetchChat();
              })
              .catch(err => console.error('Error sending message:', err));
        });
    </script>
</body>
</html>
