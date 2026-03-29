<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../app/models/FilaImpressao.php';
require_once __DIR__ . '/../app/services/ImpressoraService.php';

echo "Worker iniciado...\n";

while (true) {

    $model = new FilaImpressao();

    $fila = $model->buscarPendentes(5);

    foreach ($fila as $item) {

        echo "Processando ID {$item['id']}...\n";

        $ok = ImpressoraService::imprimir(
            $item['impressora_ip'],
            9100,
            $item['conteudo'],
            $item['pedido_id']
        );

        if ($ok) {
            $model->atualizarStatus($item['id'], 'sucesso');
            echo "OK\n";
        } else {
            $model->atualizarStatus($item['id'], 'erro', 'Falha ao conectar');
            echo "ERRO\n";
        }
    }

    sleep(2); // espera 2 segundos
}