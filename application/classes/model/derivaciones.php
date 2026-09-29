<?php

defined('SYSPATH') or die('Acceso denegado');

/**
 * Hojas de ruta derivadas por un usuario (seguimiento.derivado_por), con busqueda, filtros y paginacion.
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

    /**
     * Arma el WHERE comun. $f: q (texto), estado, desde (Y-m-d), hasta (Y-m-d).
     * $con_estado = FALSE para los contadores por estado (que ignoran el filtro de estado).
     */
    private function condiciones($id_user, array $f, $con_estado = TRUE) {
        $where = array('s.derivado_por = :id');
        $params = array(':id' => (int) $id_user);
        if ($con_estado && isset($f['estado']) && $f['estado'] !== '') {
            $where[] = 's.estado = :estado';
            $params[':estado'] = (int) $f['estado'];
        }
        if (!empty($f['desde'])) {
            $where[] = 's.fecha_emision >= :desde';
            $params[':desde'] = $f['desde'] . ' 00:00:00';
        }
        if (!empty($f['hasta'])) {
            $where[] = 's.fecha_emision <= :hasta';
            $params[':hasta'] = $f['hasta'] . ' 23:59:59';
        }
        if (!empty($f['q'])) {
            $where[] = '(s.nur LIKE :q OR s.nombre_receptor LIKE :q OR s.cargo_receptor LIKE :q OR s.proveido LIKE :q OR d.referencia LIKE :q OR d.cite_original LIKE :q)';
            $params[':q'] = '%' . $f['q'] . '%';
        }
        return array(implode(' AND ', $where), $params);
    }

    private function ejecutar($sql, array $params) {
        $query = DB::query(Database::SELECT, $sql);
        foreach ($params as $k => $v) {
            $query->param($k, $v);
        }
        return $query->execute();
    }

    public function buscar($id_user, array $f, $pagina = 1, $por_pagina = 25) {
        list($where, $params) = $this->condiciones($id_user, $f);
        $desde = (max(1, (int) $pagina) - 1) * (int) $por_pagina;

        $campos = 's.id, s.nur, s.nombre_receptor, s.cargo_receptor, s.a_oficina, s.proveido,
                   s.fecha_emision, s.fecha_recepcion, s.estado, s.oficial, s.prioridad,
                   MAX(d.referencia) AS referencia, MAX(d.cite_original) AS cite,
                   DATEDIFF(NOW(), s.fecha_emision) AS dias';
        $limite = "LIMIT $desde, " . (int) $por_pagina;

        if (empty($f['q'])) {
            // sin busqueda de texto no hace falta unir documentos para filtrar: se pagina solo seguimiento
            // y se une documentos unicamente para las filas de la pagina (mucho mas rapido con historiales grandes)
            $total = $this->ejecutar("SELECT COUNT(*) AS n FROM seguimiento s WHERE $where", $params)->get('n');
            $filas = $this->ejecutar("SELECT $campos
                    FROM (SELECT * FROM seguimiento s WHERE $where ORDER BY s.fecha_emision DESC, s.id DESC $limite) s
                    LEFT JOIN documentos d ON d.nur = s.nur AND d.original = 1
                    GROUP BY s.id
                    ORDER BY s.fecha_emision DESC, s.id DESC", $params)->as_array();
        } else {
            $total = $this->ejecutar("SELECT COUNT(DISTINCT s.id) AS n
                    FROM seguimiento s LEFT JOIN documentos d ON d.nur = s.nur AND d.original = 1
                    WHERE $where", $params)->get('n');
            $filas = $this->ejecutar("SELECT $campos
                    FROM seguimiento s LEFT JOIN documentos d ON d.nur = s.nur AND d.original = 1
                    WHERE $where
                    GROUP BY s.id
                    ORDER BY s.fecha_emision DESC, s.id DESC
                    $limite", $params)->as_array();
        }

        return array('total' => (int) $total, 'filas' => $filas);
    }

    /** Cantidad por estado con los mismos filtros (menos el de estado), para las pestanas. */
    public function por_estado($id_user, array $f) {
        list($where, $params) = $this->condiciones($id_user, $f, FALSE);
        $join = empty($f['q']) ? '' : 'LEFT JOIN documentos d ON d.nur = s.nur AND d.original = 1';
        return $this->ejecutar("SELECT s.estado, COUNT(DISTINCT s.id) AS n FROM seguimiento s $join
                WHERE $where GROUP BY s.estado", $params)->as_array('estado', 'n');
    }

}
