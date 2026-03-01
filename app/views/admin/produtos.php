<h3>Cadastro de Produtos</h3>

<form method="POST"
      action="/espetinhov5/public/admin/salvarProduto"
      enctype="multipart/form-data"
      class="mb-4">

    <div class="mb-2">
        <label>Nome</label>
        <input type="text" name="nome" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Preço</label>
        <input type="number" step="0.01" name="preco" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Grupo</label>
        <select name="grupo_id" class="form-select" required>
            <?php foreach ($grupos as $grupo) : ?>
                <option value="<?= $grupo['id'] ?>">
                    <?= $grupo['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-2">
        <label>Imagem</label>
        <input type="file" name="imagem" class="form-control">
    </div>

    <button class="btn btn-success">Salvar Produto</button>
</form>

<hr>

<h4>Produtos Cadastrados</h4>

<table class="table table-bordered">
    <tr>
        <th>Imagem</th>
        <th>Nome</th>
        <th>Preço</th>
        <th>Grupo</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>

<?php foreach ($produtos as $produto) : ?>
<tr>

    <td>
        <?php if ($produto['imagem']) : ?>
            <img src="/espetinhov5/public/uploads/<?= $produto['imagem'] ?>"
                 width="60">
        <?php endif; ?>
    </td>

    <td><?= $produto['nome'] ?></td>

    <td>R$ <?= number_format($produto['preco'],2,',','.') ?></td>

    <td><?= $produto['grupo_nome'] ?></td>

    <td>
        <?php if ($produto['ativo']) : ?>
            <span class="badge bg-success">Ativo</span>
        <?php else : ?>
            <span class="badge bg-danger">Inativo</span>
        <?php endif; ?>
    </td>

    <td>

<a href="/espetinhov5/public/admin/editarProduto/<?= $produto['id'] ?>"
   class="btn btn-sm btn-warning">
   Editar
</a>

        <a href="/espetinhov5/public/admin/toggleProduto/<?= $produto['id'] ?>"
           class="btn btn-sm btn-secondary">
           Ativar/Desativar
        </a>


    </td>

</tr>
<?php endforeach; ?>
</table>

<a href="/espetinhov5/public/dashboard"
   class="btn btn-secondary mt-3">
   Voltar
</a>