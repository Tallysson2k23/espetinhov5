let carrinho = [];
let total = 0;

let permitirSaida = false;

let produtoTemp = null;

let totalMesa = 0;

let enviandoPedido = false;

/* ==============================
   FUNÇÃO DE FILTRO DE BUSCA
============================== */
function aplicarFiltroBusca() {

    const input = document.getElementById("inputBuscaProduto");
    if (!input) return;

    const termo = input.value.toLowerCase();

    document.querySelectorAll(".produto-card").forEach(card => {

        const texto = card.innerText.toLowerCase();

        if (texto.includes(termo)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }

    });

}


function abrirObsProduto(id, nome, preco) {

    produtoTemp = { id, nome, preco };

    document.getElementById("produtoObsNome").innerText = nome;

    document.getElementById("produtoObservacao").value = "";

    let modal = new bootstrap.Modal(
        document.getElementById("modalObsProduto")
    );

    modal.show();
}

function confirmarObsProduto() {

let obs = document.getElementById("produtoObservacao").value;

// produto financeiro
if(produtoTemp.id == 229){

window.financeiroDescricao = obs;

let modalObs = bootstrap.Modal.getInstance(
document.getElementById("modalObsProduto")
);

modalObs.hide();

let modalValor = new bootstrap.Modal(
document.getElementById("modalFinanceiro")
);

modalValor.show();

return;

}

adicionarProduto(
produtoTemp.id,
produtoTemp.nome,
produtoTemp.preco,
obs
);

let modal = bootstrap.Modal.getInstance(
document.getElementById("modalObsProduto")
);

modal.hide();
}

/* ==============================
   ATIVAR BUSCA (UMA VEZ)
============================== */
document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("inputBuscaProduto");

    if (input) {
        input.addEventListener("input", aplicarFiltroBusca);
    }

});

/* ==============================
   CARREGAR PRODUTOS POR GRUPO
============================== */

document.querySelectorAll(".grupo-btn").forEach(btn => {

    btn.addEventListener("click", function() {

        // remove ativo de todos
        document.querySelectorAll(".grupo-btn").forEach(b => 
            b.classList.remove("ativo")
        );

        // adiciona ativo no clicado
        this.classList.add("ativo");

        let grupoId = this.dataset.id;

        fetch("/espetinhov5/public/api/produtos/" + grupoId)
            .then(res => res.json())
            .then(produtos => {

                let area = document.getElementById("produtos-area");
                area.innerHTML = "";

                produtos.forEach(prod => {

                    let imagem = prod.imagem 
                        ? "/espetinhov5/public/uploads/" + prod.imagem 
                        : "https://via.placeholder.com/100x100?text=Sem+Imagem";

                    let card = document.createElement("div");
                    card.className = "card mb-2 produto-card";
                    card.style.cursor = "pointer";

                   card.onclick = function() {
    abrirObsProduto(prod.id, prod.nome, prod.preco);
};

                    card.innerHTML = `
    <div class="produto-card-inner">
        <img src="${imagem}" class="produto-img">

        <div class="produto-info">
            <div class="produto-nome">${prod.nome}</div>
            <div class="produto-preco">
                R$ ${parseFloat(prod.preco).toFixed(2)}
            </div>
        </div>
    </div>
`;

                    area.appendChild(card);

                });

                /* IMPORTANTE:
                   Reaplica filtro após carregar grupo */
                aplicarFiltroBusca();

            });

    });

});


/* ==============================
   CARRINHO
============================== */

function adicionarProduto(id, nome, preco, observacao = "") {

// PRODUTO FINANCEIRO
if(id == 229){

produtoTemp = { id, nome, preco };

document.getElementById("produtoObsNome").innerText = "Descrição financeira";
document.getElementById("produtoObservacao").value = "";

let modal = new bootstrap.Modal(
document.getElementById("modalObsProduto")
);

modal.show();

return;
}

    let existente = carrinho.find(item => item.id === id);

    if (existente) {
        existente.quantidade++;
    } else {
        carrinho.push({
    id,
    nome,
    preco,
    quantidade: 1,
    observacao
});
    }

    calcularTotal();
    atualizarCarrinho();
}

