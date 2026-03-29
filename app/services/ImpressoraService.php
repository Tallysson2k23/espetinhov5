<?php

class ImpressoraService {

public static function imprimir($ip, $porta, $conteudo) {

    $lockFile = __DIR__ . '/../../storage/lock/impressao.lock';

    $tentativas = 0;

    // ⛔ espera com limite (evita travar infinito)
    while (file_exists($lockFile)) {

        usleep(300000); // 0.3s

        $tentativas++;

        // 🔥 se travar por muito tempo, limpa
        if ($tentativas > 20) {
            unlink($lockFile);
            break;
        }
    }

    // 🔒 cria trava com segurança
    $fp = fopen($lockFile, 'w');

    if (!$fp) {
        return false;
    }

    try {

        // 🔥 conecta
        $socket = @fsockopen($ip, $porta, $errno, $errstr, 2);

        if (!$socket) {
            return false;
        }

        fwrite($socket, $conteudo);

        usleep(700000); // 🔥 aumentei tempo (0.7s)

        fwrite($socket, "\x1D\x56\x41\x10");

        fclose($socket);

        return true;

    } finally {

        fclose($fp);

        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
}

    public static function testarConexao($ip, $porta) {

        $socket = @fsockopen($ip, $porta, $errno, $errstr, 1);

        if ($socket) {
            fclose($socket);
            return true;
        }

        return false;
    }

}