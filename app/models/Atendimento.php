<?php

require_once __DIR__ . '/../../core/Model.php';

class Atendimento extends Model {

    public function buscarAbertoPorMesa($mesa_id) {

    $sql = "SELECT * FROM atendimentos 
            WHERE mesa_id = :mesa_id 
            AND aberto = TRUE
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':mesa_id', $mesa_id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function criarAtendimento($mesa_id) {

    // Descobrir último número de atendimento da mesa
    $sqlNumero = "SELECT COALESCE(MAX(numero_atendimento),0) + 1 as numero
                  FROM atendimentos
                  WHERE mesa_id = :mesa_id";

    $stmtNumero = $this->db->prepare($sqlNumero);
    $stmtNumero->bindValue(':mesa_id', $mesa_id);
    $stmtNumero->execute();
    $numero = $stmtNumero->fetch(PDO::FETCH_ASSOC)['numero'];

    // Inserir atendimento
    $sql = "INSERT INTO atendimentos (mesa_id, numero_atendimento)
            VALUES (:mesa_id, :numero)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':mesa_id', $mesa_id);
    $stmt->bindValue(':numero', $numero);
    $stmt->execute();

    // Buscar o atendimento recém criado
    $sqlBuscar = "SELECT * FROM atendimentos
                  WHERE mesa_id = :mesa_id
                  AND aberto = TRUE
                  ORDER BY id DESC
                  LIMIT 1";

    $stmtBuscar = $this->db->prepare($sqlBuscar);
    $stmtBuscar->bindValue(':mesa_id', $mesa_id);
    $stmtBuscar->execute();

    return $stmtBuscar->fetch(PDO::FETCH_ASSOC);
}

}