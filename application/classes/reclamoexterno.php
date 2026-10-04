<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Reclamos de la consulta publica (sin login): el ciudadano solo puede ver los datos de contacto
 * y editar los reclamos que registro el mismo, en esta sesion.
 */
class ReclamoExterno {

    const SESION = 'reclamos_externos_propios';

    public static function propios() {
        return (array) Session::instance()->get(self::SESION, array());
    }

    public static function agregar($id_observacion) {
        $id_observacion = (int) $id_observacion;
        if ($id_observacion <= 0) {
            return;
        }
        $propios = self::propios();
        $propios[] = $id_observacion;
        Session::instance()->set(self::SESION, array_values(array_unique($propios)));
    }

    public static function es_propio($id_observacion) {
        return in_array((int) $id_observacion, self::propios(), TRUE);
    }

}
