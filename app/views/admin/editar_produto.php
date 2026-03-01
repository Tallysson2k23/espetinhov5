<h3>Editar Produto</h3>

<form method="POST"
      action="/espetinhov5/public/admin/atualizarProduto/<?= $produto['id'] ?>"
      enctype="multipart/form-data">

    <div class="mb-2">
        <label>Nome</label>
        <input type="text" name="nome"
               value="<?= $produto['nome'] ?>"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Preço</label>
        <input type="number" step="0.01"
               name="preco"
               value="<?= $produto['preco'] ?>"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Grupo</label>
        <select name="grupo_id" class="form-select">

            <?php foreach ($grupos as $grupo) : ?>
                <option value="<?= $grupo['id'] ?>"
                    <?= $grupo['id'] == $produto['grupo_id'] ? 'selected' : '' ?>>
                    <?= $grupo['nome'] ?>
                </option>
            <?php endforeach; ?>

        </select>
    </div>

    <button class="btn btn-success">Salvar Alterações</button>

</form>