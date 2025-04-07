<?php
require_once 'Database.php';

class FileUpload {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Lưu thông tin file upload
    public function saveFile($user_id, $file_name, $file_path) {
        $stmt = $this->conn->prepare("INSERT INTO files(user_id, file_name, file_path, uploaded_at) VALUES(?, ?, ?, NOW())");
        return $stmt->execute([$user_id, $file_name, $file_path]);
    }
    
    // Lấy danh sách file của người dùng
    public function getUserFiles($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM files WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