function removerItem(index) {

    if (carrinho[index].quantidade > 1) {
        carrinho[index].quantidade--;
    } else {
        carrinho.splice(index, 1);
    }

    calcularTotal();
    atualizarCarrinho();
}

function calcularTotal() {
    total = 0;
    carrinho.forEach(item => {
        total += item.preco * item.quantidade;
    });

    atualizarTotalTela();
}

function atualizarCarrinho() {

    let lista = document.getElementById("carrinho");
    lista.innerHTML = "";

    carrinho.forEach((item, index) => {

        lista.innerHTML += `
<li class="list-group-item d-flex justify-content-between">

    <div>
        ${item.quantidade}x ${item.nome}

        <br>

        <small style="color:gray">
            ${item.observacao ? "* " + item.observacao : ""}
        </small>
    </div>

    <button class="btn btn-sm btn-danger"
            onclick="removerItem(${index})">
        -
    </button>

</li>
`;
    });

    document.getElementById("total").innerText = total.toFixed(2);
}

/* ==============================
   ENVIAR PEDIDO
============================== */

function enviarPedido(atendimento_id) {

    // bloquear duplo clique
    if (enviandoPedido) return;

    if (carrinho.length === 0) {
        alert("Carrinho vazio!");
        return;
    }

    enviandoPedido = true;

    let botao = document.getElementById("btnEnviarPedido");

    if (botao) {
        botao.disabled = true;
        botao.innerText = "Enviando...";
    }

    fetch("/espetinhov5/public/pedido/salvar", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            atendimento_id: atendimento_id,
            itens: carrinho
        })
    })
    .then(res => res.json())
    .then(res => {

        if (res.status === "ok") {

                permitirSaida = true;
            
            sessionStorage.setItem(
                "msg_sucesso",
                "Pedido enviado para cozinha!"
            );

            window.location.href = "/espetinhov5/public/dashboard";

        } else {

            // liberar botão se erro
            enviandoPedido = false;

            if (botao) {
                botao.disabled = false;
                botao.innerText = "Enviar para cozinha";
            }

            alert("Erro ao enviar pedido.");
        }

    })
    .catch(error => {

        // liberar botão se erro
        enviandoPedido = false;

        if (botao) {
            botao.disabled = false;
            botao.innerText = "Enviar para cozinha";
        }

        console.error("Erro ao enviar pedido:", error);
        alert("Erro ao enviar pedido.");
    });

}
/* ==============================
   FECHAMENTO
============================== */

function abrirFechamento() {

    let modal = new bootstrap.Modal(
        document.getElementById('modalFechar')
    );

    modal.show();
}

function confirmarFechamento(atendimento_id) {

    let forma = document.getElementById("formaPagamento").value;

    fetch("/espetinhov5/public/pedido/fechar", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            atendimento_id: atendimento_id,
            forma_pagamento: forma
        })
    })
    .then(res => res.json())
    .then(res => {

        if (res.status === "ok") {

            sessionStorage.setItem(
                "msg_sucesso",
                "Mesa fechada! Total: R$ " +
                parseFloat(res.total).toFixed(2)
            );

            window.location.href = "/espetinhov5/public/dashboard";
        }

    });

}

function carregarTotalMesa(atendimento_id) {

    fetch("/espetinhov5/public/pedido/total/" + atendimento_id)
        .then(res => res.json())
        .then(res => {

            totalMesa = parseFloat(res.total);

            atualizarTotalTela();

        });

}

function atualizarTotalTela(){

    let totalFinal = totalMesa + total;

    document.getElementById("total").innerText =
        totalFinal.toFixed(2);

}


