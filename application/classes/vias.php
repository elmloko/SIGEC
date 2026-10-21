<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * VIA de un documento (hasta Vias::MAXIMO).
 *
 * El primer VIA se guarda en documentos.nombre_via / cargo_via, como siempre, para que las pantallas,
 * busquedas y documentos antiguos sigan funcionando. Los VIA 2 en adelante se guardan en documentos_vias.
 */
class Vias {

    const MAXIMO = 3;

    /**
     * Lista de VIA del documento: array(array('nombre' => .., 'cargo' => ..), ...), sin vacios.
     */
    public static function de($documento)
    {
        $vias = array();
        if (trim($documento->nombre_via) != '') {
            $vias[] = array('nombre' => $documento->nombre_via, 'cargo' => $documento->cargo_via);
        }
        if ($documento->id) {
            $extra = DB::query(Database::SELECT, 'SELECT nombre, cargo FROM documentos_vias
                    WHERE id_documento = :id ORDER BY orden')
                    ->param(':id', (int) $documento->id)
                    ->execute()->as_array();
            foreach ($extra as $v) {
                $vias[] = array('nombre' => $v['nombre'], 'cargo' => $v['cargo']);
            }
        }
        return $vias;
    }

    /**
     * Lee del formulario los VIA (via / cargovia, via2 / cargovia2, ...) sin los vacios.
     */
    public static function del_post($post)
    {
        $vias = array();
        for ($i = 1; $i <= self::MAXIMO; $i++) {
            $sufijo = $i == 1 ? '' : $i;
            $nombre = trim(Arr::get($post, 'via' . $sufijo, ''));
            if ($nombre != '') {
                $vias[] = array('nombre' => $nombre, 'cargo' => trim(Arr::get($post, 'cargovia' . $sufijo, '')));
            }
        }
        return $vias;
    }

    /**
     * Guarda el documento con sus VIA: el primero en sus columnas y el resto en documentos_vias.
     */
    public static function guardar($documento, $vias)
    {
        $vias = array_slice($vias, 0, self::MAXIMO);
        $primero = $vias ? array_shift($vias) : array('nombre' => '', 'cargo' => '');
        $documento->nombre_via = $primero['nombre'];
        $documento->cargo_via = $primero['cargo'];
        $documento->save();
        if (!$documento->id) {
            return;
        }

        DB::delete('documentos_vias')->where('id_documento', '=', (int) $documento->id)->execute();
        $orden = 2;
        foreach ($vias as $v) {
            DB::insert('documentos_vias', array('id_documento', 'orden', 'nombre', 'cargo'))
                    ->values(array((int) $documento->id, $orden++, $v['nombre'], $v['cargo']))
                    ->execute();
        }
    }

}
