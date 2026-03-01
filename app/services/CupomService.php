<?php

class CupomService {

    public static function gerar(
        $titulo,
        $mesa,
        $atendimento,
        $garcom,
        $itens
    ) {

        $linha = str_repeat("-", 48) . "\n";

        $texto  = "\x1B\x61\x01"; // centralizar
        $texto .= strtoupper($titulo) . "\n";
        $texto .= "\x1B\x61\x00"; // esquerda

        $texto .= $linha;
        $texto .= "Mesa: $mesa\n";
        $texto .= "Atendimento: $atendimento\n";
        $texto .= $linha;

        foreach ($itens as $item) {
            $texto .= $item['quantidade'] . "x "
                   . strtoupper($item['nome']) . "\n";
        }

        $texto .= $linha;
        $texto .= "Atendente: " . strtoupper($garcom) . "\n";
        $texto .= "Data/Hora: " . date("d/m/Y H:i:s") . "\n";
        $texto .= $linha;
        $texto .= "NAO E DOCUMENTO FISCAL\n\n\n";

        return $texto;
    }
}