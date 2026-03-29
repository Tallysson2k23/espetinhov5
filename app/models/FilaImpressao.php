<?php

class FilaImpressao extends Model
{
    public function adicionar($pedidoId, $ip, $conteudo)
    {
        $sql = "INSERT INTO fila_impressao 
                (pedido_id, impressora_ip, conteudo, status)
                VALUES (:pedido_id, :ip, :conteudo, 'pendente')";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':pedido_id', $pedidoId);
        $stmt->bindValue(':ip', $ip);
        $stmt->bindValue(':conteudo', $conteudo);

        return $stmt->execute();
    }

public function listarRecentes()
{
    $sql = "SELECT * FROM fila_impressao
            ORDER BY id DESC
            LIMIT 50";

    $stmt = $this->db->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function atualizarStatus($id, $status, $erro = null)
{
    $sql = "UPDATE fila_impressao
            SET status = :status,
                erro = :erro,
                atualizado_em = NOW()
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);

    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':erro', $erro);

    return $stmt->execute();
}

public function buscarPendentes($limite = 5)
{
    $sql = "SELECT * FROM fila_impressao
            WHERE status = 'pendente'
            ORDER BY id ASC
            LIMIT :limite";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function buscarPorId($id)
{
    $sql = "SELECT * FROM fila_impressao WHERE id = :id LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}




}