<?php
class FriendRequest {
    private $conn;
    
    public function __construct() {
        $dsn = "mysql:host=localhost;dbname=chat_app;charset=utf8mb4";
        $username = "root";
        $password = "";
        try {
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
        }
    }
    
    // Gửi yêu cầu kết bạn
    public function sendRequest($sender_id, $receiver_id) {
        // Tránh gửi trùng yêu cầu
        $stmt = $this->conn->prepare("SELECT id FROM friend_requests WHERE sender_id = ? AND receiver_id = ? AND status = 0");
        $stmt->execute([$sender_id, $receiver_id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
        $stmt = $this->conn->prepare("
            INSERT INTO friend_requests(sender_id, receiver_id, status, created_at)
            VALUES (?, ?, 0, NOW())
        ");
        return $stmt->execute([$sender_id, $receiver_id]);
    }
    
    // Lấy danh sách yêu cầu kết bạn đang chờ xử lý (status = 0)
    public function getPendingRequests($user_id) {
        $stmt = $this->conn->prepare("
            SELECT fr.id, u.username, fr.created_at
            FROM friend_requests fr
            JOIN users u ON fr.sender_id = u.id
            WHERE fr.receiver_id = ? AND fr.status = 0
            ORDER BY fr.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy danh sách yêu cầu đã gửi
    public function getSentRequests($user_id) {
        $stmt = $this->conn->prepare("
            SELECT fr.id, u.username, fr.created_at, fr.status
            FROM friend_requests fr
            JOIN users u ON fr.receiver_id = u.id
            WHERE fr.sender_id = ?
            ORDER BY fr.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Phản hồi yêu cầu (accept/reject) – status: 1 = accepted, 2 = rejected
    public function respondRequest($request_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE friend_requests SET status = ? WHERE id = ?
        ");
        return $stmt->execute([$status, $request_id]);
    }
}
?>