<?php

class CupomService {

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
        }

        $texto .= $linha;

        $texto .= "Atendente: " . strtoupper($garcom) . "\n";
        $texto .= "Data/Hora: " . date("d/m/Y H:i") . "\n";

        $texto .= $linha;
        $texto .= "NAO E DOCUMENTO FISCAL\n";

        $texto .= "\n\n\n";

        return $texto;
    }
}