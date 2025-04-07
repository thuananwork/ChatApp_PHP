<?php
require_once 'Database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Đăng ký
    public function register($username, $email, $phone, $passwordHash, $avatarPath = null)
    {
        // Kiểm tra email đã tồn tại chưa
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            return false; // Email đã tồn tại
        }

        $stmt = $this->conn->prepare("
            INSERT INTO users(username, email, contact_email, phone, password, avatar)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$username, $email, $email, $phone, $passwordHash, $avatarPath]);
    }

    // Đăng nhập
    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Tìm kiếm người dùng
    public function searchUsers($keyword)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email FROM users WHERE username LIKE ?");
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function withdrawFriendRequest($request_id, $user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM friend_requests WHERE id = ? AND sender_id = ? AND status = 0");
        return $stmt->execute([$request_id, $user_id]);
    }

    // Thêm bạn (lưu vào bảng friends)
    public function addFriend($user_id, $friend_id)
    {
        // Kiểm tra nếu đã kết bạn để tránh trùng lặp
        $stmt = $this->conn->prepare("SELECT * FROM friends WHERE user_id = ? AND friend_id = ?");
        $stmt->execute([$user_id, $friend_id]);
        if ($stmt->rowCount() > 0) {
            return false;
        }
        $stmt = $this->conn->prepare("INSERT INTO friends(user_id, friend_id) VALUES(?, ?)");
        return $stmt->execute([$user_id, $friend_id]);
    }

    // Lấy danh sách bạn bè
    public function getFriends($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT u.id, u.username, u.avatar 
            FROM friend_requests fr
            JOIN users u 
                ON ( (fr.sender_id = u.id AND fr.receiver_id = ?) 
                  OR (fr.receiver_id = u.id AND fr.sender_id = ?) )
            WHERE fr.status = 1
        ");
        $stmt->execute([$user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy danh sách yêu cầu kết bạn chưa xử lý (status = 0)
    public function getFriendRequests($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT fr.id, u.username 
            FROM friend_requests fr
            JOIN users u ON fr.sender_id = u.id
            WHERE fr.receiver_id = ? AND fr.status = 0
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getFriendIds($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT CASE
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END AS friend_id
            FROM friend_requests
            WHERE status = 1 AND (sender_id = ? OR receiver_id = ?)
        ");
        $stmt->execute([$user_id, $user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function unfriend($user_id, $friend_id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM friend_requests
            WHERE (sender_id = ? AND receiver_id = ? AND status = 1)
               OR (sender_id = ? AND receiver_id = ? AND status = 1)
        ");
        return $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
    }

    public function updateProfile($userId, $username, $passwordHash, $avatarPath, $birth_date, $gender, $phone, $contact_email, $location, $hide_phone, $hide_email)
    {
        $stmt = $this->conn->prepare("
            UPDATE users 
            SET username = ?, password = ?, avatar = ?,
                birth_date = ?, gender = ?, phone = ?, contact_email = ?, location = ?,
                hide_phone = ?, hide_email = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $username,
            $passwordHash,
            $avatarPath,
            $birth_date,
            $gender,
            $phone,
            $contact_email,
            $location,
            $hide_phone,
            $hide_email,
            $userId
        ]);
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
