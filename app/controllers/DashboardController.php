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

    public function statusMesas() {

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $sql = "SELECT id, status, inicio_atendimento
            FROM mesas
            ORDER BY id";

    $stmt = $db->query($sql);
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($mesas);
}





}