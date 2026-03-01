<div class="container d-flex justify-content-center align-items-center vh-100">

    <div class="card-login col-12 col-sm-10 col-md-6 col-lg-4 text-center">

        <!-- Logo -->
        <img src="/espetinhov5/public/assets/img/logo.png"
             class="logo-login"
             alt="Espetinho Central">

        <h4 class="mb-4"></h4>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger">
                <?= $erro ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3 text-start">
                <label class="form-label">Usuário</label>
                <input type="text"
                       name="usuario"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Senha</label>
                <input type="password"
                       name="senha"
                       class="form-control"
                       required>
            </div>

            <button class="btn btn-primary w-100">
                Entrar
            </button>

        </form>

    </div>

</div>