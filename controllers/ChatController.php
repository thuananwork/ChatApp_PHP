<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Chat.php';

class ChatController extends BaseController {
    private $chatModel;
    
    public function __construct() {
        $this->chatModel = new Chat();
        session_start();
    }
    
    // Gửi tin nhắn (cá nhân hoặc nhóm)
    public function sendMessage() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $from_id = $_SESSION['user']['id'];
            $to_id   = $_POST['to_id'] ?? null;
            $message = trim($_POST['message'] ?? '');
            $group_id = $_POST['group_id'] ?? null;
            if ($message && ($to_id || $group_id)) {
                $this->chatModel->sendMessage($from_id, $to_id, $message, $group_id);
            }
            if ($to_id) {
                header("Location: index.php?action=chat&to_id=$to_id");
            } else if ($group_id) {
                header("Location: index.php?action=groupChat&group_id=$group_id");
            }
            exit;
        }
    }
    
    // Hiển thị khung chat cho tin nhắn cá nhân
    public function chat() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $to_id   = $_GET['to_id'] ?? '';
        if ($to_id == '') {
            header("Location: index.php?action=dashboard");
            exit;
        }
        
        // Lấy lịch sử chat giữa 2 người
        $history = $this->chatModel->getChatHistory($user_id, $to_id);
        
        // Lấy thông tin người dùng được chat (để hiển thị tên)
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $toUserInfo = $userModel->getUserById($to_id);
        $to_user = $toUserInfo ? $toUserInfo['username'] : 'Người dùng';
        
        // Truyền biến $to_user sang view
        $this->render('chat', [
            'history' => $history, 
            'to_id'   => $to_id, 
            'to_user' => $to_user
        ]);
    }
    
    public function fetchChat() {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    
        if (!isset($_SESSION['user'])) {
            echo json_encode([]);
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $other_id = $_GET['other_id'] ?? null;
        if (!$other_id) {
            echo json_encode(['error' => 'Thiếu tham số other_id']);
            exit;
        }
        $history = $this->chatModel->getChatHistory($user_id, $other_id);
        header('Content-Type: application/json');
        echo json_encode($history);
        exit;
    }

    // Xóa tin nhắn (chỉ cho phép người gửi xóa tin của mình)
    public function deleteMessage() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $message_id = $_POST['message_id'] ?? null;
            $user_id = $_SESSION['user']['id'];
            if ($message_id) {
                $this->chatModel->deleteMessage($message_id, $user_id);
            }
            $to_id = $_POST['to_id'] ?? '';
            header("Location: index.php?action=chat&to_id=$to_id");
            exit;
        }
    }

    // Sửa tin nhắn (chỉ cho phép người gửi sửa tin của mình)
    public function editMessage() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $message_id = $_POST['message_id'] ?? null;
            $newMessage = trim($_POST['message'] ?? '');
            $user_id = $_SESSION['user']['id'];
            if ($message_id && $newMessage) {
                $this->chatModel->editMessage($message_id, $user_id, $newMessage);
            }
            $to_id = $_POST['to_id'] ?? '';
            header("Location: index.php?action=chat&to_id=$to_id");
            exit;
        }
    }
}
?>