function abrirTransferencia() {

    let modal = new bootstrap.Modal(
        document.getElementById("modalTransferir")
    );

    modal.show();

    /* ==============================
       CARREGAR ITENS DA MESA
    ============================== */

    fetch("/espetinhov5/public/pedido/itens/" + ATENDIMENTO_ID)
        .then(res => res.json())
        .then(itens => {

            let lista = document.getElementById("listaTransferencia");
            lista.innerHTML = "";

            itens.forEach(item => {

                let obs = item.observacao 
                    ? "<br><small>* " + item.observacao + "</small>"
                    : "";

                lista.innerHTML += `
                    <div class="form-check mb-2">

                        <input class="form-check-input itemTransferir"
                               type="checkbox"
                               value="${item.id}">

                        <label class="form-check-label">

                            ${item.quantidade}x ${item.nome}
                            ${obs}

                        </label>

                    </div>
                `;

            });

        });

    /* ==============================
       CARREGAR MESAS
    ============================== */

    fetch("/espetinhov5/public/api/mesas")
        .then(res => res.json())
        .then(mesas => {

            let select = document.getElementById("mesaDestino");
            select.innerHTML = "";

            mesas.forEach(mesa => {

                let option = document.createElement("option");

                option.value = mesa.id;
                option.text = "Mesa " + mesa.numero;

                select.appendChild(option);

            });

        });

}
function confirmarTransferencia() {

    let itens = [];

    document.querySelectorAll(".itemTransferir:checked").forEach(el => {
        itens.push(parseInt(el.value));
    });

    if (itens.length === 0) {
        alert("Selecione ao menos um produto.");
        return;
    }

    let mesaDestino = document.getElementById("mesaDestino").value;

    fetch("/espetinhov5/public/pedido/transferir", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            atendimento_origem: ATENDIMENTO_ID,
            mesa_destino: mesaDestino,
            itens: itens
        })
    })
    .then(res => res.json())
    .then(res => {

        if (res.status === "ok") {

            alert("Produtos transferidos!");

            /* fecha o modal */
            let modal = bootstrap.Modal.getInstance(
                document.getElementById("modalTransferir")
            );
            modal.hide();

            /* remove os itens da tela */
            itens.forEach(id => {

                let el = document.querySelector(`[data-item-id='${id}']`);

                if(el){
                    el.remove();
                }

            });

            /* atualiza total da mesa */
            carregarTotalMesa(ATENDIMENTO_ID);

        } else {

            alert("Erro ao transferir.");

        }

    })
    .catch(err => {

        console.error(err);
        alert("Erro na comunicação com servidor.");

    });

}

function imprimirConferencia(atendimentoId){

fetch("/espetinhov5/public/pedido/conferencia/" + atendimentoId)
.then(r => r.json())
.then(res => {

if(res.status === "ok"){

alert("Conferência enviada para impressora.");

}

})
.catch(() => {

alert("Erro ao imprimir conferência.");

});

}

function adicionarFinanceiro(){

let valor = parseFloat(document.getElementById("financeiroValor").value);

if(!valor || valor <= 0){
alert("Digite um valor válido.");
return;
}

carrinho.push({
id: 229,
nome: "Lançamento financeiro",
preco: valor,
quantidade: 1,
observacao: window.financeiroDescricao || ""
});

calcularTotal();
atualizarCarrinho();

let modal = bootstrap.Modal.getInstance(
document.getElementById("modalFinanceiro")
);

modal.hide();

document.getElementById("financeiroValor").value = "";

}

function cancelarItem(itemId){

if(!confirm("Deseja realmente cancelar este item?")){
return;
}

fetch("/espetinhov5/public/pedido/cancelarItem",{
method:"POST",
headers:{
"Content-Type":"application/json"
},
body:JSON.stringify({
item_id:itemId
})
})
.then(res=>res.json())
.then(res=>{

if(res.status === "ok"){

alert("Item cancelado.");

location.reload();

}else{

alert("Erro ao cancelar item.");

}

})
.catch(()=>{

alert("Erro ao cancelar item.");

});

}

window.addEventListener("beforeunload", function (e) {

    if (carrinho.length > 0 && !permitirSaida) {

        e.preventDefault();
        e.returnValue = "";

    }

});