<?php

defined('SYSPATH') or die('Acceso denegado');

/**
 * Consultas del Centro de indicadores (reports/tablero, oficinas, rezago, produccion).
 *
 * Convenciones:
 * - Un "movimiento" es una fila de seguimiento (una derivacion de hoja de ruta).
 * - El filtro de oficina se aplica a la oficina DESTINO del movimiento (id_a_oficina),
 *   es decir, a lo que la oficina recibio y debe atender.
 * - Plazo: un movimiento esta vencido cuando lleva PLAZO_DIAS o mas dias habiles sin
 *   atenderse (mismo criterio que los reportes de "fuera de plazo" existentes).
 */
class Model_Indicadores {

    const PLAZO_DIAS = 5;

    // estados de seguimiento
    const NO_RECIBIDO = 1;
    const PENDIENTE = 2;
    const DERIVADO = 4;
    const AGRUPADO = 6;
    const ARCHIVADO = 10;
    const ANULADO = 11;

    protected $desde;
    protected $hasta;
    protected $oficina;
    protected $usuario;

    /**
     * Con $usuario > 0 los indicadores son de esa persona: lo que le derivaron (derivado_a)
     * y los documentos que genero (documentos.id_user); en ese caso se ignora $oficina.
     */
    public function __construct($desde, $hasta, $oficina = 0, $usuario = 0) {
        $this->desde = $desde . ' 00:00:00';
        $this->hasta = $hasta . ' 23:59:59';
        $this->oficina = (int) $oficina;
        $this->usuario = (int) $usuario;
    }

