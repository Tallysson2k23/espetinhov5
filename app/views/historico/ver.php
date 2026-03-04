<link rel="stylesheet" href="/espetinhov5/public/assets/css/historico.css">

<div class="historico-container">

 <h2 class="historico-titulo">
    Atendimento #<?= $atendimento['id'] ?>
</h2>
<div class="historico-info">

    <strong>Mesa:</strong> <?= $atendimento['mesa'] ?><br>
    <strong>Pagamento:</strong> <?= $atendimento['forma_pagamento'] ?><br>
    <strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($atendimento['created_at'])) ?>

</div>

    <hr>

    <h4>Itens do Pedido</h4>

   <table class="historico-tabela">

        <thead>
            <tr>
                <th>Qtd</th>
                <th>Produto</th>
                <th>Observação</th>
                <th>Valor</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($itens as $item): ?>

            <tr>
                <td><?= $item['quantidade'] ?></td>

                <td><?= $item['nome'] ?></td>

                <td><?= $item['observacao'] ?></td>

                <td>
                    R$ <?= number_format($item['preco_unitario'],2,',','.') ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>


  <div class="historico-total">
    Total: R$ <?= number_format($atendimento['valor_total'],2,',','.') ?>
</div>


    <div class="historico-botoes">

       <a href="/espetinhov5/public/historico"
   class="btn-voltar">
   Voltar
</a>

<a href="/espetinhov5/public/historico/reimprimir/<?= $atendimento['id'] ?>"
   class="btn-reimprimir">
   Reimprimir Cupom
</a>


<a href="/espetinhov5/public/dashboard"
   class="btn-voltar-mesas">
Voltar para Mesas
</a>

    </div>

</div>