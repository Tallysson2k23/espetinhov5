<?php

require_once __DIR__ . '/../../core/Model.php';

class Usuario extends Model {

    public function autenticar($usuario, $senha) {

        $sql = "SELECT * FROM usuarios 
                WHERE usuario = :usuario 
                AND senha = md5(:senha)
                AND ativo = TRUE";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':senha', $senha);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}