let carrinho = [];
let total = 0;

document.querySelectorAll(".grupo-btn").forEach(btn => {

    btn.addEventListener("click", function() {

        let grupoId = this.dataset.id;

        fetch("/espetinhov5/public/api/produtos/" + grupoId)
            .then(res => res.json())
            .then(produtos => {

                let area = document.getElementById("produtos-area");
                area.innerHTML = "";

                produtos.forEach(prod => {

                    area.innerHTML += `
                        <button class="btn btn-outline-dark w-100 mb-2"
                                onclick="adicionarProduto(${prod.id}, '${prod.nome}', ${prod.preco})">
                            ${prod.nome} - R$ ${prod.preco}
                        </button>
                    `;
                });

            });

    });

});

function adicionarProduto(id, nome, preco) {

    let existente = carrinho.find(item => item.id === id);

    if (existente) {
        existente.quantidade++;
    } else {
        carrinho.push({id, nome, preco, quantidade: 1});
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
                ${item.quantidade}x ${item.nome}
                <button class="btn btn-sm btn-danger"
                        onclick="removerItem(${index})">
                    -
                </button>
            </li>
        `;
    });

    document.getElementById("total").innerText = total.toFixed(2);
}

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
    .then(res => res.json())
    .then(res => {

        if (res.status === "ok") {
            alert("Pedido enviado com sucesso!");
            carrinho = [];
            atualizarCarrinho();
        }

    });

}

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