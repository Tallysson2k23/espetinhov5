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

    header('Content-Type: application/json');

    if (!isset($_SESSION['usuario'])) {
        http_response_code(403);
        echo json_encode(["status" => "erro", "msg" => "Usuário não autenticado"]);
        exit;
    }

    require_once __DIR__ . '/../models/Pedido.php';
    require_once __DIR__ . '/../models/Mesa.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../services/ImpressoraService.php';
    require_once __DIR__ . '/../services/CupomService.php';

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!$dados) {
        echo json_encode(["status" => "erro", "msg" => "Dados inválidos"]);
        exit;
    }

    $atendimento_id = $dados['atendimento_id'] ?? null;
    $itens = $dados['itens'] ?? [];

    if (!$atendimento_id || empty($itens)) {
        echo json_encode(["status" => "erro", "msg" => "Pedido vazio"]);
        exit;
    }

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

        $observacao = $item['observacao'] ?? null;

        $pedidoModel->inserirItem(
            $pedido_id,
            $item['id'],
            $item['quantidade'],
            $item['preco'],
            $observacao
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

    if (!$mesa) {
        echo json_encode(["status" => "erro", "msg" => "Mesa não encontrada"]);
        exit;
    }

    $mesa_id = $mesa['id'];
    $mesaNumero = $mesa['numero'];

    // Marcar mesa ocupada
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

        if (!isset($impressorasMap[$impressoraId])) {
            continue;
        }

        $imp = $impressorasMap[$impressoraId];

        $conteudo = CupomService::gerar(
            $imp['nome'],
            $mesaNumero,
            $atendimento_id,
            $_SESSION['usuario'],
            $itensGrupo
        );

        ImpressoraService::imprimir(
            $imp['ip'],
            $imp['porta'],
            $conteudo
        );
    }

    // =========================
    // 5️⃣ Resposta JSON limpa
    // =========================
    echo json_encode([
        "status" => "ok"
    ]);

    exit;
}


public function fechar() {

    if (!isset($_SESSION['usuario']) || $_SESSION['nivel'] != 'admin') {
        http_response_code(403);
        exit;
    }

    require_once __DIR__ . '/../models/Pedido.php';
    require_once __DIR__ . '/../models/Mesa.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../services/CupomService.php';
    require_once __DIR__ . '/../services/ImpressoraService.php';

    $dados = json_decode(file_get_contents("php://input"), true);

    $atendimento_id = $dados['atendimento_id'];
    $forma_pagamento = $dados['forma_pagamento'];

    $pedidoModel = new Pedido();
    $mesaModel = new Mesa();

    $total = $pedidoModel->calcularTotalAtendimento($atendimento_id);

    $db = Database::getInstance()->getConnection();

    // =========================
    // Atualizar atendimento
    // =========================
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

    // =========================
    // Buscar mesa vinculada
    // =========================
    $sqlMesa = "SELECT mesa_id FROM atendimentos WHERE id = :id";
    $stmtMesa = $db->prepare($sqlMesa);
    $stmtMesa->bindValue(':id', $atendimento_id);
    $stmtMesa->execute();
    $mesa_id = $stmtMesa->fetch(PDO::FETCH_ASSOC)['mesa_id'];

    // =========================
    // Liberar mesa
    // =========================
    $mesaModel->atualizarStatus($mesa_id, 'livre');

    // =========================
    // Zerar timer
    // =========================
    $sqlReset = "UPDATE mesas
                 SET inicio_atendimento = NULL
                 WHERE id = :mesa_id";

    $stmtReset = $db->prepare($sqlReset);
    $stmtReset->bindValue(':mesa_id', $mesa_id);
    $stmtReset->execute();

    // =========================
    // Buscar numero da mesa
    // =========================
    $sqlMesaNumero = "SELECT numero FROM mesas WHERE id = :id";
    $stmtMesaNumero = $db->prepare($sqlMesaNumero);
    $stmtMesaNumero->bindValue(':id', $mesa_id);
    $stmtMesaNumero->execute();
    $mesaNumero = $stmtMesaNumero->fetch(PDO::FETCH_ASSOC)['numero'];

    // =========================
    // Buscar todos os itens do atendimento
    // =========================
    $sqlItens = "SELECT 
                    ip.quantidade,
                    p.nome,
                    ip.preco_unitario
                 FROM itens_pedido ip
                 JOIN pedidos pe ON pe.id = ip.pedido_id
                 JOIN produtos p ON p.id = ip.produto_id
                 WHERE pe.atendimento_id = :id";

    $stmtItens = $db->prepare($sqlItens);
    $stmtItens->bindValue(':id', $atendimento_id);
    $stmtItens->execute();
    $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

    // =========================
    // Gerar cupom fechamento
    // =========================
    $conteudo = CupomService::gerarFechamento(
        $mesaNumero,
        $atendimento_id,
        $itens,
        $total,
        $forma_pagamento
    );

    // =========================
    // Buscar impressora 3 (Bebidas)
    // =========================
    $sqlImp = "SELECT * FROM impressoras WHERE id = 3 LIMIT 1";
    $stmtImp = $db->prepare($sqlImp);
    $stmtImp->execute();
    $impressora = $stmtImp->fetch(PDO::FETCH_ASSOC);

    if ($impressora) {
        ImpressoraService::imprimir(
            $impressora['ip'],
            $impressora['porta'],
            $conteudo
        );
    }

    // =========================
    // Retorno para JS
    // =========================
    echo json_encode([
        "status" => "ok",
        "total" => $total
    ]);
}

public function totalMesa($atendimento_id)
{
    header('Content-Type: application/json');

    require_once __DIR__ . '/../../config/database.php';

    $database = Database::getInstance();
    $db = $database->getConnection();

   $sql = "
    SELECT 
        COALESCE(SUM(ip.quantidade * ip.preco_unitario),0) as total
    FROM pedidos p
    JOIN itens_pedido ip ON ip.pedido_id = p.id
    WHERE p.atendimento_id = :atendimento
";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':atendimento', $atendimento_id);
    $stmt->execute();

    $total = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($total);
}



}