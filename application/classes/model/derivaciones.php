<?php

defined('SYSPATH') or die('Acceso denegado');

/**
 * Estados de un paso del seguimiento. (El listado de lo derivado se unio con document/seguimientos:
 * ver Model_Hojasruta::sqlMisHojas.)
 */
class Model_Derivaciones extends Model {

    // estados de la tabla `estados`
    public static $estados = array(
        1 => 'No recibido',
        2 => 'Pendiente',
        4 => 'Derivado',
        6 => 'Agrupado',
        10 => 'Archivado',
        11 => 'Anulado',
    );

}
