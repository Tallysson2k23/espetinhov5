<h3 class="mb-4">
    Bem-vindo, <?= $_SESSION['usuario'] ?> 
    (<?= $_SESSION['nivel'] ?>)
</h3>
<div id="toast-sucesso"
     style="position:fixed;
            top:20px;
            right:20px;
            background:#198754;
            color:white;
            padding:15px 25px;
            border-radius:8px;
            display:none;
            z-index:9999;">

    <span id="toast-msg"></span>

    <div style="height:4px;
                background:white;
                margin-top:8px;
                width:100%;
                animation: diminuir 3s linear forwards;">
    </div>
</div>

<style>
@keyframes diminuir {
    from { width:100%; }
    to { width:0%; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {

    let msg = sessionStorage.getItem("msg_sucesso");

    if (msg) {

        let toast = document.getElementById("toast-sucesso");
        document.getElementById("toast-msg").innerText = msg;

        toast.style.display = "block";

        setTimeout(() => {
            toast.style.display = "none";
        }, 3000);

        sessionStorage.removeItem("msg_sucesso");
    }

});
</script>

    <?php if ($_SESSION['nivel'] == 'admin') : ?>

    <a href="/espetinhov5/public/admin/usuarios"
       class="btn btn-secondary ms-2">
       Gerenciar Usuários
    </a>
<?php endif; ?>

<?php if ($_SESSION['nivel'] == 'admin') : ?>

    <div class="mb-3">
        <a href="/espetinhov5/public/admin/produtos"
           class="btn btn-primary">
           Gerenciar Produtos
        </a>
    </div>
<?php endif; ?>



<?php if ($_SESSION['nivel'] == 'admin') : ?>

    <a href="/espetinhov5/public/admin/grupos"
       class="btn btn-dark ms-2">
       Gerenciar Grupos
    </a>

<?php endif; ?>

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
        <?php if ($mesa['inicio_atendimento']) : ?>
    <small class="timer"
           data-segundos="<?= (int)$mesa['segundos'] ?>">
        00:00:00
    </small>
<?php endif; ?>
    </div>

<?php endforeach; ?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    function formatarTempo(segundos) {

        let h = Math.floor(segundos / 3600);
        let m = Math.floor((segundos % 3600) / 60);
        let s = segundos % 60;

        return String(h).padStart(2,'0') + ":" +
               String(m).padStart(2,'0') + ":" +
               String(s).padStart(2,'0');
    }

    document.querySelectorAll(".timer").forEach(function(el) {

        let segundos = parseInt(el.dataset.segundos);

        el.innerText = formatarTempo(segundos);

        setInterval(function() {
            segundos++;
            el.innerText = formatarTempo(segundos);
        }, 1000);

    });

});
</script>

<a href="/espetinhov5/public/auth/logout" class="btn btn-dark mt-4">
    Sair
</a>