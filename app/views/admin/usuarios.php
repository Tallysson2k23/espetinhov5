<h3>Gerenciar Usuários</h3>

<form method="POST"
      action="/espetinhov5/public/admin/salvarUsuario"
      class="mb-4">

    <div class="mb-2">
        <label>Nome</label>
        <input type="text" name="nome"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Usuário</label>
        <input type="text" name="usuario"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Senha</label>
        <input type="password" name="senha"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Nível</label>
        <select name="nivel" class="form-select">
            <option value="garcom">Garçom</option>
            <option value="admin">Admin</option>
        </select>
    </div>

    <button class="btn btn-success">
        Criar Usuário
    </button>

</form>

<hr>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Usuário</th>
        <th>Nível</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($usuarios as $user) : ?>
    <tr>
        <td><?= $user['id'] ?></td>
        <td><?= $user['nome'] ?></td>
        <td><?= $user['usuario'] ?></td>
        <td><?= $user['nivel'] ?></td>

        <td>
            <?php if ($user['ativo']) : ?>
                <span class="badge bg-success">Ativo</span>
            <?php else : ?>
                <span class="badge bg-danger">Inativo</span>
            <?php endif; ?>
        </td>

        <td>

            <a href="/espetinhov5/public/admin/toggleUsuario/<?= $user['id'] ?>"
               class="btn btn-sm <?= $user['ativo'] ? 'btn-secondary' : 'btn-success' ?>">
               <?= $user['ativo'] ? 'Desativar' : 'Ativar' ?>
            </a>

        </td>
    </tr>
    <?php endforeach; ?>

</table>

<a href="/espetinhov5/public/dashboard"
   class="btn btn-secondary mt-3">
   Voltar
</a>