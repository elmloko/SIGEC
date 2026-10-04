<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Proxies / balanceadores de confianza delante de SIGEC.
 *
 * Si la conexion llega desde una de estas direcciones, la IP real del visitante se toma de la cabecera
 * X-Forwarded-For (que pone el proxy). Si llega desde cualquier otra, esa cabecera se ignora, porque
 * el propio visitante podria escribirla para hacerse pasar por otra IP.
 *
 * Por defecto: la propia maquina y las redes privadas (donde suele estar el proxy de la institucion).
 * Si se conoce la IP exacta del proxy de produccion, conviene dejar solo esa (ej. '10.0.0.5').
 */
return array(
    'confiables' => array(
        '127.0.0.1/32',
        '::1',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
    ),
);
