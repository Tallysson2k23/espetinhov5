<h3>Gerenciar Grupos</h3>

<form method="POST"
      action="/espetinhov5/public/admin/salvarGrupo"
      class="mb-4">

    <div class="mb-2">
        <label>Nome do Grupo</label>
        <input type="text" name="nome"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Impressora</label>
        <select name="impressora" class="form-select" required>
            <option value="1">1 - Cozinha Espetos</option>
            <option value="2">2 - Cozinha Porções</option>
            <option value="3">3 - Bebidas</option>
            <option value="4">4 - Sucos</option>
        </select>
    </div>

    <button class="btn btn-success">
        Criar Grupo
    </button>

</form>

<hr>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Impressora</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($grupos as $grupo) : ?>
    <tr>
        <td><?= $grupo['id'] ?></td>
        <td><?= $grupo['nome'] ?></td>
        <td><?= $grupo['impressora'] ?></td>

        <td>
            <?php if ($grupo['ativo']) : ?>
                <span class="badge bg-success">Ativo</span>
            <?php else : ?>
                <span class="badge bg-danger">Inativo</span>
            <?php endif; ?>
        </td>

        <td>
            <a href="/espetinhov5/public/admin/toggleGrupo/<?= $grupo['id'] ?>"
               class="btn btn-sm <?= $grupo['ativo'] ? 'btn-secondary' : 'btn-success' ?>">
               <?= $grupo['ativo'] ? 'Desativar' : 'Ativar' ?>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

<a href="/espetinhov5/public/dashboard"
   class="btn btn-secondary mt-3">
   Voltar
</a>