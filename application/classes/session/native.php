<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sesion nativa de PHP con la duracion que indica config/session.php.
 *
 * El driver de Kohana sincroniza la COOKIE con ese tiempo (30 horas), pero no toca
 * session.gc_maxlifetime, que en este servidor son 1440 segundos (24 minutos). Resultado:
 * el navegador segui­a mandando la cookie, pero PHP ya habia borrado el archivo de la sesion,
 * asi que el usuario aparecia como no identificado y volvia al login "sin razon".
 * Aqui se ajusta la vida del archivo al mismo tiempo antes de iniciar la sesion.
 */
class Session_Native extends Kohana_Session_Native {

    protected function _read($id = NULL)
    {
        if ($this->_lifetime > 0 AND session_status() !== PHP_SESSION_ACTIVE) {
            $actual = (int) ini_get('session.gc_maxlifetime');
            if ($actual < $this->_lifetime) {
                ini_set('session.gc_maxlifetime', (int) $this->_lifetime);
            }
        }
        return parent::_read($id);
    }

}
