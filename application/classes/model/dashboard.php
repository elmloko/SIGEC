<?php

defined('SYSPATH') or die('Acceso denegado');

/**
 * Consultas del panel de inicio (dashboard) de un usuario.
 */
class Model_Dashboard extends Model {

    /**
     * Actividad mensual de los ultimos $meses meses (incluido el actual):
     * documentos generados, correspondencia recibida y correspondencia derivada.
     * Devuelve array('meses' => array('2026-09', ...), 'generados' => array(n, ...), ...) con ceros en los meses sin movimiento.
     */
    public function actividad($id_user, $meses = 12) {
        $desde = date('Y-m-01', strtotime('first day of -' . ($meses - 1) . ' month'));
        $consultas = array(
            'generados' => 'SELECT DATE_FORMAT(fecha_creacion, \'%Y-%m\') AS mes, COUNT(*) AS n FROM documentos
                            WHERE id_user = :id AND fecha_creacion >= :desde GROUP BY mes',
            'recibidos' => 'SELECT DATE_FORMAT(fecha_emision, \'%Y-%m\') AS mes, COUNT(*) AS n FROM seguimiento
                            WHERE derivado_a = :id AND fecha_emision >= :desde GROUP BY mes',
            'derivados' => 'SELECT DATE_FORMAT(fecha_emision, \'%Y-%m\') AS mes, COUNT(*) AS n FROM seguimiento
                            WHERE derivado_por = :id AND fecha_emision >= :desde GROUP BY mes',
        );

        $lista_meses = array();
        for ($i = $meses - 1; $i >= 0; $i--) {
            $lista_meses[] = date('Y-m', strtotime('first day of -' . $i . ' month'));
        }
        $resultado = array('meses' => $lista_meses);
        foreach ($consultas as $serie => $sql) {
            $por_mes = DB::query(Database::SELECT, $sql)
                    ->param(':id', (int) $id_user)
                    ->param(':desde', $desde)
                    ->execute()
                    ->as_array('mes', 'n');
            $resultado[$serie] = array();
            foreach ($lista_meses as $mes) {
                $resultado[$serie][] = isset($por_mes[$mes]) ? (int) $por_mes[$mes] : 0;
            }
        }
        return $resultado;
    }

    /**
     * Correspondencia recibida con accion pendiente (estado 2), de la mas antigua a la mas reciente.
     */
    public function pendientes_antiguos($id_user, $limite = 6) {
        $sql = 'SELECT s.id, s.nur, s.nombre_emisor, s.cargo_emisor, s.prioridad, s.oficial,
                       MAX(d.referencia) AS referencia,
                       COALESCE(s.fecha_recepcion, s.fecha_emision) AS fecha,
                       DATEDIFF(NOW(), COALESCE(s.fecha_recepcion, s.fecha_emision)) AS dias
                FROM seguimiento s
                INNER JOIN documentos d ON d.nur = s.nur AND d.original = 1
                WHERE s.derivado_a = :id AND s.estado = 2
                GROUP BY s.id
                ORDER BY fecha ASC
                LIMIT ' . (int) $limite;
        return DB::query(Database::SELECT, $sql)->param(':id', (int) $id_user)->execute()->as_array();
    }

    /**
     * Correspondencia derivada al usuario que aun no recibio (estado 1): primero las urgentes y las mas antiguas.
     */
    public function por_recibir($id_user, $limite = 6) {
        $sql = 'SELECT s.id, s.nur, s.nombre_emisor, s.cargo_emisor, s.prioridad, s.oficial, s.fecha_emision AS fecha,
                       MAX(d.referencia) AS referencia,
                       DATEDIFF(NOW(), s.fecha_emision) AS dias
                FROM seguimiento s
                INNER JOIN documentos d ON d.nur = s.nur AND d.original = 1
                WHERE s.derivado_a = :id AND s.estado = 1
                GROUP BY s.id
                ORDER BY s.prioridad DESC, s.fecha_emision ASC
                LIMIT ' . (int) $limite;
        return DB::query(Database::SELECT, $sql)->param(':id', (int) $id_user)->execute()->as_array();
    }

}
