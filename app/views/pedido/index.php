<div class="pdv-old">

    <!-- HEADER -->
    <div class="pdv-top">

        <div class="pdv-left">
            Atendimento - <?= $atendimento_id ?>
        </div>

        <div class="pdv-right">
            <div>Tempo: <strong id="tempoMesa">00:00</strong></div>
        </div>

    </div>


    <!-- GRUPOS -->
    <div class="pdv-grupos-top">
        <?php foreach ($grupos as $grupo) : ?>
           <button class="grupo-btn"
        data-id="<?= $grupo['id'] ?>">
    <?= strtoupper($grupo['nome']) ?>
</button>
        <?php endforeach; ?>
    </div>


    <!-- CORPO -->
    <div class="pdv-main">

        <!-- PRODUTOS -->
        <div class="pdv-produtos-area">
            <div class="busca-produto">
    <input type="text"
           id="inputBuscaProduto"
           placeholder="Pesquisar produto..."
           class="form-control">
</div>
            <div id="produtos-area">
                Selecione um grupo
            </div>
        </div>


        <!-- CARRINHO + HISTÓRICO -->
        <div class="pdv-carrinho-area">

            <div class="carrinho-header">
                ITENS DO PEDIDO ATUAL
            </div>

            <ul id="carrinho"></ul>

            <hr>

            <div class="carrinho-header">
                PEDIDOS JÁ ENVIADOS
            </div>

            <div class="pedidos-enviados">

                <?php if (!empty($pedidos)) : ?>

                    <?php foreach ($pedidos as $pedido) : ?>

                        <div class="pedido-bloco">
                            <strong>
                                Pedido #<?= $pedido['id'] ?>
                            </strong>
                            <br>
                            <small>
                                <?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?>
                                - Garçom: <?= $pedido['garcom'] ?>
                            </small>

                            <ul>
                                <?php foreach ($pedido['itens'] as $item) : ?>
                                    <li>
                                        <?= $item['quantidade'] ?>x <?= $item['nome'] ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    <?php endforeach; ?>

                <?php else : ?>

                    <small>Nenhum pedido enviado ainda.</small>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <!-- RODAPÉ -->
    <div class="pdv-footer">

        <div class="pdv-total-box">
            TOTAL
            <span>R$ <span id="total">0,00</span></span>
        </div>

        <div class="pdv-buttons">

            <button class="pdv-btn pdv-btn-enviar"
                    onclick="enviarPedido(<?= $atendimento_id ?>)">
                Enviar para cozinha
            </button>

            <?php if ($_SESSION['nivel'] == 'admin') : ?>
                <button class="pdv-btn pdv-btn-fechar"
                        onclick="abrirFechamento()">
                    Fechar mesa
                </button>
            <?php endif; ?>

           <a href="/espetinhov5/public/dashboard"
   class="pdv-btn btn-voltar">
   Voltar
</a>

        </div>

    </div>

</div>


<!-- MODAL FECHAR -->
<div class="modal fade" id="modalFechar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">

      <div class="modal-header">
        <h5 class="modal-title">Fechar Mesa</h5>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <h5>Total do Atendimento:</h5>
        <h3 class="text-danger">
            R$ <?= number_format($totalAtendimento ?? 0, 2, ',', '.') ?>
        </h3>

        <label class="mt-3 mb-2">Forma de Pagamento</label>

        <select class="form-select bg-dark text-white"
                id="formaPagamento">
            <option value="Dinheiro">Dinheiro</option>
            <option value="Pix">Pix</option>
            <option value="Cartão Crédito">Cartão Crédito</option>
            <option value="Cartão Débito">Cartão Débito</option>
        </select>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary"
                data-bs-dismiss="modal">
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


<!-- MODAL OBSERVAÇÃO PRODUTO -->
<div class="modal fade" id="modalObsProduto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Observação do Produto</h5>
        <button type="button"
                class="btn-close"
                data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <strong id="produtoObsNome"></strong>

        <textarea
            id="produtoObservacao"
            class="form-control mt-2"
            rows="3"
            placeholder="Ex: sem cebola, bem passado..."></textarea>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary"
                data-bs-dismiss="modal">
            Cancelar
        </button>

        <button class="btn btn-primary"
                onclick="confirmarObsProduto()">
            Adicionar
        </button>
      </div>

    </div>
  </div>
</div>


<script src="/espetinhov5/public/js/pedido.js"></script>
