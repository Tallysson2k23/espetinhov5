<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../models/Atendimento.php';

class PedidoController extends Controller {

    public function abrir($mesa_id) {

        if (!isset($_SESSION['usuario'])) {
            header("Location: /espetinhov5/public/");
            exit;
        }

        $mesaModel = new Mesa();
        $atendimentoModel = new Atendimento();

        // Verifica se já existe atendimento aberto
        $atendimento = $atendimentoModel->buscarAbertoPorMesa($mesa_id);

if (!$atendimento) {

    $atendimento = $atendimentoModel->criarAtendimento($mesa_id);

    // NÃO muda status aqui
}

        header("Location: /espetinhov5/public/pedido/visualizar/" . $atendimento['id']);
        exit;
    }


public function visualizar($atendimento_id) {

    if (!isset($_SESSION['usuario'])) {
        header("Location: /espetinhov5/public/");
        exit;
    }

    require_once __DIR__ . '/../models/Grupo.php';
    require_once __DIR__ . '/../models/Pedido.php';

    $grupoModel = new Grupo();
    $pedidoModel = new Pedido();

    $grupos = $grupoModel->listarAtivos();
    $pedidos = $pedidoModel->listarPorAtendimento($atendimento_id);
    $totalAtendimento = $pedidoModel->calcularTotalAtendimento($atendimento_id);

    // Buscar itens de cada pedido
    foreach ($pedidos as &$pedido) {
        $pedido['itens'] = $pedidoModel->listarItens($pedido['id']);
    }

$this->view('pedido/index', [
    'atendimento_id' => $atendimento_id,
    'grupos' => $grupos,
    'pedidos' => $pedidos,
    'totalAtendimento' => $totalAtendimento
]);
}


public function apiProdutos($grupo_id) {

    require_once __DIR__ . '/../models/Produto.php';

    $produtoModel = new Produto();
    $produtos = $produtoModel->listarPorGrupo($grupo_id);

    header('Content-Type: application/json');
    echo json_encode($produtos);
}

public function salvar() {

    if (!isset($_SESSION['usuario'])) {
        http_response_code(403);
        exit;
    }

    require_once __DIR__ . '/../models/Pedido.php';
    require_once __DIR__ . '/../models/Mesa.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../services/ImpressoraService.php';
    require_once __DIR__ . '/../services/CupomService.php';

    $dados = json_decode(file_get_contents("php://input"), true);

    $atendimento_id = $dados['atendimento_id'];
    $itens = $dados['itens'];

    $pedidoModel = new Pedido();
    $mesaModel = new Mesa();
    $db = Database::getInstance()->getConnection();

    // =========================
    // 1️⃣ Criar pedido
    // =========================
    $pedido_id = $pedidoModel->criarPedido(
        $atendimento_id,
        $_SESSION['usuario_id']
    );

    foreach ($itens as $item) {
        $pedidoModel->inserirItem(
            $pedido_id,
            $item['id'],
            $item['quantidade'],
            $item['preco']
        );
    }

    // =========================
    // 2️⃣ Buscar mesa
    // =========================
    $sqlMesa = "SELECT m.id, m.numero
                FROM mesas m
                JOIN atendimentos a ON a.mesa_id = m.id
                WHERE a.id = :id";

    $stmtMesa = $db->prepare($sqlMesa);
    $stmtMesa->bindValue(':id', $atendimento_id);
    $stmtMesa->execute();
    $mesa = $stmtMesa->fetch(PDO::FETCH_ASSOC);

    $mesa_id = $mesa['id'];
    $mesaNumero = $mesa['numero'];

    // Marcar ocupada
    $mesaModel->atualizarStatus($mesa_id, 'ocupada');

    // Iniciar timer se NULL
    $sqlInicio = "UPDATE mesas
                  SET inicio_atendimento = NOW()
                  WHERE id = :mesa_id
                  AND inicio_atendimento IS NULL";

    $stmtInicio = $db->prepare($sqlInicio);
    $stmtInicio->bindValue(':mesa_id', $mesa_id);
    $stmtInicio->execute();

    // =========================
    // 3️⃣ Preparar impressão
    // =========================
    $itensComGrupo = $pedidoModel->listarItensComGrupo($pedido_id);

    $gruposImpressao = [];

    foreach ($itensComGrupo as $item) {
        $gruposImpressao[$item['impressora']][] = $item;
    }

    // Buscar impressoras
    $stmtImp = $db->query("SELECT * FROM impressoras");
    $impressoras = $stmtImp->fetchAll(PDO::FETCH_ASSOC);

    $impressorasMap = [];
    foreach ($impressoras as $imp) {
        $impressorasMap[$imp['id']] = $imp;
    }

    // =========================
    // 4️⃣ Enviar para impressoras
    // =========================
    foreach ($gruposImpressao as $impressoraId => $itensGrupo) {

        if (!isset($impressorasMap[$impressoraId])) continue;

        $imp = $impressorasMap[$impressoraId];

        $conteudo = CupomService::gerar(
            $imp['nome'],
            $mesaNumero,
            $atendimento_id,
            $_SESSION['usuario'],
            $itensGrupo
        );

        // Timeout de 1 segundo para não travar
        ImpressoraService::imprimir(
            $imp['ip'],
            $imp['porta'],
            $conteudo
        );
    }

    echo json_encode([
    "status" => "ok",
    "mensagem" => "Pedido enviado para cozinha!"
]);
}
public function fechar() {

    if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] != 'admin') {
        http_response_code(403);
        exit;
    }

    require_once __DIR__ . '/../models/Pedido.php';
    require_once __DIR__ . '/../models/Mesa.php';
    require_once __DIR__ . '/../../config/database.php';

    $dados = json_decode(file_get_contents("php://input"), true);

    $atendimento_id = $dados['atendimento_id'];
    $forma_pagamento = $dados['forma_pagamento'];

    $pedidoModel = new Pedido();
    $mesaModel = new Mesa();

    $total = $pedidoModel->calcularTotalAtendimento($atendimento_id);

    $db = Database::getInstance()->getConnection();

    // Atualizar atendimento
    $sql = "UPDATE atendimentos
            SET forma_pagamento = :forma_pagamento,
                valor_total = :total,
                fechado = TRUE,
                aberto = FALSE,
                fechado_em = NOW()
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':forma_pagamento', $forma_pagamento);
    $stmt->bindValue(':total', $total);
    $stmt->bindValue(':id', $atendimento_id);
    $stmt->execute();

    // Buscar mesa
    $sqlMesa = "SELECT mesa_id FROM atendimentos WHERE id = :id";
    $stmtMesa = $db->prepare($sqlMesa);
    $stmtMesa->bindValue(':id', $atendimento_id);
    $stmtMesa->execute();
    $mesa_id = $stmtMesa->fetch(PDO::FETCH_ASSOC)['mesa_id'];

    // Liberar mesa
    $mesaModel->atualizarStatus($mesa_id, 'livre');

    // Zerar timer da mesa
$sqlReset = "UPDATE mesas
             SET inicio_atendimento = NULL
             WHERE id = :mesa_id";

$stmtReset = $db->prepare($sqlReset);
$stmtReset->bindValue(':mesa_id', $mesa_id);
$stmtReset->execute();

    echo json_encode([
        "status" => "ok",
        "total" => $total
    ]);
}



}