<?php

require_once __DIR__ . '/../../config/database.php';

class HistoricoController {

    public function index() {

        if (!isset($_SESSION['usuario'])) {
            header("Location: /espetinhov5/public/login");
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Data escolhida no calendário
        $data = $_GET['data'] ?? date('Y-m-d');

        $sql = "SELECT 
                    a.id,
                    a.forma_pagamento,
                    a.valor_total,
                    a.created_at,
                    m.numero AS mesa
                FROM atendimentos a
                JOIN mesas m ON m.id = a.mesa_id
                WHERE a.fechado = TRUE
                AND DATE(a.created_at) = :data
                ORDER BY m.numero ASC";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':data', $data);
        $stmt->execute();

        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/historico/index.php';
    }


    public function ver($id) {

        $db = Database::getInstance()->getConnection();

        $sql = "SELECT 
                    a.id,
                    a.valor_total,
                    a.forma_pagamento,
                    a.created_at,
                    m.numero AS mesa
                FROM atendimentos a
                JOIN mesas m ON m.id = a.mesa_id
                WHERE a.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlItens = "SELECT 
                        ip.quantidade,
                        ip.preco_unitario,
                        ip.observacao,
                        p.nome
                    FROM itens_pedido ip
                    JOIN pedidos pe ON pe.id = ip.pedido_id
                    JOIN produtos p ON p.id = ip.produto_id
                    WHERE pe.atendimento_id = :id";

        $stmtItens = $db->prepare($sqlItens);
        $stmtItens->bindValue(':id', $id);
        $stmtItens->execute();

        $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/historico/ver.php';
    }



    public function reimprimir($id) {

        require_once __DIR__ . '/../services/ImpressoraService.php';
        require_once __DIR__ . '/../services/CupomService.php';

        $db = Database::getInstance()->getConnection();

        $sql = "SELECT 
                    a.id,
                    a.valor_total,
                    a.forma_pagamento,
                    m.numero AS mesa
                FROM atendimentos a
                JOIN mesas m ON m.id = a.mesa_id
                WHERE a.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);


        $sqlItens = "SELECT 
                        ip.quantidade,
                        ip.preco_unitario,
                        p.nome
                    FROM itens_pedido ip
                    JOIN pedidos pe ON pe.id = ip.pedido_id
                    JOIN produtos p ON p.id = ip.produto_id
                    WHERE pe.atendimento_id = :id";

        $stmtItens = $db->prepare($sqlItens);
        $stmtItens->bindValue(':id', $id);
        $stmtItens->execute();

        $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);


        $conteudo = CupomService::gerarFechamento(
            $atendimento['mesa'],
            $id,
            $itens,
            $atendimento['valor_total'],
            $atendimento['forma_pagamento'],
            $_SESSION['usuario']
        );

        // Impressora 3 (fechamento)
        $ip = "192.168.68.45";
        $porta = 9100;

        ImpressoraService::imprimir($ip, $porta, $conteudo);

        header("Location: /espetinhov5/public/historico/ver/$id");
    }
}