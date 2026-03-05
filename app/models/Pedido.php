<?php

require_once __DIR__ . '/../../core/Model.php';

class Pedido extends Model {

    public function criarPedido($atendimento_id, $usuario_id) {

        $sql = "INSERT INTO pedidos (atendimento_id, usuario_id, enviado)
                VALUES (:atendimento_id, :usuario_id, TRUE)
                RETURNING id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':atendimento_id', $atendimento_id);
        $stmt->bindValue(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

public function inserirItem($pedido_id, $produto_id, $quantidade, $preco, $observacao = null) {

    $sql = "INSERT INTO itens_pedido 
            (pedido_id, produto_id, quantidade, preco_unitario, observacao)
            VALUES (:pedido_id, :produto_id, :quantidade, :preco, :observacao)";

    $stmt = $this->db->prepare($sql);

    $stmt->bindValue(':pedido_id', $pedido_id);
    $stmt->bindValue(':produto_id', $produto_id);
    $stmt->bindValue(':quantidade', $quantidade);
    $stmt->bindValue(':preco', $preco);
    $stmt->bindValue(':observacao', $observacao);

    $stmt->execute();
}

public function listarPorAtendimento($atendimento_id) {

    $sql = "SELECT 
                p.id,
                p.created_at,
                COALESCE(u.nome, 'Transferência') as garcom
            FROM pedidos p
            LEFT JOIN usuarios u ON u.id = p.usuario_id
            WHERE p.atendimento_id = :atendimento_id
            ORDER BY p.id ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':atendimento_id', $atendimento_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function listarItens($pedido_id) {

    $sql = "SELECT ip.quantidade, pr.nome
            FROM itens_pedido ip
            JOIN produtos pr ON pr.id = ip.produto_id
            WHERE ip.pedido_id = :pedido_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':pedido_id', $pedido_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function calcularTotalAtendimento($atendimento_id) {

    $sql = "SELECT SUM(ip.quantidade * ip.preco_unitario) as total
            FROM pedidos p
            JOIN itens_pedido ip ON ip.pedido_id = p.id
            JOIN atendimentos a ON a.id = p.atendimento_id
            WHERE p.atendimento_id = :atendimento_id
            AND a.aberto = TRUE";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':atendimento_id', $atendimento_id);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return $resultado['total'] ?? 0;
}

public function listarItensComGrupo($pedido_id) {

    $sql = "SELECT 
                ip.quantidade,
                p.nome,
                g.impressora,
                ip.observacao
            FROM itens_pedido ip
            JOIN produtos p ON p.id = ip.produto_id
            JOIN grupos g ON g.id = p.grupo_id
            WHERE ip.pedido_id = :pedido_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':pedido_id', $pedido_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function buscarItensAtendimento($atendimento_id)
{

    $sql = "
        SELECT
            ip.id,
            ip.quantidade,
            ip.observacao,
            p.nome
        FROM itens_pedido ip
        JOIN pedidos pe ON pe.id = ip.pedido_id
        JOIN produtos p ON p.id = ip.produto_id
        WHERE pe.atendimento_id = :atendimento_id
        ORDER BY ip.id DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':atendimento_id', $atendimento_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

public function transferirItens($itens, $mesaDestino, $atendimentoOrigem)
{

    $this->db->beginTransaction();

    try {

        /* =============================
           GERAR NUMERO DO ATENDIMENTO
        ============================= */

        $sql = "SELECT COALESCE(MAX(numero_atendimento),0)+1 AS numero 
                FROM atendimentos";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $numero = $stmt->fetch(PDO::FETCH_ASSOC)['numero'];


        /* =============================
           CRIAR NOVO ATENDIMENTO
        ============================= */

       $sql = "INSERT INTO atendimentos 
        (mesa_id, numero_atendimento, created_at)
        VALUES (:mesa, :numero, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mesa', $mesaDestino);
        $stmt->bindValue(':numero', $numero);
        $stmt->execute();

        $atendimentoDestino = $this->db->lastInsertId();


        /* =============================
           MARCAR MESA COMO OCUPADA
        ============================= */

        $sql = "UPDATE mesas 
        SET status = 'ocupada',
            inicio_atendimento = NOW()
        WHERE id = :mesa";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':mesa', $mesaDestino);
$stmt->execute();


        /* =============================
           CRIAR PEDIDO DESTINO
        ============================= */

        $sql = "INSERT INTO pedidos (atendimento_id, enviado, created_at)
                VALUES (:atendimento, 't', NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':atendimento', $atendimentoDestino);
        $stmt->execute();

        $pedidoDestino = $this->db->lastInsertId();


        /* =============================
           MOVER ITENS
        ============================= */

        foreach ($itens as $itemId) {

            $sql = "SELECT * FROM itens_pedido WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $itemId);
            $stmt->execute();

            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                continue;
            }

            /* inserir item no novo pedido */

            $sql = "INSERT INTO itens_pedido
                    (pedido_id, produto_id, quantidade, preco_unitario, observacao)
                    VALUES (:pedido, :produto, :quantidade, :preco, :obs)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':pedido' => $pedidoDestino,
                ':produto' => $item['produto_id'],
                ':quantidade' => $item['quantidade'],
                ':preco' => $item['preco_unitario'],
                ':obs' => $item['observacao']
            ]);


            /* remover item da mesa origem */

            $sql = "DELETE FROM itens_pedido WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $itemId);
            $stmt->execute();

        }

        $this->db->commit();

        return true;

    } catch (Exception $e) {

        $this->db->rollBack();

        echo json_encode([
            "status" => "erro",
            "msg" => $e->getMessage()
        ]);

        exit;

    }

}



}