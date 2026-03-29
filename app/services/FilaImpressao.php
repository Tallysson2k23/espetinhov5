<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/ImpressoraService.php';

class FilaImpressao {

    public static function processar() {

        $db = Database::getInstance()->getConnection();

        // 🔒 pegar 1 item da fila (evita conflito)
        $sql = "SELECT * FROM fila_impressao
                WHERE status = 'pendente'
                ORDER BY id ASC
                LIMIT 1
                FOR UPDATE SKIP LOCKED";

        $db->beginTransaction();

        $stmt = $db->query($sql);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            $db->commit();
            return;
        }

        // marcar como imprimindo
        $update = $db->prepare("UPDATE fila_impressao SET status = 'imprimindo' WHERE id = :id");
        $update->bindValue(':id', $pedido['id']);
        $update->execute();

        $db->commit();

        // 🔥 imprimir fora da transação
        $ok = ImpressoraService::imprimir(
            $pedido['ip'],
            $pedido['porta'],
            $pedido['conteudo']
        );

        // atualizar status final
        $novoStatus = $ok ? 'impresso' : 'erro';

        $updateFinal = $db->prepare("UPDATE fila_impressao SET status = :status WHERE id = :id");
        $updateFinal->bindValue(':status', $novoStatus);
        $updateFinal->bindValue(':id', $pedido['id']);
        $updateFinal->execute();
    }
}