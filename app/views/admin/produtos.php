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

<table class="table">
    <tr>
        <th>Imagem</th>
        <th>Nome</th>
        <th>Preço</th>
        <th>Grupo</th>
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
        </tr>
    <?php endforeach; ?>
</table>

<a href="/espetinhov5/public/dashboard"
   class="btn btn-secondary mt-3">
   Voltar
</a>