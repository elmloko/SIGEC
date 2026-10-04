<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Limite de intentos fallidos de ingreso (contra la prueba masiva de contraseñas).
 *
 * - 5 fallos del mismo usuario desde la misma IP en 15 minutos: ese par queda bloqueado 15 minutos.
 * - 30 fallos desde una misma IP (con cualquier usuario) en 15 minutos: la IP queda bloqueada 15 minutos.
 *
 * Los contadores se guardan en archivos dentro de application/cache/login_intentos (no hace falta tabla).
 * Un ingreso correcto borra el contador de ese usuario.
 */
class LoginLimite {

    const VENTANA = 900;          // segundos
    const MAX_USUARIO = 5;
    const MAX_IP = 30;

    /** Segundos que faltan para poder intentar de nuevo (0 = puede intentar). */
    public static function espera($username) {
        $ahora = time();
        $espera = 0;
        foreach (self::claves($username) as $clave => $max) {
            $d = self::leer($clave);
            if ($d['hasta'] > $ahora) {
                $espera = max($espera, $d['hasta'] - $ahora);
            }
        }
        return $espera;
    }

    public static function fallo($username) {
        $ahora = time();
        foreach (self::claves($username) as $clave => $max) {
            $d = self::leer($clave);
            if ($ahora - $d['desde'] > self::VENTANA) {
                $d = array('fallos' => 0, 'desde' => $ahora, 'hasta' => 0);
            }
            $d['fallos']++;
            if ($d['fallos'] >= $max) {
                $d['hasta'] = $ahora + self::VENTANA;
                Kohana::$log->add(Log::WARNING, 'Login: bloqueo temporal por intentos fallidos (' . $clave . ')');
            }
            self::escribir($clave, $d);
        }
    }

    public static function exito($username) {
        $claves = array_keys(self::claves($username));
        @unlink(self::archivo($claves[0]));
    }

    protected static function claves($username) {
        $ip = (string) Arr::get($_SERVER, 'REMOTE_ADDR', '');
        return array(
            'u|' . strtolower(trim((string) $username)) . '|' . $ip => self::MAX_USUARIO,
            'ip|' . $ip => self::MAX_IP,
        );
    }

    protected static function archivo($clave) {
        return APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'login_intentos' . DIRECTORY_SEPARATOR . sha1($clave) . '.json';
    }

    protected static function leer($clave) {
        $vacio = array('fallos' => 0, 'desde' => time(), 'hasta' => 0);
        $f = self::archivo($clave);
        if (!is_file($f)) {
            return $vacio;
        }
        $d = json_decode((string) @file_get_contents($f), TRUE);
        return is_array($d) ? array_merge($vacio, $d) : $vacio;
    }

    protected static function escribir($clave, array $d) {
        $f = self::archivo($clave);
        if (!is_dir(dirname($f))) {
            @mkdir(dirname($f), 0770, TRUE);
        }
        @file_put_contents($f, json_encode($d), LOCK_EX);
    }

}