    // periodo pedido por GET (desde/hasta en Y-m-d); por defecto el año en curso
    public static function periodo(array $get) {
        $valida = function ($f) {
            return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $f) && strtotime($f) !== FALSE;
        };
        $desde = isset($get['desde']) && $valida($get['desde']) ? $get['desde'] : date('Y-01-01');
        $hasta = isset($get['hasta']) && $valida($get['hasta']) ? $get['hasta'] : date('Y-m-d');
        return $desde > $hasta ? array($hasta, $desde) : array($desde, $hasta);
    }

    // datos del tablero de una persona (lo usan reports/persona y el tablero propio de cada usuario)
    public function tablero_persona() {
        $perfil = $this->perfil();
        $datos = array(
            'perfil' => $perfil,
            'volumen' => $this->volumen(),
            'enviados' => $this->enviados(),
            'tiempos' => $this->tiempos(),
            'cumplimiento' => $this->cumplimiento(),
            'rezago' => $this->rezago(),
            'tendencia' => $this->tendencia_persona(),
            'contrapartes' => $this->contrapartes(8),
            'horas' => $this->por_hora(),
            'acciones' => $this->por_accion(),
            'tipos' => $this->documentos_por_tipo(),
            'abiertos' => $this->detalle_rezago(FALSE, 300),
            'oficina' => NULL,
        );
        // referencia: los mismos indicadores para toda su oficina
        if ($perfil && $perfil['id_oficina']) {
            $o = new Model_Indicadores(substr($this->desde, 0, 10), substr($this->hasta, 0, 10), $perfil['id_oficina']);
            $datos['oficina'] = array('tiempos' => $o->tiempos(), 'cumplimiento' => $o->cumplimiento());
        }
        return $datos;
    }

    // "3 h 20 min", "2,4 días"; los dias son corridos (24 h)
    public static function duracion($minutos) {
        if ($minutos === NULL) {
            return '—';
        }
        if ($minutos < 60) {
            return round($minutos) . ' min';
        }
        if ($minutos < 1440) {
            $h = floor($minutos / 60);
            $m = round($minutos - $h * 60);
            return $h . ' h' . ($m > 0 ? ' ' . $m . ' min' : '');
        }
        return number_format($minutos / 1440, 1, ',', '.') . ' días';
    }

    public static function numero($n) {
        return number_format((float) $n, 0, ',', '.');
    }

    // clase de semaforo segun % de cumplimiento
    public static function semaforo($porcentaje) {
        if ($porcentaje === NULL) {
            return 'gris';
        }
        return $porcentaje >= 80 ? 'verde' : ($porcentaje >= 60 ? 'ambar' : 'rojo');
    }

    protected function query($sql, array $params = array()) {
        $params += array(':desde' => $this->desde, ':hasta' => $this->hasta, ':oficina' => $this->oficina, ':usuario' => $this->usuario, ':plazo' => self::PLAZO_DIAS);
        return DB::query(Database::SELECT, $sql)->parameters($params)->execute()->as_array();
    }

    protected function filtro_oficina($columna = 's.id_a_oficina') {
        if ($this->usuario > 0) {
            $por_usuario = array('s.id_a_oficina' => 's.derivado_a', 'd.id_oficina' => 'd.id_user');
            return isset($por_usuario[$columna]) ? " AND {$por_usuario[$columna]} = :usuario" : '';
        }
        return $this->oficina > 0 ? " AND $columna = :oficina" : '';
    }

    // minutos promedio que tarda el destinatario en dar por recibido un movimiento
    // y en atenderlo (derivarlo) despues de recibirlo
    public function tiempos() {
        $f = $this->filtro_oficina();
        $recepcion = $this->query("SELECT AVG(TIMESTAMPDIFF(MINUTE, s.fecha_emision, s.fecha_recepcion)) AS minutos
                FROM seguimiento s
                WHERE s.fecha_emision BETWEEN :desde AND :hasta
                  AND s.fecha_recepcion IS NOT NULL AND s.fecha_recepcion >= s.fecha_emision $f");
        $atencion = $this->query("SELECT AVG(TIMESTAMPDIFF(MINUTE, s.fecha_recepcion, h.primera)) AS minutos
                FROM seguimiento s
                INNER JOIN (SELECT id_seguimiento, MIN(fecha_emision) AS primera
                            FROM seguimiento
                            WHERE id_seguimiento > 0 AND fecha_emision >= :desde
                            GROUP BY id_seguimiento) h ON h.id_seguimiento = s.id
                WHERE s.fecha_emision BETWEEN :desde AND :hasta
                  AND s.estado = " . self::DERIVADO . "
                  AND s.fecha_recepcion IS NOT NULL AND h.primera >= s.fecha_recepcion $f");
        return array(
            'recepcion' => $recepcion[0]['minutos'] === NULL ? NULL : (float) $recepcion[0]['minutos'],
            'atencion' => $atencion[0]['minutos'] === NULL ? NULL : (float) $atencion[0]['minutos'],
        );
    }

    // volumen del periodo
    public function volumen() {
        $f = $this->filtro_oficina();
        $mov = $this->query("SELECT COUNT(*) AS total,
                    SUM(s.estado IN (" . self::DERIVADO . "," . self::ARCHIVADO . "," . self::AGRUPADO . ")) AS atendidos,
                    SUM(s.estado = " . self::ARCHIVADO . ") AS archivados,
                    SUM(s.estado IN (" . self::NO_RECIBIDO . "," . self::PENDIENTE . ")) AS abiertos,
                    SUM(s.prioridad = 1) AS urgentes,
                    COUNT(DISTINCT s.nur) AS hojas
                FROM seguimiento s
                WHERE s.fecha_emision BETWEEN :desde AND :hasta
                  AND s.estado <> " . self::ANULADO . " $f");
        $nuevas = $this->query("SELECT COUNT(*) AS total
                FROM nurs n
                " . ($this->usuario <= 0 && $this->oficina > 0 ? 'INNER JOIN users u ON u.id = n.id_user AND u.id_oficina = :oficina' : '') . "
                WHERE n.fecha_creacion BETWEEN :desde AND :hasta" . ($this->usuario > 0 ? ' AND n.id_user = :usuario' : ''));
        $docs = $this->query("SELECT COUNT(*) AS total, SUM(d.id_tipo = 6) AS externos
                FROM documentos d
                WHERE d.fecha_creacion BETWEEN :desde AND :hasta" . $this->filtro_oficina('d.id_oficina'));
        $m = $mov[0];
        return array(
            'movimientos' => (int) $m['total'],
            'atendidos' => (int) $m['atendidos'],
            'archivados' => (int) $m['archivados'],
            'abiertos' => (int) $m['abiertos'],
            'urgentes' => (int) $m['urgentes'],
            'hojas_movidas' => (int) $m['hojas'],
            'hojas_nuevas' => (int) $nuevas[0]['total'],
            'documentos' => (int) $docs[0]['total'],
            'externos' => (int) $docs[0]['externos'],
        );
    }

    // % de movimientos derivados en el periodo cuya siguiente derivacion salio dentro del plazo
    // (los archivados no guardan su fecha en seguimiento, por eso solo se miden derivaciones)
    public function cumplimiento() {
        $f = $this->filtro_oficina();
        $r = $this->query("SELECT COUNT(*) AS cerrados,
                    SUM(RESTA2_FECHAS(s.fecha_emision, h.primera) < :plazo) AS en_plazo
                FROM seguimiento s
                INNER JOIN (SELECT id_seguimiento, MIN(fecha_emision) AS primera
                            FROM seguimiento
                            WHERE id_seguimiento > 0 AND fecha_emision >= :desde
                            GROUP BY id_seguimiento) h ON h.id_seguimiento = s.id
                WHERE s.fecha_emision BETWEEN :desde AND :hasta
                  AND s.estado = " . self::DERIVADO . " $f");
        $cerrados = (int) $r[0]['cerrados'];
        return array(
            'cerrados' => $cerrados,
            'en_plazo' => (int) $r[0]['en_plazo'],
            'porcentaje' => $cerrados > 0 ? round(100 * $r[0]['en_plazo'] / $cerrados, 1) : NULL,
        );
    }

    // foto actual de lo que esta sin atender (no depende del periodo)
    public function rezago() {
        $f = $this->filtro_oficina();
        $r = $this->query("SELECT
                    COUNT(*) AS total,
                    SUM(x.estado = " . self::NO_RECIBIDO . ") AS no_recibidos,
                    SUM(x.estado = " . self::PENDIENTE . ") AS pendientes,
                    SUM(x.dias >= :plazo) AS vencidos,
                    SUM(x.oficial = 0) AS copias,
                    SUM(x.accion = 8) AS por_archivar,
                    SUM(x.dias <= 2) AS b0,
                    SUM(x.dias BETWEEN 3 AND :plazo - 1) AS b1,
                    SUM(x.dias BETWEEN :plazo AND 10) AS b2,
                    SUM(x.dias BETWEEN 11 AND 30) AS b3,
                    SUM(x.dias BETWEEN 31 AND 120) AS b4,
                    SUM(x.dias > 120) AS b5,
                    MAX(x.dias) AS maximo,
                    AVG(x.dias) AS promedio
                FROM (
                    SELECT s.estado, s.oficial, s.accion, RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias
                    FROM seguimiento s
                    WHERE s.estado IN (" . self::NO_RECIBIDO . "," . self::PENDIENTE . ") $f
                ) x");
        $r = $r[0];
        $out = array();
        foreach ($r as $k => $v) {
            $out[$k] = $v === NULL ? 0 : ($k === 'promedio' ? round($v, 1) : (int) $v);
        }
        return $out;
    }

    // movimientos, hojas nuevas y archivados por mes en los ultimos 12 meses hasta "hasta"
    public function tendencia_mensual() {
        $f = $this->filtro_oficina();
        $inicio = date('Y-m-01 00:00:00', strtotime(substr($this->hasta, 0, 10) . ' -11 months'));
        $p = array(':inicio' => $inicio);
        $mov = $this->query("SELECT DATE_FORMAT(s.fecha_emision, '%Y-%m') AS mes, COUNT(*) AS total,
                    SUM(s.estado IN (" . self::NO_RECIBIDO . "," . self::PENDIENTE . ")) AS abiertos
                FROM seguimiento s
                WHERE s.fecha_emision BETWEEN :inicio AND :hasta AND s.estado <> " . self::ANULADO . " $f
                GROUP BY mes", $p);
        $docs = $this->query("SELECT DATE_FORMAT(d.fecha_creacion, '%Y-%m') AS mes, COUNT(*) AS total
                FROM documentos d
                WHERE d.fecha_creacion BETWEEN :inicio AND :hasta" . $this->filtro_oficina('d.id_oficina') . "
                GROUP BY mes", $p);
        $meses = array();
        for ($i = 0; $i < 12; $i++) {
            $k = date('Y-m', strtotime($inicio . " +$i months"));
            $meses[$k] = array('mes' => $k, 'movimientos' => 0, 'abiertos' => 0, 'documentos' => 0);
        }
        foreach ($mov as $m) {
            if (isset($meses[$m['mes']])) {
                $meses[$m['mes']]['movimientos'] = (int) $m['total'];
                $meses[$m['mes']]['abiertos'] = (int) $m['abiertos'];
            }
        }
        foreach ($docs as $d) {
            if (isset($meses[$d['mes']])) {
                $meses[$d['mes']]['documentos'] = (int) $d['total'];
            }
        }
        return array_values($meses);
    }

    public function por_estado() {
        return $this->query("SELECT e.estado AS etiqueta, COUNT(*) AS total
                FROM seguimiento s INNER JOIN estados e ON e.id = s.estado
                WHERE s.fecha_emision BETWEEN :desde AND :hasta" . $this->filtro_oficina() . "
                GROUP BY s.estado ORDER BY total DESC");
    }

    public function por_accion() {
        return $this->query("SELECT COALESCE(a.accion, 'Sin instrucción') AS etiqueta, COUNT(*) AS total
                FROM seguimiento s LEFT JOIN acciones a ON a.id = s.accion
                WHERE s.fecha_emision BETWEEN :desde AND :hasta" . $this->filtro_oficina() . "
                GROUP BY etiqueta ORDER BY total DESC");
    }

    // carga por dia de la semana (1=domingo) del periodo
    public function por_dia_semana() {
        $r = $this->query("SELECT DAYOFWEEK(s.fecha_emision) AS dia, COUNT(*) AS total
                FROM seguimiento s
                WHERE s.fecha_emision BETWEEN :desde AND :hasta" . $this->filtro_oficina() . "
                GROUP BY dia");
        $dias = array_fill(1, 7, 0);
        foreach ($r as $d) {
            $dias[(int) $d['dia']] = (int) $d['total'];
        }
        return $dias;
    }

    /**
     * Indicadores por oficina destino (o por funcionario de una oficina si $por_usuario).
     * Mezcla periodo (recibidos, atendidos, tiempos, cumplimiento) y foto actual (rezago).
     */
    public function desempeno($por_usuario = FALSE) {
        if ($por_usuario) {
            $clave = 's.derivado_a';
            $f = $this->filtro_oficina('u.id_oficina');
            $join = 'INNER JOIN users u ON u.id = s.derivado_a';
        } else {
            $clave = 's.id_a_oficina';
            $f = $this->filtro_oficina();
            $join = '';
        }
        $periodo = $this->query("SELECT $clave AS id,
                    COUNT(*) AS recibidos,
                    SUM(s.estado IN (" . self::DERIVADO . "," . self::ARCHIVADO . "," . self::AGRUPADO . ")) AS atendidos,
                    SUM(s.estado = " . self::ARCHIVADO . ") AS archivados,
                    AVG(CASE WHEN s.fecha_recepcion >= s.fecha_emision THEN TIMESTAMPDIFF(MINUTE, s.fecha_emision, s.fecha_recepcion) END) AS min_recepcion,
                    AVG(CASE WHEN s.estado = " . self::DERIVADO . " AND h.primera >= s.fecha_recepcion THEN TIMESTAMPDIFF(MINUTE, s.fecha_recepcion, h.primera) END) AS min_atencion,
                    SUM(s.estado = " . self::DERIVADO . " AND h.primera IS NOT NULL) AS derivados_medidos,
                    SUM(s.estado = " . self::DERIVADO . " AND RESTA2_FECHAS(s.fecha_emision, h.primera) < :plazo) AS derivados_en_plazo
                FROM seguimiento s
                $join
                LEFT JOIN (SELECT id_seguimiento, MIN(fecha_emision) AS primera
                           FROM seguimiento
                           WHERE id_seguimiento > 0 AND fecha_emision >= :desde
                           GROUP BY id_seguimiento) h ON h.id_seguimiento = s.id
                WHERE s.fecha_emision BETWEEN :desde AND :hasta AND s.estado <> " . self::ANULADO . " $f
                GROUP BY $clave");
        $abiertos = $this->query("SELECT $clave AS id,
                    SUM(s.estado = " . self::PENDIENTE . ") AS pendientes,
                    SUM(s.estado = " . self::NO_RECIBIDO . ") AS no_recibidos,
                    SUM(RESTA2_FECHAS(s.fecha_emision, NOW()) >= :plazo) AS vencidos,
                    MAX(RESTA2_FECHAS(s.fecha_emision, NOW())) AS mas_antiguo
                FROM seguimiento s
                $join
                WHERE s.estado IN (" . self::NO_RECIBIDO . "," . self::PENDIENTE . ") $f
                GROUP BY $clave");

        if ($por_usuario) {
            $nombres = $this->query("SELECT u.id, u.nombre, u.cargo, o.sigla, u.habilitado,
                        FROM_UNIXTIME(u.last_login) AS ultimo_ingreso
                    FROM users u LEFT JOIN oficinas o ON o.id = u.id_oficina
                    WHERE 1 = 1" . $this->filtro_oficina('u.id_oficina'));
        } else {
            $nombres = $this->query("SELECT o.id, o.oficina AS nombre, o.sigla FROM oficinas o
                    WHERE 1 = 1" . $this->filtro_oficina('o.id'));
        }

        $filas = array();
        foreach ($nombres as $n) {
            $filas[$n['id']] = array(
                'id' => (int) $n['id'],
                'nombre' => $n['nombre'],
                'sigla' => $n['sigla'],
                'cargo' => isset($n['cargo']) ? $n['cargo'] : '',
                'ultimo_ingreso' => isset($n['ultimo_ingreso']) ? $n['ultimo_ingreso'] : NULL,
                'recibidos' => 0, 'atendidos' => 0, 'archivados' => 0,
                'min_recepcion' => NULL, 'min_atencion' => NULL, 'cumplimiento' => NULL,
                'pendientes' => 0, 'no_recibidos' => 0, 'vencidos' => 0, 'mas_antiguo' => 0,
            );
        }
        foreach ($periodo as $p) {
            if (!isset($filas[$p['id']])) {
                continue;
            }
            $fila = & $filas[$p['id']];
            $fila['recibidos'] = (int) $p['recibidos'];
            $fila['atendidos'] = (int) $p['atendidos'];
            $fila['archivados'] = (int) $p['archivados'];
            $fila['min_recepcion'] = $p['min_recepcion'] === NULL ? NULL : (float) $p['min_recepcion'];
            $fila['min_atencion'] = $p['min_atencion'] === NULL ? NULL : (float) $p['min_atencion'];
            $fila['cumplimiento'] = $p['derivados_medidos'] > 0 ? round(100 * $p['derivados_en_plazo'] / $p['derivados_medidos'], 1) : NULL;
            unset($fila);
        }
        foreach ($abiertos as $a) {
            if (!isset($filas[$a['id']])) {
                continue;
            }
            $filas[$a['id']]['pendientes'] = (int) $a['pendientes'];
            $filas[$a['id']]['no_recibidos'] = (int) $a['no_recibidos'];
            $filas[$a['id']]['vencidos'] = (int) $a['vencidos'];
            $filas[$a['id']]['mas_antiguo'] = (int) $a['mas_antiguo'];
        }
        // solo quienes tuvieron actividad en el periodo o tienen algo abierto
        $filas = array_filter($filas, function ($f) {
            return $f['recibidos'] > 0 || $f['pendientes'] > 0 || $f['no_recibidos'] > 0;
        });
        usort($filas, function ($a, $b) {
            return $b['vencidos'] - $a['vencidos'] ?: $b['recibidos'] - $a['recibidos'];
        });
        return $filas;
    }

    // detalle de lo abierto, del mas antiguo al mas reciente
    public function detalle_rezago($solo_vencidos = TRUE, $limite = 1000) {
        $limite = max(1, (int) $limite);
        return $this->query("SELECT s.id, s.nur, s.nombre_emisor, s.de_oficina, s.nombre_receptor, s.cargo_receptor,
                    s.a_oficina, s.fecha_emision, s.fecha_recepcion, s.estado, s.oficial, s.prioridad,
                    a.accion, RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    (SELECT d.referencia FROM documentos d WHERE d.nur = s.nur ORDER BY d.id LIMIT 1) AS referencia
                FROM seguimiento s
                LEFT JOIN acciones a ON a.id = s.accion
                WHERE s.estado IN (" . self::NO_RECIBIDO . "," . self::PENDIENTE . ")" . $this->filtro_oficina() . "
                " . ($solo_vencidos ? 'HAVING dias >= :plazo' : '') . "
                ORDER BY s.fecha_emision ASC
                LIMIT $limite");
    }

    public function documentos_por_tipo() {
        return $this->query("SELECT COALESCE(t.tipo, 'Sin tipo') AS etiqueta, COUNT(*) AS total
                FROM documentos d LEFT JOIN tipos t ON t.id = d.id_tipo
                WHERE d.fecha_creacion BETWEEN :desde AND :hasta" . $this->filtro_oficina('d.id_oficina') . "
                GROUP BY etiqueta ORDER BY total DESC");
    }

    public function documentos_por_oficina($limite = 15) {
        return $this->query("SELECT COALESCE(o.sigla, 'Sin oficina') AS etiqueta, o.oficina AS nombre, COUNT(*) AS total
                FROM documentos d LEFT JOIN oficinas o ON o.id = d.id_oficina
                WHERE d.fecha_creacion BETWEEN :desde AND :hasta" . $this->filtro_oficina('d.id_oficina') . "
                GROUP BY d.id_oficina ORDER BY total DESC LIMIT " . (int) $limite);
    }

    public function documentos_por_usuario($limite = 15) {
        return $this->query("SELECT u.nombre AS etiqueta, o.sigla, COUNT(*) AS total
                FROM documentos d INNER JOIN users u ON u.id = d.id_user LEFT JOIN oficinas o ON o.id = u.id_oficina
                WHERE d.fecha_creacion BETWEEN :desde AND :hasta" . $this->filtro_oficina('d.id_oficina') . "
                GROUP BY d.id_user ORDER BY total DESC LIMIT " . (int) $limite);
    }

    // documentos por mes y tipo (ultimos 12 meses hasta "hasta")
    public function documentos_mensual_por_tipo() {
        $inicio = date('Y-m-01 00:00:00', strtotime(substr($this->hasta, 0, 10) . ' -11 months'));
        $r = $this->query("SELECT DATE_FORMAT(d.fecha_creacion, '%Y-%m') AS mes, COALESCE(t.tipo, 'Sin tipo') AS tipo, COUNT(*) AS total
                FROM documentos d LEFT JOIN tipos t ON t.id = d.id_tipo
                WHERE d.fecha_creacion BETWEEN :inicio AND :hasta" . $this->filtro_oficina('d.id_oficina') . "
                GROUP BY mes, tipo", array(':inicio' => $inicio));
        $meses = array();
        for ($i = 0; $i < 12; $i++) {
            $meses[] = date('Y-m', strtotime($inicio . " +$i months"));
        }
        $series = array();
        foreach ($r as $row) {
            if (!isset($series[$row['tipo']])) {
                $series[$row['tipo']] = array_fill_keys($meses, 0);
            }
            $series[$row['tipo']][$row['mes']] = (int) $row['total'];
        }
        return array('meses' => $meses, 'series' => $series);
    }

    // hojas de ruta que nacen con documento externo vs internas
    public function origen_hojas() {
        $r = $this->query("SELECT SUM(x.externo) AS externas, SUM(1 - x.externo) AS internas
                FROM (
                    SELECT n.nur, MAX(d.id_tipo = 6) AS externo
                    FROM nurs n
                    INNER JOIN documentos d ON d.nur = n.nur
                    WHERE n.fecha_creacion BETWEEN :desde AND :hasta" . $this->filtro_oficina('d.id_oficina') . "
                    GROUP BY n.nur
                ) x");
        return array('externas' => (int) $r[0]['externas'], 'internas' => (int) $r[0]['internas']);
    }

    // ---------- tablero por persona (requieren $usuario) ----------

    public function perfil() {
        $r = $this->query("SELECT u.id, u.nombre, u.cargo, u.username, u.habilitado, u.logins, u.id_oficina,
                    o.oficina, o.sigla, FROM_UNIXTIME(u.last_login) AS ultimo_ingreso
                FROM users u LEFT JOIN oficinas o ON o.id = u.id_oficina
                WHERE u.id = :usuario");
        return $r ? $r[0] : NULL;
    }

    // derivaciones que la persona hizo (envio) en el periodo
    public function enviados() {
        $r = $this->query("SELECT COUNT(*) AS total, COUNT(DISTINCT s.nur) AS hojas, SUM(s.prioridad = 1) AS urgentes
                FROM seguimiento s
                WHERE s.derivado_por = :usuario AND s.fecha_emision BETWEEN :desde AND :hasta AND s.estado <> " . self::ANULADO);
        return array('total' => (int) $r[0]['total'], 'hojas' => (int) $r[0]['hojas'], 'urgentes' => (int) $r[0]['urgentes']);
    }

    // recibidos, enviados y documentos por mes (ultimos 12 meses hasta "hasta")
    public function tendencia_persona() {
        $inicio = date('Y-m-01 00:00:00', strtotime(substr($this->hasta, 0, 10) . ' -11 months'));
        $p = array(':inicio' => $inicio);
        $series = array(
            'recibidos' => $this->query("SELECT DATE_FORMAT(fecha_emision, '%Y-%m') AS mes, COUNT(*) AS total FROM seguimiento
                    WHERE derivado_a = :usuario AND fecha_emision BETWEEN :inicio AND :hasta AND estado <> " . self::ANULADO . " GROUP BY mes", $p),
            'enviados' => $this->query("SELECT DATE_FORMAT(fecha_emision, '%Y-%m') AS mes, COUNT(*) AS total FROM seguimiento
                    WHERE derivado_por = :usuario AND fecha_emision BETWEEN :inicio AND :hasta AND estado <> " . self::ANULADO . " GROUP BY mes", $p),
            'documentos' => $this->query("SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS mes, COUNT(*) AS total FROM documentos
                    WHERE id_user = :usuario AND fecha_creacion BETWEEN :inicio AND :hasta GROUP BY mes", $p),
        );
        $meses = array();
        for ($i = 0; $i < 12; $i++) {
            $k = date('Y-m', strtotime($inicio . " +$i months"));
            $meses[$k] = array('mes' => $k, 'recibidos' => 0, 'enviados' => 0, 'documentos' => 0);
        }
        foreach ($series as $nombre => $filas) {
            foreach ($filas as $f) {
                if (isset($meses[$f['mes']])) {
                    $meses[$f['mes']][$nombre] = (int) $f['total'];
                }
            }
        }
        return array_values($meses);
    }

    // quienes mas le derivan y a quienes mas deriva, en el periodo
    public function contrapartes($limite = 8) {
        $limite = (int) $limite;
        return array(
            'remitentes' => $this->query("SELECT s.derivado_por AS id, MAX(s.nombre_emisor) AS nombre, MAX(s.de_oficina) AS oficina, COUNT(*) AS total
                    FROM seguimiento s
                    WHERE s.derivado_a = :usuario AND s.fecha_emision BETWEEN :desde AND :hasta AND s.estado <> " . self::ANULADO . "
                    GROUP BY s.derivado_por ORDER BY total DESC LIMIT $limite"),
            'destinatarios' => $this->query("SELECT s.derivado_a AS id, MAX(s.nombre_receptor) AS nombre, MAX(s.a_oficina) AS oficina, COUNT(*) AS total
                    FROM seguimiento s
                    WHERE s.derivado_por = :usuario AND s.fecha_emision BETWEEN :desde AND :hasta AND s.estado <> " . self::ANULADO . "
                    GROUP BY s.derivado_a ORDER BY total DESC LIMIT $limite"),
        );
    }

    // hora del dia en que la persona deriva (0-23)
    public function por_hora() {
        $r = $this->query("SELECT HOUR(s.fecha_emision) AS hora, COUNT(*) AS total
                FROM seguimiento s
                WHERE s.derivado_por = :usuario AND s.fecha_emision BETWEEN :desde AND :hasta
                GROUP BY hora");
        $horas = array_fill(0, 24, 0);
        foreach ($r as $h) {
            $horas[(int) $h['hora']] = (int) $h['total'];
        }
        return $horas;
    }

}
