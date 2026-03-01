<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Produto.php';

class AdminController extends Controller {

    public function produtos() {

        if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] != 'admin') {
            header("Location: /espetinhov5/public/dashboard");
            exit;
        }

        $grupoModel = new Grupo();
        $produtoModel = new Produto();

        $grupos = $grupoModel->listarAtivos();
        $produtos = $produtoModel->listarTodos();

        $this->view('admin/produtos', [
            'grupos' => $grupos,
            'produtos' => $produtos
        ]);
    }

    public function salvarProduto() {

        if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] != 'admin') {
            exit;
        }

        require_once __DIR__ . '/../../config/database.php';

        $db = Database::getInstance()->getConnection();

        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        $grupo_id = $_POST['grupo_id'];

        $imagemNome = null;

        if (!empty($_FILES['imagem']['name'])) {

            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $imagemNome = uniqid() . "." . $extensao;

            move_uploaded_file(
                $_FILES['imagem']['tmp_name'],
                __DIR__ . '/../../public/uploads/' . $imagemNome
            );
        }

        $sql = "INSERT INTO produtos (nome, preco, grupo_id, imagem, ativo)
                VALUES (:nome, :preco, :grupo_id, :imagem, TRUE)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':preco', $preco);
        $stmt->bindValue(':grupo_id', $grupo_id);
        $stmt->bindValue(':imagem', $imagemNome);
        $stmt->execute();

        header("Location: /espetinhov5/public/admin/produtos");
        exit;
    }
}