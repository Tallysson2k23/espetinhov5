<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController extends Controller {

    public function login() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $usuario = $_POST['usuario'];
            $senha = $_POST['senha'];

            $usuarioModel = new Usuario();
            $user = $usuarioModel->autenticar($usuario, $senha);

            if ($user) {

                $_SESSION['usuario'] = $user['nome'];
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nivel'] = $user['nivel'];

                header("Location: /espetinhov5/public/dashboard");
                exit;

            } else {
                $erro = "Usuário ou senha inválidos";
                $this->view('auth/login', ['erro' => $erro]);
                return;
            }
        }

        $this->view('auth/login');
    }

    public function logout() {
        session_destroy();
        header("Location: /espetinhov5/public/");
        exit;
    }

    public function index() {
        $this->login();
    }
}