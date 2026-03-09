<style>

@media (max-width:768px){

.pdv-buttons{
display:none;
}

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
display:none !important;
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
    margin-bottom:20px;
}
}



/* LISTA COMPACTA DE PRODUTOS */

@media (max-width:768px){

.produto-card-inner{
display:flex;
align-items:center;
gap:10px;
}

.produto-img{
width:40px;
height:40px;
object-fit:cover;
border-radius:6px;
}

.produto-info{
flex:1;
}

.produto-nome{
font-size:14px;
font-weight:600;
line-height:1.2;
}

.produto-preco{
font-size:13px;
color:#ff6600;
margin-top:2px;
}

.produto-card{
padding:6px;
margin-bottom:6px;
}

}

/* LISTA DE PRODUTOS MOBILE */

@media (max-width:768px){

#produtos-area{
width:100%;
overflow:visible;
max-height:none;
}

.produto-card{
display:flex;
align-items:center;
padding:8px;
margin-bottom:6px;
background:#1b1b1b;
border-radius:8px;
}

.produto-img{
width:45px;
height:45px;
object-fit:cover;
border-radius:6px;
margin-right:10px;
}

.produto-info{
flex:1;
}

.produto-nome{
font-size:15px;
font-weight:600;
}

.produto-preco{
font-size:14px;
color:#ff6600;
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
z-index:1000;
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
z-index:1000;
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

.btnTransferirMobile{
width:100%;
padding:14px;
background:#2563eb;
color:#fff;
border:none;
border-radius:6px;
font-size:18px;
margin-top:8px;
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

    <?php if ($_SESSION['nivel'] == 'admin') : ?>

        <button
            style="margin-left:10px; font-size:12px;"
            onclick="cancelarItem(<?= $item['id'] ?>)">
            ❌
        </button>

    <?php endif; ?>

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

        <div id="areaTroco" style="display:none; margin-top:15px;">

<label class="mb-1">Valor recebido</label>

<input type="number"
id="valorRecebido"
class="form-control"
step="0.01"
placeholder="0,00"
oninput="calcularTroco()">

<div style="margin-top:10px;">
Troco: 
<strong style="color:#00ff88;">
R$ <span id="valorTroco">0,00</span>
</strong>
</div>

</div>

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

<!-- MODAL PRODUTO FINANCEIRO -->

<div class="modal fade" id="modalFinanceiro" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-dark text-white">
<h5 class="modal-title">Lançamento Financeiro</h5>

<button type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<label class="mt-3 mb-1">Valor</label>

<input type="number"
id="financeiroValor"
class="form-control"
step="0.01"
inputmode="decimal"
pattern="[0-9]*"
placeholder="0,00"
style="font-size:22px; padding:12px;">

<!-- telcado do celular
<input type="number"
id="financeiroValor"
class="form-control"
step="0.01"
placeholder="0,00"> -->

</div>

<div class="modal-footer">

<button class="btn btn-secondary"
data-bs-dismiss="modal">
Cancelar
</button>

<button class="btn btn-success"
onclick="adicionarFinanceiro()">
Adicionar
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
<div class="cartHeader">
Itens do pedido
</div>
<ul id="mobileCartItems"></ul>

<hr>

<div id="mobilePedidosEnviados"></div>

<div class="cartTotal">
TOTAL R$ <span id="mobileCartTotal">0,00</span>
</div>

<button
id="btnEnviarCarrinho"
class="btnEnviarCarrinho"
onclick="enviarPedidoMobile(<?= $atendimento_id ?>)">
Enviar para cozinha
</button>

<!--
<button
class="btnTransferirMobile"
onclick="abrirTransferencia()">
Transferir produtos
</button> -->

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

function verificarFormaPagamento(){

let forma = document.getElementById("formaPagamento").value;

let areaTroco = document.getElementById("areaTroco");

if(forma === "Dinheiro"){

areaTroco.style.display = "block";

}else{

areaTroco.style.display = "none";

}

}

document.getElementById("formaPagamento").addEventListener("change", verificarFormaPagamento);

/* executa ao abrir página */

verificarFormaPagamento();

</script>

<script>

function calcularTroco(){

let total = parseFloat("<?= $totalAtendimento ?? 0 ?>");

let recebido = parseFloat(document.getElementById("valorRecebido").value);

if(isNaN(recebido)){
document.getElementById("valorTroco").innerText = "0,00";
return;
}

let troco = recebido - total;

if(troco < 0){
troco = 0;
}

document.getElementById("valorTroco").innerText =
troco.toFixed(2).replace(".",",");

}

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