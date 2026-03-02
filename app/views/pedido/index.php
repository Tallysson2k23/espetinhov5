<div class="pdv-old">

    <!-- HEADER ESTILO ANTIGO -->
    <div class="pdv-top">

        <div class="pdv-left">
            Atendimento - <?= $atendimento_id ?>
        </div>

        <div class="pdv-right">
            <div>Mesa: <strong><?= $_GET['mesa'] ?? '' ?></strong></div>
            <div>Tempo: <strong id="tempoMesa">00:00</strong></div>
        </div>

    </div>


    <!-- GRUPOS HORIZONTAIS -->
    <div class="pdv-grupos-top">
        <?php foreach ($grupos as $grupo) : ?>
            <button class="grupo-btn grupo-top-btn"
                    data-id="<?= $grupo['id'] ?>">
                <?= strtoupper($grupo['nome']) ?>
            </button>
        <?php endforeach; ?>
    </div>


    <!-- CORPO -->
    <div class="pdv-main">

        <!-- PRODUTOS -->
        <div class="pdv-produtos-area">
            <div id="produtos-area">
                Selecione um grupo
            </div>
        </div>

        <!-- CARRINHO -->
        <div class="pdv-carrinho-area">

            <div class="carrinho-header">
                ITENS
            </div>

            <ul id="carrinho"></ul>

        </div>

    </div>


    <!-- RODAPÉ TOTAL FIXO -->
    <div class="pdv-footer">

        <div class="pdv-total-box">
            TOTAL
            <span>R$ <span id="total">0,00</span></span>
        </div>

        <div class="pdv-buttons">
            <button onclick="enviarPedido(<?= $atendimento_id ?>)">
                Enviar para cozinha
            </button>

            <?php if ($_SESSION['nivel'] == 'admin') : ?>
                <button onclick="abrirFechamento()" class="btn-fechar">
                    Fechar mesa
                </button>
            <?php endif; ?>

            <a href="/espetinhov5/public/dashboard">
                Voltar
            </a>
        </div>

    </div>

</div>

<script src="/espetinhov5/public/js/pedido.js"></script>