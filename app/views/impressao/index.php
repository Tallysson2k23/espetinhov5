<h2>🖨 Monitor de Impressão</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Impressora</th>
        <th>Status</th>
        <th>Tentativas</th>
        <th>Criado em</th>
        <th>Erro</th>
        <th>Ação</th>
    </tr>

    <?php foreach ($fila as $item): ?>
        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= $item['impressora_ip'] ?></td>
            <td><?= $item['status'] ?></td>
            <td><?= $item['tentativas'] ?></td>
            <td><?= $item['criado_em'] ?></td>
            <td><?= $item['erro'] ?></td>
        </tr>
        <td>
    <a href="/espetinhov5/public/impressao/reimprimir/<?= $item['id'] ?>">
        🔄 Reimprimir
    </a>
</td>
    <?php endforeach; ?>
</table>