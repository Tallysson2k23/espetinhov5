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

    $sql = "SELECT 
        ip.id,
        ip.quantidade,
        ip.observacao,
        pr.nome
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
        ip.produto_id,
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

    /* descobrir mesa de origem */

$sql = "SELECT mesa_id
        FROM atendimentos
        WHERE id = :atendimento";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':atendimento', $atendimentoOrigem);
$stmt->execute();

$mesaOrigem = $stmt->fetch(PDO::FETCH_ASSOC)['mesa_id'];


    $usuarioId = $_SESSION['usuario_id'] ?? null;

    $this->db->beginTransaction();

    try {

    /* =============================
   VERIFICAR ATENDIMENTO EXISTENTE
============================= */

$sql = "SELECT id
        FROM atendimentos
        WHERE mesa_id = :mesa
        AND aberto = true
        LIMIT 1";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':mesa', $mesaDestino);
$stmt->execute();

$atendimentoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
/* =============================
   USAR OU CRIAR ATENDIMENTO
============================= */

if ($atendimentoExistente) {

    $atendimentoDestino = $atendimentoExistente['id'];

} else {

    $sql = "SELECT COALESCE(MAX(numero_atendimento),0)+1 AS numero 
            FROM atendimentos";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $numero = $stmt->fetch(PDO::FETCH_ASSOC)['numero'];

    $sql = "INSERT INTO atendimentos 
    (mesa_id, numero_atendimento, created_at)
    VALUES (:mesa, :numero, NOW())";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':mesa', $mesaDestino);
    $stmt->bindValue(':numero', $numero);
    $stmt->execute();

    $atendimentoDestino = $this->db->lastInsertId();
}



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
   VERIFICAR PEDIDO EXISTENTE
============================= */

$sql = "SELECT id
        FROM pedidos
        WHERE atendimento_id = :atendimento
        ORDER BY id DESC
        LIMIT 1";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':atendimento', $atendimentoDestino);
$stmt->execute();

$pedidoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

/* =============================
   CRIAR PEDIDO DESTINO
============================= */

$sql = "INSERT INTO pedidos (atendimento_id, usuario_id, enviado, created_at)
        VALUES (:atendimento, :usuario, 't', NOW())";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':atendimento', $atendimentoDestino);
$stmt->bindValue(':usuario', $usuarioId);
$stmt->execute();

$pedidoDestino = $this->db->lastInsertId();

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
            $sql = "SELECT mesa_id 
        FROM atendimentos
        WHERE id = :atendimento";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':atendimento', $atendimentoOrigem);
$stmt->execute();

$mesaOrigem = $stmt->fetchColumn();

     $obsTransferencia = "Transferido da mesa " . $mesaOrigem;

if (!empty($item['observacao'])) {
    $obsTransferencia = $item['observacao'] . " -> " . $obsTransferencia;
}

            $sql = "INSERT INTO itens_pedido
                    (pedido_id, produto_id, quantidade, preco_unitario, observacao)
                    VALUES (:pedido, :produto, :quantidade, :preco, :obs)";

            $stmt = $this->db->prepare($sql);


            $stmt->execute([
                ':pedido' => $pedidoDestino,
                ':produto' => $item['produto_id'],
                ':quantidade' => $item['quantidade'],
                ':preco' => $item['preco_unitario'],
               ':obs' => $obsTransferencia
            ]);


            /* remover item da mesa origem */

            $sql = "DELETE FROM itens_pedido WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $itemId);
            $stmt->execute();

        }

        /* =============================
   VERIFICAR SE SOBROU ITEM NA ORIGEM
============================= */

$sql = "SELECT COUNT(*) as total
        FROM itens_pedido ip
        JOIN pedidos p ON ip.pedido_id = p.id
        WHERE p.atendimento_id = :atendimento";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':atendimento', $atendimentoOrigem);
$stmt->execute();

$totalItens = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

if ($totalItens == 0) {

/* fechar atendimento origem */

$sql = "UPDATE atendimentos
        SET aberto = false,
            fechado = true,
            fechado_em = NOW()
        WHERE id = :atendimento";

$stmt = $this->db->prepare($sql);
$stmt->bindValue(':atendimento', $atendimentoOrigem);
$stmt->execute();

    /* liberar mesa origem */

    $sql = "UPDATE mesas
            SET status = 'livre',
                inicio_atendimento = NULL
            WHERE id = (
                SELECT mesa_id
                FROM atendimentos
                WHERE id = :atendimento
            )";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':atendimento', $atendimentoOrigem);
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

public function cancelarItem($itemId)
{
    try {

        // buscar quantidade atual
        $sql = "SELECT quantidade 
                FROM itens_pedido
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $itemId);
        $stmt->execute();

        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false;
        }

        $quantidade = (int)$item['quantidade'];

        // se tiver mais de 1 → diminuir
        if ($quantidade > 1) {

            $sql = "UPDATE itens_pedido
                    SET quantidade = quantidade - 1
                    WHERE id = :id";

        } else {

            // se tiver apenas 1 → excluir
            $sql = "DELETE FROM itens_pedido
                    WHERE id = :id";

        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $itemId);

        return $stmt->execute();

    } catch (Exception $e) {

        return false;

    }
}



}