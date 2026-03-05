<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Espetinho Central - V5</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="/espetinhov5/public/espetinho_central.ico">

    <!-- Bootstrap (mantido global) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS BASE (sempre carrega) -->
    <link rel="stylesheet" href="/espetinhov5/public/assets/css/base.css">

    <?php
        $currentUrl = $_SERVER['REQUEST_URI'];

        // DASHBOARD
        if (strpos($currentUrl, '/dashboard') !== false) {
            echo '<link rel="stylesheet" href="/espetinhov5/public/assets/css/dashboard.css">';
        }

        // ADMIN (produtos, usuários, grupos, etc)
        if (strpos($currentUrl, '/admin') !== false) {
            echo '<link rel="stylesheet" href="/espetinhov5/public/assets/css/admin.css">';
        }

        // PDV / PEDIDO
        if (strpos($currentUrl, '/pedido') !== false) {
            echo '<link rel="stylesheet" href="/espetinhov5/public/assets/css/pdv.css">';
        }

        // AUTH (login)
        if (strpos($currentUrl, '/auth') !== false) {
            echo '<link rel="stylesheet" href="/espetinhov5/public/assets/css/auth.css">';
        }

        $isPedido = strpos($currentUrl, '/pedido') !== false;
    ?>


<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>



</head>
<body>

<?php if (!$isPedido): ?>
<div class="dashboard-container">
<?php endif; ?>