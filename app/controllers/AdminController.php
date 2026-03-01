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

public function toggleProduto($id) {

    if ($_SESSION['nivel'] != 'admin') exit;

    $db = Database::getInstance()->getConnection();

    $sql = "UPDATE produtos 
            SET ativo = NOT ativo
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/produtos");
    exit;
}


public function editarProduto($id) {

    if ($_SESSION['nivel'] != 'admin') exit;

    $db = Database::getInstance()->getConnection();

    $sql = "SELECT * FROM produtos WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/../models/Grupo.php';
    $grupoModel = new Grupo();
    $grupos = $grupoModel->listarAtivos();

    $this->view('admin/editar_produto', [
        'produto' => $produto,
        'grupos' => $grupos
    ]);
}


public function atualizarProduto($id) {

    if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] != 'admin') {
        exit;
    }

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $grupo_id = $_POST['grupo_id'];

    // Atualizar dados básicos
    $sql = "UPDATE produtos
            SET nome = :nome,
                preco = :preco,
                grupo_id = :grupo_id
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':preco', $preco);
    $stmt->bindValue(':grupo_id', $grupo_id);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/produtos");
    exit;
}

public function usuarios() {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $sql = "SELECT * FROM usuarios ORDER BY id DESC";
    $stmt = $db->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->view('admin/usuarios', [
        'usuarios' => $usuarios
    ]);
}

public function salvarUsuario() {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = md5($_POST['senha']);
    $nivel = $_POST['nivel'];

    $sql = "INSERT INTO usuarios (nome, usuario, senha, nivel, ativo)
            VALUES (:nome, :usuario, :senha, :nivel, TRUE)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->bindValue(':senha', $senha);
    $stmt->bindValue(':nivel', $nivel);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/usuarios");
    exit;
}

public function toggleUsuario($id) {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $sql = "UPDATE usuarios
            SET ativo = NOT ativo
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/usuarios");
    exit;
}


public function grupos() {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $sql = "SELECT * FROM grupos ORDER BY id DESC";
    $stmt = $db->query($sql);
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->view('admin/grupos', [
        'grupos' => $grupos
    ]);
}

public function salvarGrupo() {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $nome = $_POST['nome'];
    $impressora = $_POST['impressora'];

    $sql = "INSERT INTO grupos (nome, impressora, ativo)
            VALUES (:nome, :impressora, TRUE)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':impressora', $impressora);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/grupos");
    exit;
}

public function toggleGrupo($id) {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $sql = "UPDATE grupos
            SET ativo = NOT ativo
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/grupos");
    exit;
}


public function impressoras() {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("SELECT * FROM impressoras ORDER BY id");
    $impressoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->view('admin/impressoras', [
        'impressoras' => $impressoras
    ]);
}

public function salvarImpressora() {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $sql = "UPDATE impressoras
            SET nome = :nome,
                ip = :ip,
                porta = :porta
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':nome', $_POST['nome']);
    $stmt->bindValue(':ip', $_POST['ip']);
    $stmt->bindValue(':porta', $_POST['porta']);
    $stmt->bindValue(':id', $_POST['id']);
    $stmt->execute();

    header("Location: /espetinhov5/public/admin/impressoras");
    exit;
}


public function testarImpressora($id) {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../services/ImpressoraService.php';

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT * FROM impressoras WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    $imp = $stmt->fetch(PDO::FETCH_ASSOC);

    $ok = ImpressoraService::testarConexao($imp['ip'], $imp['porta']);

    echo json_encode([
        "status" => $ok ? "online" : "offline"
    ]);
}

public function imprimirTeste($id) {

    if ($_SESSION['nivel'] != 'admin') exit;

    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../services/ImpressoraService.php';
    require_once __DIR__ . '/../services/CupomService.php';

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT * FROM impressoras WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    $imp = $stmt->fetch(PDO::FETCH_ASSOC);

    $conteudo = CupomService::gerar(
        "TESTE IMPRESSORA",
        "00",
        "0000",
        $_SESSION['usuario'],
        [
            ["quantidade" => 1, "nome" => "IMPRESSAO OK"]
        ]
    );

    ImpressoraService::imprimir(
        $imp['ip'],
        $imp['porta'],
        $conteudo
    );

    header("Location: /espetinhov5/public/admin/impressoras");
    exit;
}






}