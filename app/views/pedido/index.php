<h4>Atendimento Nº <?= $atendimento_id ?></h4>


<div class="row mt-3">

    <!-- COLUNA GRUPOS -->
    <div class="col-12 col-md-3 mb-3">
        <h5>Grupos</h5>
        <?php foreach ($grupos as $grupo) : ?>
            <button class="btn btn-outline-primary w-100 mb-2 grupo-btn"
                    data-id="<?= $grupo['id'] ?>">
                <?= $grupo['nome'] ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- COLUNA PRODUTOS -->
    <div class="col-12 col-md-5 mb-3">
        <h5>Produtos</h5>
        <div id="produtos-area">
            <p>Selecione um grupo</p>
        </div>
    </div>

    <!-- COLUNA CARRINHO -->
    <div class="col-12 col-md-4">
        <h5>Carrinho</h5>
        <ul class="list-group mb-3" id="carrinho"></ul>

        <h4>Total: R$ <span id="total">0.00</span></h4>
        

<button class="btn btn-success w-100 mt-2"
        onclick="enviarPedido(<?= $atendimento_id ?>)">
    Enviar para Cozinha
</button>
<?php if ($_SESSION['nivel'] == 'admin') : ?>

    <button class="btn btn-danger w-100 mt-2"
            onclick="abrirFechamento()">
        Fechar Mesa
    </button>

<?php endif; ?>

        <a href="/espetinhov5/public/dashboard" 
           class="btn btn-secondary w-100 mt-2">
            Voltar
        </a>

<hr>


<h5 class="text-danger">
    Total Parcial: 
    R$ <?= number_format($totalAtendimento ?? 0, 2, ',', '.') ?>
</h5>
<h5>Pedidos Enviados</h5>

<?php if (!empty($pedidos)) : ?>

    <?php foreach ($pedidos as $pedido) : ?>

        <div class="card mb-2">
            <div class="card-body">

                <strong>
                    Pedido #<?= $pedido['id'] ?>
                </strong>
                <br>
                <small>
                    <?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?>
                    - Garçom: <?= $pedido['garcom'] ?>
                </small>

                <ul class="mt-2 mb-0">
                    <?php foreach ($pedido['itens'] as $item) : ?>
                        <li>
                            <?= $item['quantidade'] ?>x <?= $item['nome'] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>
        </div>

    <?php endforeach; ?>

<?php else : ?>

    <p>Nenhum pedido enviado ainda.</p>

<?php endif; ?>
    </div>

</div>

<!-- MODAL FECHAMENTO -->
<div class="modal fade" id="modalFechar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Fechar Mesa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <label class="mb-2">Forma de Pagamento</label>

        <select class="form-select" id="formaPagamento">
            <option value="Dinheiro">Dinheiro</option>
            <option value="Pix">Pix</option>
            <option value="Cartão Crédito">Cartão Crédito</option>
            <option value="Cartão Débito">Cartão Débito</option>
        </select>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>

        <button class="btn btn-danger"
                onclick="confirmarFechamento(<?= $atendimento_id ?>)">
            Confirmar Fechamento
        </button>
      </div>

    </div>
  </div>
</div>

<script src="/espetinhov5/public/js/pedido.js"></script>