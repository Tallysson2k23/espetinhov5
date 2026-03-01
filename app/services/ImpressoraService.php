<?php

class ImpressoraService {

    public static function imprimir($ip, $porta, $conteudo) {

        $socket = @fsockopen($ip, $porta, $errno, $errstr, 1);

        if (!$socket) {
            return false;
        }

        fwrite($socket, $conteudo);

        // Corte de papel ESC/POS
        fwrite($socket, "\x1D\x56\x41\x10");

        fclose($socket);

        return true;
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