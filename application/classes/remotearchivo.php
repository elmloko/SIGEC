<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Acceso a los adjuntos reales de producción vía SFTP, usado solo cuando la app
 * corre en Windows (entorno local de desarrollo), donde el disco real
 * (/backup/backup_sigec/sigec/archivo) no está montado localmente.
 * En Linux (producción) esta clase no se usa: se sigue leyendo el disco local.
 */
class RemoteArchivo {

    private static $sftp;

    public static function is_enabled() {
        if (DIRECTORY_SEPARATOR !== '\\') {
            return FALSE;
        }
        $config = Kohana::$config->load('archivo')->get('remote');
        return !empty($config['enabled']);
    }

    private static function connect() {
        if (self::$sftp instanceof \phpseclib\Net\SFTP) {
            return self::$sftp;
        }

        require_once DOCROOT . 'vendor/autoload.php';

        $config = Kohana::$config->load('archivo')->get('remote');
        $sftp = new \phpseclib\Net\SFTP($config['host'], $config['port']);

        if (!$sftp->login($config['user'], $config['password'])) {
            throw new Kohana_Exception('No se pudo conectar por SFTP a :host', array(':host' => $config['host']));
        }

        return self::$sftp = $sftp;
    }

    private static function remote_path($relative_path) {
        $config = Kohana::$config->load('archivo')->get('remote');
        return rtrim($config['remote_path'], '/') . '/' . ltrim(str_replace('\\', '/', $relative_path), '/');
    }

    public static function exists($relative_path) {
        return (bool) self::connect()->stat(self::remote_path($relative_path));
    }

    public static function filesize($relative_path) {
        $stat = self::connect()->stat(self::remote_path($relative_path));
        return $stat ? $stat['size'] : FALSE;
    }

    /**
     * Envía el archivo remoto directamente a la salida (streaming), sin
     * cargarlo completo en memoria.
     */
    public static function stream_download($relative_path) {
        $out = fopen('php://output', 'wb');
        self::connect()->get(self::remote_path($relative_path), $out);
        fclose($out);
    }

    /**
     * Sube un archivo local temporal (ej. $_FILES[...]['tmp_name']) al
     * directorio remoto indicado, creando las carpetas que falten.
     */
    public static function upload($local_tmp_path, $relative_path) {
        $sftp = self::connect();
        $remote_full = self::remote_path($relative_path);
        $remote_dir = dirname($remote_full);

        if (!$sftp->is_dir($remote_dir)) {
            $sftp->mkdir($remote_dir, -1, TRUE);
        }

        return $sftp->put($remote_full, $local_tmp_path, \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE);
    }

}
