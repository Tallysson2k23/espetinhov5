let carrinho = [];
let total = 0;

let produtoTemp = null;

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

    if (carrinho.length === 0) {
        alert("Carrinho vazio!");
        return;
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
    .then(response => response.json())
    .then(data => {

        if (data.status === "ok") {

            sessionStorage.setItem(
                "msg_sucesso",
                "Pedido enviado para cozinha!"
            );

            window.location.href = "/espetinhov5/public/dashboard";
        }

    })
    .catch(error => {
        console.error("Erro ao enviar pedido:", error);
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