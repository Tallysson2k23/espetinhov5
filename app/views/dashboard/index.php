<div class="dashboard-container">

    <!-- HEADER -->
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title">
                <?= $_SESSION['usuario'] ?>
                <span class="nivel">(<?= $_SESSION['nivel'] ?>)</span>
            </h2>
        </div>

        <a href="/espetinhov5/public/auth/logout"
           class="btn btn-logout">
            Sair
        </a>
    </div>


    <!-- MENU ADMIN -->
    <?php if ($_SESSION['nivel'] == 'admin') : ?>
        <div class="admin-menu">

            <a href="/espetinhov5/public/admin/usuarios" class="admin-item">
                Usuários
            </a>

            <a href="/espetinhov5/public/admin/produtos" class="admin-item">
                Produtos
            </a>

            <a href="/espetinhov5/public/admin/grupos" class="admin-item">
                Grupos
            </a>

            <a href="/espetinhov5/public/admin/impressoras" class="admin-item">
                Impressoras
            </a>

        </div>
    <?php endif; ?>


    <!-- GRID MESAS -->
    <div class="mesas-grid">

        <?php foreach ($mesas as $mesa) : ?>

            <?php
                $classe = $mesa['status'] == 'livre'
                    ? 'mesa-livre'
                    : 'mesa-ocupada';
            ?>

            <a id="mesa-<?= $mesa['id'] ?>"
               href="/espetinhov5/public/pedido/abrir/<?= $mesa['id'] ?>"
               class="mesa-card <?= $classe ?>">

                <div class="mesa-nome">
                    Mesa <?= $mesa['numero'] ?>
                </div>

                <?php if ($mesa['inicio_atendimento']) : ?>
                    <div class="timer"
                         data-segundos="<?= (int)$mesa['segundos'] ?>">
                        00:00:00
                    </div>
                <?php endif; ?>

            </a>

        <?php endforeach; ?>

    </div>

</div>



<!-- ============================= -->
<!-- TOAST (MANTIDO ORIGINAL) -->
<!-- ============================= -->

<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toastSucesso" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMensagem"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>

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



<!-- TIMER (MANTIDO ORIGINAL) -->
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



<!-- REALTIME (MANTIDO ORIGINAL) -->
<script>

function atualizarMesas() {

    fetch("/espetinhov5/public/dashboard/statusMesas")
        .then(res => res.json())
        .then(mesas => {

            mesas.forEach(mesa => {

                let card = document.getElementById("mesa-" + mesa.id);

                if (!card) return;

                if (mesa.status === "ocupada") {
                    card.classList.remove("mesa-livre");
                    card.classList.add("mesa-ocupada");
                } else {
                    card.classList.remove("mesa-ocupada");
                    card.classList.add("mesa-livre");
                }

            });

        });

}

setInterval(atualizarMesas, 3000);

</script>