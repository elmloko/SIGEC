<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Proteccion CSRF por origen: todo envio POST debe venir de una pagina del propio SIGEC.
 *
 * El navegador agrega a cada POST la cabecera Origin (y Referer) con el sitio desde el que se envia,
 * y una pagina ajena no puede falsificarlas. Si alguna de las dos indica otro sitio, se rechaza.
 * Si no viene ninguna (clientes que no son navegadores, como integraciones servidor a servidor)
 * se deja pasar: CSRF es un ataque que se hace a traves del navegador de la victima.
 *
 * Asi no hace falta un token en cada formulario ni en cada llamada AJAX.
 */
class Csrf {

    /**
     * Paginas publicas sin sesion: un envio desde otro sitio no puede abusar de la sesion de nadie.
     * (externo/guardarRespuestaReclamo NO va aqui: la usa el personal con sesion iniciada)
     */
    protected static $publicas = array(
        'externo/index', 'externo/busqueda', 'externo/buscarhojaderuta', 'externo/seguimientoexterno',
        'externo/guardarreclamo', 'externo/getseguimientoreclamo', 'externo/getobservacionaeditar', 'externo/editarreclamo',
    );

    public static function verificar()
    {
        if (strtoupper(Arr::get($_SERVER, 'REQUEST_METHOD', 'GET')) !== 'POST' || PHP_SAPI === 'cli') {
            return;
        }
        if (in_array(self::ruta(), self::$publicas, TRUE)) {
            return;
        }

        $origen = Arr::get($_SERVER, 'HTTP_ORIGIN');
        $referer = Arr::get($_SERVER, 'HTTP_REFERER');

        if ($origen !== NULL && $origen !== '') {
            $ok = self::es_propio($origen);
        } elseif ($referer !== NULL && $referer !== '') {
            $ok = self::es_propio($referer);
        } else {
            $ok = TRUE;
        }

        if (!$ok) {
            $bloquear = Kohana::$config->load('csrf')->get('bloquear', TRUE);
            Kohana::$log->add(Log::WARNING, 'CSRF: POST ' . ($bloquear ? 'rechazado' : 'de otro sitio (solo registrado)')
                . ' a ' . Arr::get($_SERVER, 'REQUEST_URI') . ' desde ' . ($origen ? $origen : $referer));
            if (!$bloquear) {
                return;
            }
            header('HTTP/1.1 403 Forbidden');
            header('Content-Type: text/html; charset=utf-8');
            echo '<p style="font-family:sans-serif">Solicitud rechazada: el formulario no se envió desde SIGEC. '
                . 'Vuelva a la página e inténtelo otra vez.</p>';
            exit;
        }
    }

    /** "controlador/accion" de la peticion actual, en minusculas. */
    protected static function ruta()
    {
        $partes = explode('/', trim((string) parse_url(Arr::get($_SERVER, 'REQUEST_URI', ''), PHP_URL_PATH), '/'));
        $controlador = strtolower(Arr::get($partes, 0, ''));
        $accion = strtolower(Arr::get($partes, 1, 'index'));
        return $controlador . '/' . $accion;
    }

    /** La URL (Origin o Referer) es de este mismo sitio: mismo host y puerto. */
    protected static function es_propio($url)
    {
        if ($url === 'null') {
            return FALSE;
        }
        $p = parse_url($url);
        if (empty($p['host'])) {
            return FALSE;
        }
        $de = strtolower($p['host']) . (isset($p['port']) ? ':' . $p['port'] : '');
        $hosts = array_merge(
            explode(',', (string) Arr::get($_SERVER, 'HTTP_HOST', '')),
            explode(',', (string) Arr::get($_SERVER, 'HTTP_X_FORWARDED_HOST', '')),
            (array) Kohana::$config->load('csrf')->get('hosts_extra', array())
        );
        foreach ($hosts as $host) {
            $host = strtolower(trim($host));
            if ($host === '') {
                continue;
            }
            // el puerto por defecto puede venir o no en una de las dos cabeceras
            if ($de === $host || preg_replace('/:(80|443)$/', '', $de) === preg_replace('/:(80|443)$/', '', $host)) {
                return TRUE;
            }
        }
        return FALSE;
    }

}
