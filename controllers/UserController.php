<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        session_start();
    }

    // Xử lý đăng ký (cập nhật hỗ trợ upload avatar)
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];

            // Kiểm tra từng trường
            if (empty($username)) {
                $errors['username'] = "Vui lòng nhập Tên hiển thị";
            }
            if (empty($email)) {
                $errors['email'] = "Vui lòng nhập email với định dạng usermail@gmail.com";
            } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
                $errors['email'] = "Vui lòng nhập email với định dạng usermail@gmail.com";
            }
            if (empty($phone)) {
                $errors['phone'] = "Vui lòng nhập Số điện thoại";
            } else if (!preg_match('/^0\d{9}$/', $phone)) {
                // Kiểm tra định dạng số điện thoại: bắt đầu bằng số 0 và có tổng cộng 10 số
                $errors['phone'] = "Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0";
            }
            if (empty($password)) {
                $errors['password'] = "Vui lòng nhập Mật khẩu";
            }
            if (empty($confirmPassword)) {
                $errors['confirm_password'] = "Vui lòng Xác nhận mật khẩu";
            }

            // Nếu có lỗi, hiển thị form với thông báo lỗi
            if (!empty($errors)) {
                $this->render('register', ['errors' => $errors]);
                return;
            }

            // Kiểm tra mật khẩu khớp
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = "Mật khẩu không khớp";
                $this->render('register', ['errors' => $errors]);
                return;
            }

            // Kiểm tra email và số điện thoại đã tồn tại
            $result = $this->userModel->register($username, $email, $phone, password_hash($password, PASSWORD_DEFAULT));

            if ($result === 'username_exists') {
                $errors['username'] = "Tên hiển thị đã được sử dụng. Vui lòng sử dụng tên hiển thị khác";
                $this->render('register', ['errors' => $errors]);
                return;
            } else if ($result === 'email_exists') {
                $errors['email'] = "Email này đã được sử dụng. Vui lòng sử dụng email khác.";
                $this->render('register', ['errors' => $errors]);
                return;
            } else if ($result === 'phone_exists') {
                $errors['phone'] = "Số điện thoại đã tồn tại trong cơ sở dữ liệu. Vui lòng sử dụng số điện thoại khác";
                $this->render('register', ['errors' => $errors]);
                return;
            } else if ($result === true) {
                $success = "Tài khoản đã đăng ký thành công!";
                $this->render('register', ['success' => $success]);
                return;
            } else {
                $errors['general'] = "Đăng ký thất bại. Vui lòng thử lại sau.";
                $this->render('register', ['errors' => $errors]);
                return;
            }
        } else {
            $this->render('register');
        }
    }

    // Xử lý đăng nhập
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if ($email && $password) {
                $user = $this->userModel->login($email, $password);
                if ($user) {
                    $_SESSION['user'] = $user;
                    header("Location: index.php?action=dashboard");
                    exit;
                } else {
                    $error = "Email hoặc mật khẩu không đúng";
                }
            } else {
                $error = "Vui lòng nhập email và mật khẩu.";
            }
            $this->render('login', ['error' => $error]);
        } else {
            $this->render('login');
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $this->render('dashboard', ['user' => $_SESSION['user']]);
    }

    // Tìm kiếm người dùng
    public function search()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $keyword = trim($_GET['q'] ?? '');
        $results = [];
        if ($keyword != '') {
            // Tìm kiếm người dùng bằng keyword
            $results = $this->userModel->searchUsers($keyword);
            // Lấy danh sách bạn bè của người dùng hiện tại
            $currentUserId = $_SESSION['user']['id'];
            $friendIds = $this->userModel->getFriendIds($currentUserId);
            // Gán flag is_friend cho từng kết quả
            foreach ($results as &$result) {
                $result['is_friend'] = in_array($result['id'], $friendIds) ? 1 : 0;
            }
        }
        $this->render('search', ['results' => $results, 'keyword' => $keyword]);
    }


    public function viewUser()
    {
        // Bắt buộc phải đăng nhập để xem thông tin chi tiết người dùng
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $user_id = $_GET['user_id'] ?? '';
        if (!$user_id) {
            header("Location: index.php?action=search");
            exit;
        }

        $user = $this->userModel->getUserById($user_id);
        if (!$user) {
            header("Location: index.php?action=search");
            exit;
        }

        // Lấy thông tin người dùng hiện tại (người xem)
        $currentUser = $_SESSION['user'];

        // Nếu người xem không phải là chủ hồ sơ, tôn trọng thiết lập ẩn số điện thoại và email
        if ((int)$currentUser['id'] !== (int)$user['id']) {
            if (isset($user['hide_phone']) && $user['hide_phone'] == 1) {
                $user['phone'] = null; // Hoặc thay thế bằng thông báo "Ẩn"
            }
            if (isset($user['hide_email']) && $user['hide_email'] == 1) {
                $user['contact_email'] = null; // Hoặc thay thế bằng "Ẩn"
            }
        }

        $this->render('view_user', ['user' => $user]);
    }


    public function withdrawRequest()
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            // (Nếu bạn thấy điều này xảy ra mặc dù người dùng đã đăng nhập, hãy kiểm tra session_start())
            header("Location: index.php?action=login");
            exit;
        }

        // Lấy request_id từ GET
        $request_id = $_GET['request_id'] ?? '';
        if (!$request_id) {
            header("Location: index.php?action=viewSentRequests"); // hoặc sentRequests
            exit;
        }

        // Thực hiện thu hồi lời mời (chỉ cho phép nếu trạng thái đang là 0)
        $result = $this->userModel->withdrawFriendRequest($request_id, $_SESSION['user']['id']);

        $message = $result ? "Đã thu hồi lời mời kết bạn." : "Thu hồi thất bại.";
        header("Location: index.php?action=viewSentRequests&msg=" . urlencode($message));
        exit;
    }

    // Thêm bạn
    public function addFriend()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $friend_id = $_GET['id'] ?? '';
        $user_id   = $_SESSION['user']['id'];
        if ($friend_id != '') {
            if ($this->userModel->addFriend($user_id, $friend_id)) {
                header("Location: index.php?action=dashboard");
                exit;
            } else {
                $error = "Bạn đã kết bạn với người này hoặc có lỗi xảy ra.";
                $this->render('dashboard', ['user' => $_SESSION['user'], 'error' => $error]);
            }
        }
    }

    // Gửi yêu cầu kết bạn
    public function sendFriendRequest()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $receiver_id = $_GET['receiver_id'] ?? '';
        if ($receiver_id) {
            require_once __DIR__ . '/../models/FriendRequest.php';
            $friendRequestModel = new FriendRequest();
            if ($friendRequestModel->sendRequest($_SESSION['user']['id'], $receiver_id)) {
                $message = "Yêu cầu kết bạn đã được gửi.";
            } else {
                $message = "Yêu cầu kết bạn đã tồn tại hoặc có lỗi xảy ra.";
            }
            header("Location: index.php?action=dashboard&msg=" . urlencode($message));
            exit;
        }
    }

    // Xem danh sách yêu cầu kết bạn nhận được
    public function viewFriendRequests()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        require_once __DIR__ . '/../models/FriendRequest.php';
        $friendRequestModel = new FriendRequest();
        $requests = $friendRequestModel->getPendingRequests($_SESSION['user']['id']);
        $this->render('friend_requests', ['requests' => $requests]);
    }

    // Phản hồi yêu cầu kết bạn (chấp nhận hoặc từ chối)
    public function respondFriendRequest()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        require_once __DIR__ . '/../models/FriendRequest.php';
        $friendRequestModel = new FriendRequest();
        $request_id = $_GET['request_id'] ?? '';
        $response = $_GET['response'] ?? ''; // accept hoặc reject
        if ($request_id && ($response === 'accept' || $response === 'reject')) {
            $status = ($response === 'accept') ? 1 : 2;
            $friendRequestModel->respondRequest($request_id, $status);
            header("Location: index.php?action=viewFriendRequests");
            exit;
        }
    }

    // Xem danh sách yêu cầu đã gửi (để biết phản hồi)
    public function viewSentRequests()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }
        require_once __DIR__ . '/../models/FriendRequest.php';
        $friendRequestModel = new FriendRequest();
        $sentRequests = $friendRequestModel->getSentRequests($_SESSION['user']['id']);
        $this->render('sent_requests', ['sentRequests' => $sentRequests]);
    }


    public function unfriend()
    {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
            exit;
        }
        $friend_id = $_GET['friend_id'] ?? '';
        if ($friend_id) {
            $user_id = $_SESSION['user']['id'];
            $result = $this->userModel->unfriend($user_id, $friend_id);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy ID bạn bè.']);
        }
        exit;
    }

    // Thêm phương thức editProfile() vào class UserController

    public function editProfile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $user = $_SESSION['user'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $birth_date = $_POST['birth_date'] ?? '';
            $gender = $_POST['gender'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $contact_email = $_POST['contact_email'] ?? '';
            $location = $_POST['location'] ?? '';
            $hide_phone = isset($_POST['hide_phone']) ? 1 : 0;
            $hide_email = isset($_POST['hide_email']) ? 1 : 0;

            if (!$username) {
                $error = "Tên không được để trống.";
                $this->render('profile', ['user' => $user, 'error' => $error]);
                exit;
            }

            $passwordHash = $user['password'];
            if ($newPassword || $confirmPassword) {
                if ($newPassword !== $confirmPassword) {
                    $error = "Mật khẩu mới không khớp.";
                    $this->render('profile', ['user' => $user, 'error' => $error]);
                    exit;
                }
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            if ($this->userModel->updateProfile(
                $user['id'],
                $username,
                $passwordHash,
                $user['avatar'], // Giữ nguyên avatar cũ
                $birth_date,
                $gender,
                $phone,
                $contact_email,
                $location,
                $hide_phone,
                $hide_email
            )) {
                $updatedUser = $this->userModel->getUserById($user['id']);
                $_SESSION['user'] = $updatedUser;
                $message = "Cập nhật hồ sơ thành công.";
                $this->render('profile', ['user' => $updatedUser, 'message' => $message]);
                exit;
            } else {
                $error = "Cập nhật hồ sơ thất bại.";
                $this->render('profile', ['user' => $user, 'error' => $error]);
                exit;
            }
        } else {
            $this->render('profile', ['user' => $user]);
        }
    }
}
