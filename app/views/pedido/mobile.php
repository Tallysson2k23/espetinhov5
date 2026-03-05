<style>

@media (max-width:768px){

body{
    padding:6px;
}

/* HEADER */

.pdv-top{
    font-size:16px;
    padding:10px;
}

/* GRUPOS */

.pdv-grupos-top{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
}

.grupo-btn{
    flex:1 1 48%;
    font-size:14px;
    padding:10px;
}

/* LAYOUT */

.pdv-main{
    display:block !important;
}

/* PRODUTOS */

.pdv-produtos-area{
    width:100% !important;
}

#produtos-area{
    max-height:35vh;
    overflow:auto;
}

/* BUSCA */

#inputBuscaProduto{
    font-size:16px;
    padding:10px;
}

/* CARRINHO */

.pdv-carrinho-area{
    width:100% !important;
    margin-top:10px;
}

#carrinho{
    font-size:16px;
}

/* ESCONDER HISTÓRICO */

.pedidos-enviados{
    display:none;
}

/* FOOTER */

.pdv-buttons{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.pdv-btn{
    padding:14px;
    font-size:16px;
}

}
/* PRODUTOS MAIS FÁCEIS DE CLICAR */

@media (max-width:768px){

.produto-item{
    font-size:18px;
    padding:14px;
}

.produto-item button{
    font-size:18px;
    padding:8px 14px;
}

}



/* CARRINHO FIXO MOBILE */

@media (max-width:768px){

.pdv-footer{
margin-top:20px;
padding:10px;
background:#111;
border-top:2px solid #ff6600;
}

#carrinho{
    max-height:120px;
    overflow:auto;
}

.pdv-carrinho-area{
    margin-bottom:140px;
}

}

/* BOTÃO CARRINHO MOBILE */

#mobileCartButton{
position:fixed;
bottom:90px;
right:15px;
background:#ff6600;
color:#fff;
font-size:20px;
padding:12px 16px;
border-radius:30px;
box-shadow:0 4px 10px rgba(0,0,0,0.3);
z-index:9999;
cursor:pointer;
}

#mobileCartButton span{
font-weight:bold;
margin-left:6px;
}

/* PAINEL DO CARRINHO */

#mobileCartPanel{
position:fixed;
bottom:0;
left:0;
right:0;
background:#111;
color:#fff;
padding:15px;
display:none;
z-index:9999;
border-top:2px solid #ff6600;

/* NOVO */

max-height:85vh;
overflow-y:auto;
}

.cartHeader{
display:flex;
justify-content:space-between;
font-size:18px;
margin-bottom:10px;
}

#mobileCartItems{
max-height:200px;
overflow:auto;
margin-bottom:10px;
}

.cartTotal{
font-size:18px;
margin-bottom:10px;
}

.btnEnviarCarrinho{
width:100%;
padding:14px;
background:#1fa44a;
color:#fff;
border:none;
border-radius:6px;
font-size:18px;
}

/* ANIMAÇÃO DO CARRINHO */

@keyframes cartPop{
0%{transform:scale(1);}
50%{transform:scale(1.3);}
100%{transform:scale(1);}
}

.cartAnim{
animation:cartPop 0.3s ease;
}

/* ESTILO PEDIDOS ENVIADOS */

.mobile-enviados{
background:#1b1b1b;
padding:10px;
border-radius:8px;
margin-bottom:10px;
font-size:14px;
color:#bbb;
}

.mobile-enviados b{
color:#ff6600;
display:block;
margin-bottom:5px;
}

/* ESTILO PEDIDO ATUAL */

#mobileCartItems{
background:#222;
padding:10px;
border-radius:8px;
margin-bottom:10px;
}

#mobileCartItems li{
font-size:16px;
margin-bottom:4px;
}

.btnEnviarCarrinho.enviando{
background:#888;
}

