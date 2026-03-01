<h2 class="mb-4">Login - Espetinho Central V5</h2>

<?php if (!empty($erro)) : ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label>Usuário</label>
        <input type="text" name="usuario" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Senha</label>
        <input type="password" name="senha" class="form-control" required>
    </div>

    <button class="btn btn-primary w-100">Entrar</button>

</form>