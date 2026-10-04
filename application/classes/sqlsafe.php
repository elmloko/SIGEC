<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Saneo de parametros que se concatenan en SQL armado a mano
 * (filtros/orden/paginacion de las grillas jqwidgets en los controladores ajax).
 */
class SqlSafe {

    /** Valor para usar DENTRO de comillas simples: '...'. Se escapa sin agregar comillas. */
    public static function value($value) {
        $value = (string) $value;
        try {
            return substr(Database::instance()->escape($value), 1, -1);
        } catch (Exception $e) {
            return strtr($value, array(
                '\\' => '\\\\', "\0" => '\\0', "\n" => '\\n', "\r" => '\\r',
                "'" => "\\'", '"' => '\\"', "\x1a" => '\\Z',
            ));
        }
    }

    /** Nombre de columna (opcionalmente alias.columna); si no es valido se usa 1 para no romper el SQL. */
    public static function field($field) {
        $field = (string) $field;
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/', $field) ? $field : '1';
    }

    /** Entero no negativo (paginacion). */
    public static function int($value) {
        return max(0, (int) $value);
    }

}
