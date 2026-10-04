<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * IP real del visitante, tambien cuando SIGEC esta detras de un proxy o balanceador (ver config/proxy.php).
 */
class IpCliente {

    /** IP del visitante. */
    public static function obtener() {
        $remota = (string) Arr::get($_SERVER, 'REMOTE_ADDR', '');
        if (!self::es_proxy($remota)) {
            return $remota;
        }
        // X-Forwarded-For: "cliente, proxy1, proxy2". Se recorre desde el final (lo agrego el proxy mas
        // cercano) y se toma la primera IP que no sea de un proxy de confianza: las de mas a la izquierda
        // las pudo escribir el propio visitante.
        $cadena = array_reverse(array_map('trim', explode(',', (string) Arr::get($_SERVER, 'HTTP_X_FORWARDED_FOR', ''))));
        foreach ($cadena as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                break;
            }
            if (!self::es_proxy($ip)) {
                return $ip;
            }
        }
        return $remota;
    }

    /** La IP es la de un proxy de confianza (no la de un visitante). */
    public static function es_proxy($ip) {
        foreach ((array) Kohana::$config->load('proxy')->get('confiables', array()) as $red) {
            if (self::en_red($ip, $red)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    protected static function en_red($ip, $red) {
        if (strpos($red, '/') === FALSE) {
            return $ip === $red;
        }
        list($base, $bits) = explode('/', $red, 2);
        $ip_bin = @inet_pton($ip);
        $base_bin = @inet_pton($base);
        if ($ip_bin === FALSE || $base_bin === FALSE || strlen($ip_bin) !== strlen($base_bin)) {
            return FALSE;
        }
        $bits = (int) $bits;
        $bytes = intval($bits / 8);
        if (substr($ip_bin, 0, $bytes) !== substr($base_bin, 0, $bytes)) {
            return FALSE;
        }
        $resto = $bits % 8;
        if ($resto === 0) {
            return TRUE;
        }
        $mascara = chr((0xFF << (8 - $resto)) & 0xFF);
        return (substr($ip_bin, $bytes, 1) & $mascara) === (substr($base_bin, $bytes, 1) & $mascara);
    }

}
