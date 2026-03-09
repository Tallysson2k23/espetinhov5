<?php

class CupomService {


public static function agora()
{
    require_once __DIR__ . '/../../config/database.php';

    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("SELECT NOW()");
    $hora = $stmt->fetchColumn();

    return date("d/m/Y H:i:s", strtotime($hora));
}

    public static function gerar(
        $titulo,
        $mesa,
        $atendimento,
        $garcom,
        $itens
    ) {

        $largura = 48;
        $linha = str_repeat("-", $largura) . "\n";

        $texto  = "\x1B\x40";        // Inicializa impressora
        $texto .= "\x1B\x61\x01";    // Centralizar
        $texto .= strtoupper($titulo) . "\n";
        $texto .= "\x1B\x61\x00";    // Alinhar esquerda

        $texto .= $linha;
        $texto .= "Mesa: " . $mesa . "\n";
        $texto .= "Atendimento: " . $atendimento . "\n";
        $texto .= $linha;

        $texto .= "Qtd  Descricao\n";
        $texto .= $linha;

        foreach ($itens as $item) {

            $qtd = $item['quantidade'];
            $nome = strtoupper($item['nome']);

            // Quebrar nome grande
            if (strlen($nome) > 38) {
                $nome = substr($nome, 0, 38);
            }

        $texto .= str_pad($qtd, 4)
        . " "
        . $nome . "\n";

// imprimir observação se existir
if (!empty($item['observacao'])) {

    $obs = strtoupper($item['observacao']);

    $texto .= "     * " . $obs . "\n";
}
        }

        $texto .= $linha;

        $texto .= "Atendente: " . strtoupper($garcom) . "\n";
        $texto .= "Data/Hora: " . date("d/m/Y H:i") . "\n";

        $texto .= $linha;
        $texto .= "NAO E DOCUMENTO FISCAL\n";

        $texto .= "\n\n\n";

        return $texto;
    }

public static function gerarFechamento(
    $mesa,
    $atendimento,
    $itens,
    $total,
    $forma_pagamento
) {

    $largura = 48;
    $linha = str_repeat("-", $largura) . "\n";

    $texto  = "\x1B\x40";        // Inicializa impressora
    $texto .= "\x1B\x61\x01";    // Centralizar
    $texto .= "COMPROVANTE\n";
    $texto .= "\x1B\x61\x00";    // Alinhar esquerda

    $texto .= $linha;
    $texto .= "Mesa: " . $mesa . "\n";
    $texto .= "Atendimento: " . $atendimento . "\n";
    $texto .= $linha;

    $texto .= "Qtd  Produto              Vl.Unit   Total\n";
    $texto .= $linha;

    foreach ($itens as $item) {

        $qtd = $item['quantidade'];
        $nome = strtoupper($item['nome']);
        $valor = number_format($item['preco_unitario'], 2, ',', '.');
        $subtotal = number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.');

        $texto .= str_pad($qtd, 4);
        $texto .= str_pad(substr($nome, 0, 18), 20);
        $texto .= str_pad($valor, 8, " ", STR_PAD_LEFT);
        $texto .= str_pad($subtotal, 8, " ", STR_PAD_LEFT);
        $texto .= "\n";
    }

    $texto .= $linha;

    // TOTAL CENTRALIZADO EM DESTAQUE
    $texto .= "\x1B\x61\x01"; // centraliza
    $texto .= "\x1B\x45\x01"; // negrito ON
    $texto .= "TOTAL: R$ " . number_format($total, 2, ',', '.') . "\n";
    $texto .= "\x1B\x45\x00"; // negrito OFF
    $texto .= "\x1B\x61\x00"; // volta esquerda

    $texto .= $linha;

    $texto .= "Forma Pagamento: " . strtoupper($forma_pagamento) . "\n\n";

    $texto .= "Obrigado, volte sempre!\n";
    $texto .= "Atendente: " . strtoupper($_SESSION['usuario']) . "\n";
    $texto .= "Cod. Venda: " . $atendimento . "\n\n";

    $texto .= "NAO E DOCUMENTO FISCAL\n";
    $texto .= "OPR: " . strtoupper($_SESSION['usuario']) . "\n";
    $texto .= "Data/Hora: " . date("d/m/Y H:i:s") . "\n";

    $texto .= "\n\n\n";

    return $texto;
}

public static function gerarConferencia(
    $mesa,
    $atendimento,
    $itens,
    $total
) {

    $largura = 48;
    $linha = str_repeat("-", $largura) . "\n";

    $texto  = "\x1B\x40";
    $texto .= "\x1B\x61\x01";
    $texto .= "CONFERENCIA DE MESA\n";
    $texto .= "\x1B\x61\x00";

    $texto .= $linha;
    $texto .= "Mesa: " . $mesa . "\n";
    $texto .= "Atendimento: " . $atendimento . "\n";
    $texto .= $linha;

    $texto .= "Qtd  Produto              Total\n";
    $texto .= $linha;

    foreach ($itens as $item) {

        $qtd = $item['quantidade'];
        $nome = strtoupper($item['nome']);
        $subtotal = number_format(
            $item['quantidade'] * $item['preco_unitario'],
            2,
            ',',
            '.'
        );

        $texto .= str_pad($qtd, 4);
        $texto .= str_pad(substr($nome,0,22), 24);
        $texto .= str_pad($subtotal, 10, " ", STR_PAD_LEFT);
        $texto .= "\n";
    }

    $texto .= $linha;

    $texto .= "\x1B\x61\x01";
    $texto .= "\x1B\x45\x01";
    $texto .= "TOTAL: R$ " . number_format($total,2,',','.') . "\n";
    $texto .= "\x1B\x45\x00";
    $texto .= "\x1B\x61\x00";

    $texto .= $linha;
    $texto .= "CONFERENCIA - NAO FISCAL\n";
    $texto .= "Data/Hora: " . self::agora() . "\n";

    $texto .= "\n\n\n";

    return $texto;
}



}