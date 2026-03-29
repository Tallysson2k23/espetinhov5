<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../models/FilaImpressao.php';

class ImpressaoController extends Controller
{
    public function index()
    {
        $filaModel = new FilaImpressao();

        $dados['fila'] = $filaModel->listarRecentes();

        $this->view('impressao/index', $dados);
    }

public function reimprimir($id)
{
    require_once __DIR__ . '/../services/ImpressoraService.php';

    $model = new FilaImpressao();

    $item = $model->buscarPorId($id);

    if (!$item) {
        echo "Registro não encontrado";
        return;
    }

    // marca como pendente novamente
    $model->atualizarStatus($id, 'pendente');

    header("Location: /espetinhov5/public/impressao");
}





}