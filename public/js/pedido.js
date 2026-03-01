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

let imagem = prod.imagem 
    ? "/espetinhov5/public/uploads/" + prod.imagem 
    : "https://via.placeholder.com/100x100?text=Sem+Imagem";

area.innerHTML += `
    <div class="card mb-2 produto-card"
         onclick="adicionarProduto(${prod.id}, '${prod.nome}', ${prod.preco})"
         style="cursor:pointer">

        <div class="row g-0 align-items-center">

            <div class="col-4">
                <img src="${imagem}"
                     class="img-fluid rounded-start"
                     style="height:80px; object-fit:cover;">
            </div>

            <div class="col-8">
                <div class="card-body p-2">
                    <h6 class="mb-1">${prod.nome}</h6>
                    <strong>R$ ${parseFloat(prod.preco).toFixed(2)}</strong>
                </div>
            </div>

        </div>
    </div>
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


.then(response => response.json())
.then(data => {

    if (data.status === "ok") {

        sessionStorage.setItem(
            "msg_sucesso",
            "Pedido enviado para cozinha!"
        );

        // Redireciona para dashboard
        window.location.href = "/espetinhov5/public/dashboard";
    }

})
.catch(error => {
    console.error("Erro ao enviar pedido:", error);
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