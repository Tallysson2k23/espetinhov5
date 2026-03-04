<?php

require_once __DIR__ . '/../../core/Model.php';

class Impressora extends Model {

    public function listar() {
        $stmt = $this->db->query("SELECT * FROM impressoras ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($nome, $ip, $porta) {
        $sql = "INSERT INTO impressoras (nome, ip, porta)
                VALUES (:nome, :ip, :porta)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':ip', $ip);
        $stmt->bindValue(':porta', $porta);
        $stmt->execute();
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("DELETE FROM impressoras WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }
}