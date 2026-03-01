<?php

require_once __DIR__ . '/../../core/Model.php';

class Mesa extends Model {

    public function listarTodas() {

        $sql = "SELECT * FROM mesas ORDER BY numero ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
   public function atualizarStatus($mesa_id, $status) {

    if ($status == 'ocupada') {

        $sql = "UPDATE mesas 
                SET status = :status,
                    inicio_atendimento = NOW()
                WHERE id = :mesa_id";

    } else {

        $sql = "UPDATE mesas 
                SET status = :status,
                    inicio_atendimento = NULL
                WHERE id = :mesa_id";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':mesa_id', $mesa_id, PDO::PARAM_INT);
    $stmt->execute();
}




}