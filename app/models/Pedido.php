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

    public function inserirItem($pedido_id, $produto_id, $quantidade, $preco) {

        $sql = "INSERT INTO itens_pedido 
                (pedido_id, produto_id, quantidade, preco_unitario)
                VALUES (:pedido_id, :produto_id, :quantidade, :preco)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pedido_id', $pedido_id);
        $stmt->bindValue(':produto_id', $produto_id);
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':preco', $preco);
        $stmt->execute();
    }

public function listarPorAtendimento($atendimento_id) {

    $sql = "SELECT p.id, p.created_at, u.nome as garcom
            FROM pedidos p
            JOIN usuarios u ON u.id = p.usuario_id
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
            WHERE p.atendimento_id = :atendimento_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':atendimento_id', $atendimento_id);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return $resultado['total'] ?? 0;
}




}