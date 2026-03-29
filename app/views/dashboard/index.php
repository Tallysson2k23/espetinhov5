<div class="dashboard-wrapper">

    <!-- HEADER SUPERIOR -->
    <div class="topbar">

        <div class="brand">
            <div class="logo-circle">
    <img src="/espetinhov5/public/assets/img/logo.png" 
         alt="Logo"
         class="logo-img">
</div>
            <div>
                <div class="brand-title">Espetinho Central</div>
                <div class="brand-sub">Sistema PDV</div>
            </div>
        </div>

        <div class="top-actions">
            <span class="user-name">
                <?= $_SESSION['usuario'] ?> 
                <small>(<?= $_SESSION['nivel'] ?>)</small>
            </span>

            <a href="/espetinhov5/public/auth/logout" class="btn-logout">
                Sair
            </a>
        </div>

    </div>

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

        <a href="/espetinhov5/public/historico" class="admin-item">
            Histórico
        </a>

        <a href="/espetinhov5/public/impressao" class="admin-item">🖨 fila</a>

    </div>
<?php endif; ?>



    <!-- STATUS RESUMO -->
    <?php
        $livres = 0;
        $ocupadas = 0;
        foreach ($mesas as $m) {
            if ($m['status'] == 'livre') $livres++;
            else $ocupadas++;
        }
    ?>

    <div class="status-bar">
        <div class="status-item livre">
            <span class="dot"></span>
            <?= $livres ?> livres
        </div>

        <div class="status-item ocupada">
            <span class="dot"></span>
            <?= $ocupadas ?> ocupadas
        </div>
    </div>


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

                <div class="mesa-header">
                    <div class="mesa-numero">
                        <?= $mesa['numero'] ?>
                    </div>

                    <div class="status-dot"></div>
                </div>

                <div class="mesa-label">
                    MESA
                </div>

                <div class="mesa-status">
                    <?= $mesa['status'] == 'livre' ? 'Livre' : 'Ocupada' ?>
                </div>

                <?php if ($mesa['inicio_atendimento']) : ?>
                   <div class="timer"
     data-segundos="<?= (int)$mesa['segundos'] ?>">
     ⏱ 00:00:00
</div>
                <?php endif; ?>

            </a>

        <?php endforeach; ?>

    </div>

</div>

<?php if (isset($_SESSION['msg_sucesso'])) : ?>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toastSucesso"
         class="toast align-items-center text-bg-success border-0 show"
         role="alert">

        <div class="d-flex">
            <div class="toast-body">
                <?= $_SESSION['msg_sucesso']; ?>
            </div>
            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast">
            </button>
        </div>

        <div class="progress" style="height: 4px;">
            <div id="toastBarra"
                 class="progress-bar bg-light"
                 style="width: 100%">
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let barra = document.getElementById("toastBarra");
        let largura = 100;

        let intervalo = setInterval(function() {
            largura -= 1;
            barra.style.width = largura + "%";
            if (largura <= 0) clearInterval(intervalo);
        }, 30);

        setTimeout(function() {
            let toast = document.getElementById("toastSucesso");
            if (toast) toast.remove();
        }, 3000);
    });
</script>

<?php unset($_SESSION['msg_sucesso']); ?>
<?php endif; ?>



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

<script>
document.addEventListener("DOMContentLoaded", function() {

    let mensagem = sessionStorage.getItem("msg_sucesso");

    if (mensagem) {

        let toastHtml = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show text-bg-success border-0">
                <div class="d-flex">
                    <div class="toast-body">${mensagem}</div>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML("beforeend", toastHtml);

        setTimeout(() => {
            document.querySelector(".toast")?.remove();
        }, 3000);

        sessionStorage.removeItem("msg_sucesso");
    }

});
</script>