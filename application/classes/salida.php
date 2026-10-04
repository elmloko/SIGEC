<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Escapado de datos que se mandan al navegador como HTML
 * (las grillas jqwidgets muestran el contenido de cada celda como HTML, sin escapar).
 */
class Salida {

    /**
     * Escapa todos los textos de las filas. En las columnas de $con_negritas se vuelven a permitir
     * solo <b> y </b> (la bitacora las guarda para resaltar).
     */
    public static function filas(array $filas, array $con_negritas = array()) {
        foreach ($filas as $i => $fila) {
            foreach ($fila as $campo => $valor) {
                if (!is_string($valor)) {
                    continue;
                }
                $valor = HTML::chars($valor);
                if (in_array($campo, $con_negritas, TRUE)) {
                    $valor = preg_replace('#&lt;(/?)b&gt;#i', '<$1b>', $valor);
                }
                $filas[$i][$campo] = $valor;
            }
        }
        return $filas;
    }

}
