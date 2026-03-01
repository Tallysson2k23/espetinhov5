<h3>Configuração de Impressoras</h3>

<table class="table table-bordered text-center">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>IP</th>
        <th>Porta</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($impressoras as $imp) : ?>
    <tr id="row-<?= $imp['id'] ?>">
        <form method="POST"
              action="/espetinhov5/public/admin/salvarImpressora">

            <td><?= $imp['id'] ?></td>

            <td>
                <input type="text"
                       name="nome"
                       value="<?= $imp['nome'] ?>"
                       class="form-control">
            </td>

            <td>
                <input type="text"
                       name="ip"
                       value="<?= $imp['ip'] ?>"
                       class="form-control">
            </td>

            <td>
                <input type="number"
                       name="porta"
                       value="<?= $imp['porta'] ?>"
                       class="form-control">
            </td>

            <td>
                <span id="status-<?= $imp['id'] ?>"
                      class="badge bg-secondary">
                    Não testado
                </span>
            </td>

            <td>
                <input type="hidden"
                       name="id"
                       value="<?= $imp['id'] ?>">

                <button class="btn btn-success btn-sm mb-1">
                    Salvar
                </button>

                <button type="button"
                        class="btn btn-primary btn-sm mb-1"
                        onclick="testarImpressora(<?= $imp['id'] ?>)">
                    Testar
                </button>

                <a href="/espetinhov5/public/admin/imprimirTeste/<?= $imp['id'] ?>"
                   class="btn btn-dark btn-sm">
                   Imprimir Teste
                </a>
            </td>

        </form>
    </tr>
    <?php endforeach; ?>

</table>

<a href="/espetinhov5/public/dashboard"
   class="btn btn-secondary mt-3">
   Voltar
</a>

<script>
function testarImpressora(id) {

    fetch("/espetinhov5/public/admin/testarImpressora/" + id)
        .then(res => res.json())
        .then(data => {

            let status = document.getElementById("status-" + id);

            if (data.status === "online") {
                status.className = "badge bg-success";
                status.innerText = "Online";
            } else {
                status.className = "badge bg-danger";
                status.innerText = "Offline";
            }

        });
}
</script>