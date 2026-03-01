<?php

class Controller {

    protected function view($view, $data = []) {

        extract($data);

        require_once __DIR__ . '/../app/views/layouts/header.php';
        require_once __DIR__ . '/../app/views/' . $view . '.php';
        require_once __DIR__ . '/../app/views/layouts/footer.php';
    }

}