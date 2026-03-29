<?php

$ip = "192.168.68.20"; // teste uma impressora
$porta = 9100;

$socket = fsockopen($ip, $porta, $errno, $errstr, 3);

if (!$socket) {
    echo "ERRO CONEXAO";
    exit;
}

// COMANDO MAIS SIMPLES POSSÍVEL
$dados = "TESTE IMPRESSAO\n\n\n\n";

// CORTE
$dados .= "\x1D\x56\x41\x10";

fwrite($socket, $dados);
fclose($socket);

echo "ENVIADO";