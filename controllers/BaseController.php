<?php
class BaseController {
    public function render($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../views/header.php';
        require_once __DIR__ . '/../views/' . $view . '.php';
        require_once __DIR__ . '/../views/footer.php';
    }
}
?>
