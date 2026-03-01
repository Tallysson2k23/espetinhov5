<?php

require_once __DIR__ . '/../../core/Model.php';

class Grupo extends Model {

    public function listarAtivos() {

        $sql = "SELECT * FROM grupos 
                WHERE ativo = TRUE
                ORDER BY id ASC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}