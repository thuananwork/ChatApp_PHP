<?php
// Front Controller: định tuyến các request
spl_autoload_register(function($className) {
    if(file_exists(__DIR__ . '/../controllers/' . $className . '.php')) {
        require_once __DIR__ . '/../controllers/' . $className . '.php';
    } elseif(file_exists(__DIR__ . '/../models/' . $className . '.php')) {
        require_once __DIR__ . '/../models/' . $className . '.php';
    }
});

$action = $_GET['action'] ?? 'login';

// ...
switch($action) {
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    case 'dashboard':
        $controller = new UserController();
        $controller->dashboard();
        break;
    case 'search':
        $controller = new UserController();
        $controller->search();
        break;
    case 'sendFriendRequest':
        $controller = new UserController();
        $controller->sendFriendRequest();
        break;
    case 'viewFriendRequests':
        $controller = new UserController();
        $controller->viewFriendRequests();
        break;
    case 'respondFriendRequest':
        $controller = new UserController();
        $controller->respondFriendRequest();
        break;
    case 'viewSentRequests':
        $controller = new UserController();
        $controller->viewSentRequests();
        break;
    case 'chat':
        $controller = new ChatController();
        $controller->chat();
        break;
    case 'fetchChat':
        $controller = new ChatController();
        $controller->fetchChat();
        break;
    case 'sendMessage':
        $controller = new ChatController();
        $controller->sendMessage();
        break;
    case 'deleteMessage':
        $controller = new ChatController();
        $controller->deleteMessage();
        break;
    case 'editMessage':
        $controller = new ChatController();
        $controller->editMessage();
        break;
    case 'editProfile':
        $controller = new UserController();
        $controller->editProfile();
        break;
    case 'viewUser':
        $controller = new UserController();
        $controller->viewUser();
        break;
    case 'withdrawRequest':
    case 'withdrawFriendRequest':
        $controller = new UserController();
        $controller->withdrawRequest();
        break;
    case 'unfriend':
        $controller = new UserController();
        $controller->unfriend();
        break;
    default:
        header("Location: index.php?action=login");
        break;
}
?>
