<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Mesa.php';

class DashboardController extends Controller {

    public function index() {

        if (!isset($_SESSION['usuario'])) {
            header("Location: /espetinhov5/public/");
            exit;
        }

        $mesaModel = new Mesa();
        $mesas = $mesaModel->listarTodas();

        $this->view('dashboard/index', ['mesas' => $mesas]);
    }
}