<h3 class="mb-4">
    Bem-vindo, <?= $_SESSION['usuario'] ?> 
    (<?= $_SESSION['nivel'] ?>)
</h3>

<!-- TOAST SUCESSO -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toastSucesso" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMensagem"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>

        <!-- Barra de tempo -->
        <div class="progress" style="height: 4px;">
            <div id="toastBarra"
                 class="progress-bar bg-light"
                 style="width: 100%"></div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    let mensagem = sessionStorage.getItem("msg_sucesso");

    if (mensagem) {

        let toastEl = document.getElementById("toastSucesso");
        let toastMsg = document.getElementById("toastMensagem");
        let toastBarra = document.getElementById("toastBarra");

        toastMsg.innerText = mensagem;

        let toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });

        toast.show();

        // anima barra
        let largura = 100;
        let intervalo = setInterval(function() {
            largura -= 1;
            toastBarra.style.width = largura + "%";
            if (largura <= 0) clearInterval(intervalo);
        }, 30);

        sessionStorage.removeItem("msg_sucesso");
    }

});
</script>

<div class="row">

<?php foreach ($mesas as $mesa) : 

    $cor = $mesa['status'] == 'livre' ? 'success' : 'danger';

?>

    <div class="col-6 col-md-3 col-lg-2 mb-3">
        <a href="/espetinhov5/public/pedido/abrir/<?= $mesa['id'] ?>" 
           class="btn btn-<?= $cor ?> w-100 p-4">

            <h4>Mesa <?= $mesa['numero'] ?></h4>

        </a>
    </div>

<?php endforeach; ?>

</div>

<a href="/espetinhov5/public/auth/logout" class="btn btn-dark mt-4">
    Sair
</a>