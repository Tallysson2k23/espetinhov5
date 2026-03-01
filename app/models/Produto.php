<?php

require_once __DIR__ . '/../../core/Model.php';

class Produto extends Model {

    public function listarPorGrupo($grupo_id) {

        $sql = "SELECT * FROM produtos 
                WHERE grupo_id = :grupo_id
                AND ativo = TRUE
                ORDER BY nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':grupo_id', $grupo_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodos() {

    $sql = "SELECT p.*, g.nome as grupo_nome
            FROM produtos p
            JOIN grupos g ON g.id = p.grupo_id
            ORDER BY p.id DESC";

    $stmt = $this->db->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}







}