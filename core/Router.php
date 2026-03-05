<?php

class Router {

    public function run() {

$url = isset($_GET['url']) ? explode('/', $_GET['url']) : ['auth', 'login'];

if ($url[0] == 'api') {

    require_once __DIR__ . '/../app/controllers/PedidoController.php';
    $controller = new PedidoController();

    // /api/produtos/{grupo}
    if ($url[1] == 'produtos') {
        $controller->apiProdutos($url[2]);
        return;
    }

    // /api/mesas
    if ($url[1] == 'mesas') {
        $controller->mesas();
        return;
    }

}

if ($url[0] == 'pedido' && $url[1] == 'salvar') {

    require_once __DIR__ . '/../app/controllers/PedidoController.php';
    $controller = new PedidoController();
    $controller->salvar();
    return;
}

if ($url[0] == 'pedido' && $url[1] == 'fechar') {

    require_once __DIR__ . '/../app/controllers/PedidoController.php';
    $controller = new PedidoController();
    $controller->fechar();
    return;
}

if ($url[0] == 'pedido' && $url[1] == 'transferir') {

    require_once __DIR__ . '/../app/controllers/PedidoController.php';

    $controller = new PedidoController();

    $controller->transferirItens();

    return;
}


if ($url[0] == 'pedido' && $url[1] == 'total') {

    require_once __DIR__ . '/../app/controllers/PedidoController.php';

    $controller = new PedidoController();

    $controller->totalMesa($url[2]);

    return;
}

        $controllerName = ucfirst($url[0]) . 'Controller';
        $method = $url[1] ?? 'index';

        $controllerPath = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerPath)) {
            die("Controller não encontrado");
        }

        require_once $controllerPath;

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            die("Método não encontrado");
        }

        call_user_func_array([$controller, $method], array_slice($url, 2));
    }
}