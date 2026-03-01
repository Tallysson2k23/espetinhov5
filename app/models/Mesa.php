<?php

require_once __DIR__ . '/../../core/Model.php';

class Mesa extends Model {

public function listarTodas() {

    $sql = "SELECT *,
            EXTRACT(EPOCH FROM (NOW() - inicio_atendimento)) as segundos
            FROM mesas
            ORDER BY numero ASC";

    $stmt = $this->db->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
public function atualizarStatus($mesa_id, $status) {

    $sql = "UPDATE mesas 
            SET status = :status
            WHERE id = :mesa_id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':mesa_id', $mesa_id, PDO::PARAM_INT);
    $stmt->execute();
}




}