<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Proteccion CSRF por origen (ver classes/csrf.php).
 */
return array(
    // TRUE: rechaza los POST que vienen de otro sitio. FALSE: solo los anota en el log (application/logs)
    'bloquear' => TRUE,
    // dominios adicionales con los que se abre SIGEC, si el servidor esta detras de un proxy que cambia el host
    // ej: array('sigec.correos.gob.bo', '172.65.10.50:8130')
    'hosts_extra' => array(),
);
