<?php
require_once 'Database.php';

class Chat {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Gửi tin nhắn cá nhân hoặc nhóm (nếu truyền group_id)
    public function sendMessage($from_id, $to_id, $message, $group_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO messages(from_id, to_id, group_id, message, sent_at) VALUES(?, ?, ?, ?, NOW())");
        return $stmt->execute([$from_id, $to_id, $group_id, $message]);
    }

    // Lấy lịch sử chat giữa 2 người
    public function getChatHistory($user_id, $other_id) {
        $stmt = $this->conn->prepare("SELECT * FROM messages WHERE (from_id = ? AND to_id = ?) OR (from_id = ? AND to_id = ?) ORDER BY sent_at ASC");
        $stmt->execute([$user_id, $other_id, $other_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa tin nhắn (chỉ người gửi được xóa)
    public function deleteMessage($message_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM messages WHERE id = ? AND from_id = ?");
        return $stmt->execute([$message_id, $user_id]);
    }

    // Sửa tin nhắn (chỉ người gửi được sửa)
    public function editMessage($message_id, $user_id, $newMessage) {
        $stmt = $this->conn->prepare("UPDATE messages SET message = ? WHERE id = ? AND from_id = ?");
        return $stmt->execute([$newMessage, $message_id, $user_id]);
    }
}
?>
