<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sesion nativa de PHP con la duracion que indica config/session.php.
 *
 * El driver de Kohana sincroniza la COOKIE con ese tiempo ('lifetime'), pero no toca
 * session.gc_maxlifetime, que en este servidor son 1440 segundos (24 minutos). Resultado:
 * el navegador segui­a mandando la cookie, pero PHP ya habia borrado el archivo de la sesion,
 * asi que el usuario aparecia como no identificado y volvia al login "sin razon".
 * Aqui se ajusta la vida del archivo al mismo tiempo antes de iniciar la sesion.
 *
 * Ademas, 'inactividad': si pasa ese tiempo sin ninguna peticion, la sesion se vacia
 * (el usuario vuelve al login). Kohana guarda la hora de la ultima peticion en 'last_active'.
 */
class Session_Native extends Kohana_Session_Native {

    protected $_inactividad = 0;

    public function __construct(array $config = NULL, $id = NULL)
    {
        if (isset($config['inactividad'])) {
            $this->_inactividad = (int) $config['inactividad'];
        }
        parent::__construct($config, $id);
    }

    protected function _read($id = NULL)
    {
        if ($this->_lifetime > 0 AND session_status() !== PHP_SESSION_ACTIVE) {
            $actual = (int) ini_get('session.gc_maxlifetime');
            if ($actual < $this->_lifetime) {
                ini_set('session.gc_maxlifetime', (int) $this->_lifetime);
            }
        }
        $resultado = parent::_read($id);

        // sesion abandonada: se vacia y se le da un id nuevo
        if ($this->_inactividad > 0 AND isset($_SESSION['last_active'])
                AND time() - (int) $_SESSION['last_active'] > $this->_inactividad) {
            $_SESSION = array();
            session_regenerate_id(TRUE);
        }

        return $resultado;
    }

}