</style>
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

            <button id="btnEnviarPedido"
        class="pdv-btn pdv-btn-enviar"
        onclick="enviarPedido(<?= $atendimento_id ?>)">
    Enviar para cozinha
</button>

            <button class="pdv-btn"
        onclick="abrirTransferencia()">
    Transferir produtos
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

<div class="modal-header bg-dark text-white">
    <h5 class="modal-title">Observação do Produto</h5>
    <button type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="modal"></button>
</div>

      <div class="modal-body">

        <h5 id="produtoObsNome" style="color:#000; font-weight:600;"></h5>

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

<!-- MODAL TRANSFERIR PRODUTOS -->
<div class="modal fade" id="modalTransferir" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Transferir Produtos</h5>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <h6>Selecione os produtos</h6>

        <div id="listaTransferencia">

            <!-- produtos serão inseridos aqui via JS -->

        </div>

        <hr>

        <label class="mt-2">Mesa destino</label>

        <select id="mesaDestino"
        class="form-select bg-dark text-white">
        </select>

      </div>

      <div class="modal-footer">

        <button class="btn btn-secondary"
                data-bs-dismiss="modal">
            Cancelar
        </button>

        <button class="btn btn-primary"
                onclick="confirmarTransferencia()">
            Transferir
        </button>

      </div>

    </div>
  </div>
</div>

<div id="mobileCartButton">
🛒 <span id="cartCount">0</span>
</div>

<div id="mobileCartPanel">

<div class="cartHeader">
Pedido atual
<button onclick="fecharCarrinho()">✕</button>
</div>

<div id="mobilePedidosEnviados"></div>

<hr>

<ul id="mobileCartItems"></ul>

<div class="cartTotal">
TOTAL R$ <span id="mobileCartTotal">0,00</span>
</div>

<button
id="btnEnviarCarrinho"
class="btnEnviarCarrinho"
onclick="enviarPedidoMobile(<?= $atendimento_id ?>)">
Enviar para cozinha
</button>

</div>

<script>
const ATENDIMENTO_ID = <?= $atendimento_id ?>;
</script>

<script src="/espetinhov5/public/js/pedido.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function(){

    carregarTotalMesa(<?= $atendimento_id ?>);

});

</script>

<script>

let ultimoTotalItens = 0;

document.getElementById("mobileCartButton").onclick = function(){
document.getElementById("mobileCartPanel").style.display = "block";
atualizarCarrinhoMobile();
}

function fecharCarrinho(){
document.getElementById("mobileCartPanel").style.display = "none";
}

function atualizarCarrinhoMobile(){

let carrinho = document.getElementById("carrinho");

let enviados = document.querySelector(".pedidos-enviados");
let enviadosMobile = document.getElementById("mobilePedidosEnviados");

if(enviados){
enviadosMobile.innerHTML =
"<div class='mobile-enviados'><b>Enviado para cozinha</b>" +
enviados.innerHTML +
"</div>";
}

let mobileList = document.getElementById("mobileCartItems");

mobileList.innerHTML = carrinho.innerHTML;

let itens = carrinho.querySelectorAll("li").length;

document.getElementById("cartCount").innerText = itens;

let total = document.getElementById("total").innerText;

document.getElementById("mobileCartTotal").innerText = total;

/* anima somente se mudou */

if(itens !== ultimoTotalItens){

let cartBtn = document.getElementById("mobileCartButton");

cartBtn.classList.add("cartAnim");

setTimeout(()=>{
cartBtn.classList.remove("cartAnim");
},300);

}

ultimoTotalItens = itens;

}

setInterval(atualizarCarrinhoMobile,500);

function enviarPedidoMobile(atendimentoId){

let btn = document.getElementById("btnEnviarCarrinho");

if(btn.classList.contains("enviando")) return;

btn.classList.add("enviando");

btn.innerText = "Enviando...";

btn.disabled = true;

/* chama a função original do sistema */

enviarPedido(atendimentoId);

}

</script>