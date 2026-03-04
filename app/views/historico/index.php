<link rel="stylesheet" href="/espetinhov5/public/assets/css/historico.css">

<div class="historico-container">

<div class="historico-topo">

<h2 class="historico-titulo">
Histórico de Mesas
</h2>

<a href="/espetinhov5/public/dashboard"
   class="btn-voltar-mesas">
Voltar para Mesas
</a>

</div>
<form method="GET" class="historico-filtro">

<label>Selecionar data:</label>

<input type="date"
       name="data"
       value="<?= $_GET['data'] ?? date('Y-m-d') ?>">

<button type="submit" class="btn-filtrar">
Buscar
</button>

</form>


<div class="mesas-grid">

<?php if (!empty($atendimentos)): ?>

<?php foreach ($atendimentos as $a): ?>

<div class="mesa-card-historico">

<div class="mesa-numero">
Mesa <?= $a['mesa'] ?>
</div>

<div class="mesa-info">

<div>
Atendimento #<?= $a['id'] ?>
</div>

<div>
<?= date('H:i', strtotime($a['created_at'])) ?>
</div>

<div class="mesa-valor">
R$ <?= number_format($a['valor_total'],2,',','.') ?>
</div>

<div class="mesa-pagamento">
<?= $a['forma_pagamento'] ?>
</div>

</div>

<a href="/espetinhov5/public/historico/ver/<?= $a['id'] ?>"
   class="btn-ver-pedido">
Ver Pedido
</a>

</div>

<?php endforeach; ?>

<?php else: ?>

<p>Nenhum atendimento encontrado nesta data.</p>

<?php endif; ?>

</div>

</div>