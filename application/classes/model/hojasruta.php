<?php

defined('SYSPATH') or die('no tiene acceso');

//descripcion del modelo productos
class Model_Hojasruta extends ORM {

    protected $_table_names_plural = false;

    //protected $_sorting = array('fecha_publicacion' => 'DESC');
    //mis hojas de ruta creadas por un usuario
    public function HRhijos($p) {
        $p = SqlSafe::value($p); // se pega en el SQL entre comillas
        $sql = "SELECT d.nur,d.referencia,d.cite_original,IF(s.oficial>0,'Oficial','Copia') as oficial,
            DATE_FORMAT(s.fecha_recepcion,'%d-%m-%Y %H:%i:%s') as fecha_recepcion, x.id as id_agrupacion
            FROM  (select id,hijo,fecha,id_seguimiento from agrupaciones where padre='$p') as x, documentos d,seguimiento s
            WHERE d.nur=x.hijo
            AND x.id_seguimiento=s.id
            AND d.original='1'"; //important
        return db::query(Database::SELECT, $sql)->execute();
    }

    //ajsx hoja ruta imprimir
    public function hojaruta($like, $user = NULL) {
        // el texto llega del usuario (autocompletado): se enlaza como parametro, nunca concatenado
        // con $user solo se sugieren hojas de ruta en las que interviene (ver classes/acceso.php)
        $solo_suyas = $user ? Acceso::condicion_sql($user, 'n') : '';
        $sql = "SELECT n.id_user, n.nur FROM nurs n WHERE n.nur LIKE :like"
                . ($solo_suyas !== '' ? ' AND ' . $solo_suyas : '') . "
                ORDER BY n.fecha_creacion DESC
                LIMIT 10"; //important
        return db::query(Database::SELECT, $sql)->param(':like', '%' . $like . '%')->execute();
    }

    /**
     * Ultimas hojas de ruta generadas por el usuario: las de sus documentos originales (documento que dio origen a la hoja de ruta).
     */
    public function recientes_usuario($id_user, $limite = 8) {
        $limite = (int) $limite;
        $sql = "SELECT d.nur, d.fecha_creacion AS fecha, d.referencia
                FROM documentos d USE INDEX (INDEX_ID_USER__FECHA_CREACION)
                WHERE d.id_user = :id AND d.original = 1 AND d.nur <> ''
                ORDER BY d.fecha_creacion DESC
                LIMIT $limite";
        return db::query(Database::SELECT, $sql)->param(':id', (int) $id_user)->execute()->as_array();
    }

    //lista de pendientes a pdf

    public function pendientes($id) {
        $sql = "SELECT s.id, s.padre,s.hijo,s.id_seguimiento,s.nur, s.nombre_emisor,s.cargo_emisor,
            s.de_oficina,s.fecha_emision as fecha,DATE_FORMAT(s.fecha_recepcion,'%d/%m/%Y %H:%i:%s') as fecha_recepcion, 
            a.accion, IF(s.oficial=0,'Copia','Oficial') as oficial, s.hijo, s.proveido,s.adjuntos,s.archivos
             , d.codigo,d.cite_original, d.nombre_destinatario,d.nombre_destinatario, 
             d.cargo_destinatario,d.referencia,d.id as id_doc,s.prioridad,RESTA2_FECHAS(NOW(),s.fecha_recepcion)AS dias_ahora,
RESTA2_FECHAS(s.fecha_recepcion,s.fecha_emision) AS dias_recepcion
            FROM 
            (SELECT *
            FROM seguimiento WHERE derivado_a = :id and estado='2') as s 
            INNER JOIN documentos as d ON s.nur=d.nur
            INNER JOIN acciones a ON s.accion=a.id
            WHERE d.original='1' ORDER BY s.fecha_recepcion DESC"; //important
        return db::query(Database::SELECT, $sql)->param(":id", (int) $id)->execute();
    }

    public function enviados() {
        $sql = "SELECT x.nur,x.fecha_emision,x.derivado_a,x.nombre_receptor,x.cargo_receptor,x.fecha_recepcion,x.estado,y.referencia,y.cite_original,x.accion,x.proveido FROM 
                (select *  from seguimiento where derivado_por='728' and fecha_emision between '2014-05-22 00:00:00' AND '2014-07-31 23:59:00' ) AS x, documentos y
                WHERE x.nur=y.nur
                AND y.original='1'
                ORDER BY x.fecha_emision ASC";
        return db::query(Database::SELECT, $sql)->execute();
        
    }

    public function hojasruta($id_user, $o, $i) {
        $id_user = SqlSafe::value($id_user); // se pega en el SQL entre comillas
        $sql = "SELECT d.id as id_documento, d.codigo,d.cite_original, d.nombre_destinatario, d.cargo_destinatario, 
         d.referencia, d.nur, d.fecha_creacion,d.estado,p.proceso 
        FROM documentos d 
        INNER JOIN procesos p ON d.id_proceso=p.id        
        WHERE d.id_user='$id_user'
        AND d.original='1'
        ORDER BY d.fecha_creacion DESC
        LIMIT $o , $i"; //important
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function imprimir($nur) {
        $nur = SqlSafe::value($nur); // se pega en el SQL entre comillas
        $sql = "SELECT d.id_tipo,d.hojas,d.codigo,d.nur,d.nombre_destinatario,d.cargo_destinatario,d.nombre_remitente,d.cargo_remitente,d.referencia,d.fecha_creacion,d.adjuntos,d.copias,d.institucion_destinatario,d.institucion_remitente
    ,d.cite_original, e.entidad,e.sigla,e.logo2,e.logo,e.sigla2,p.proceso FROM documentos d
    INNER JOIN users u ON u.id=d.id_user 
    INNER JOIN oficinas o ON o.id=u.id_oficina
    INNER JOIN entidades e ON e.id=o.id_entidad 
    INNER JOIN procesos p ON d.id_proceso=p.id
    WHERE d.nur='$nur'
    AND d.original='1'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function proveidos($nur) {
        $nur = SqlSafe::value($nur); // se pega en el SQL entre comillas
        /*
        $sql = "SELECT nombre_receptor,cargo_receptor,proveido,accion
                FROM seguimiento where nur='$nur' and oficial>'0'
               order by id";
        */

        $sql = "SELECT 
                    nombre_receptor, cargo_receptor, proveido, accion
                FROM
                    seguimiento
                WHERE
                    nur = '$nur' -- AND oficial > '0'
                ORDER BY fecha_emision;";
                               
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function agrupado($nur) {
        $nur = SqlSafe::value($nur); // se pega en el SQL entre comillas
        $sql = "SELECT * FROM agrupaciones a INNER  JOIN nurs n ON a.padre=n.nur WHERE a.padre='$nur'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function hijos($nur) {
        $nur = SqlSafe::value($nur); // se pega en el SQL entre comillas
        $sql = "SELECT * FROM seguimiento s
            INNER JOIN nurs n ON s.nur=n.nur
            INNER JOIN agrupaciones a ON s.id=a.id_seguimiento
            WHERE a.padre='$nur'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    // hoja de ruta "padre" en la que fue agrupado un nur (cuando el nur es el "hijo")
    public function HRpadre($hijo) {
        $hijo = SqlSafe::value($hijo); // se pega en el SQL entre comillas
        $sql = "SELECT a.id as id_agrupacion, a.padre, a.fecha, a.nombre, a.cargo, d.referencia, d.cite_original,
            d.nombre_destinatario, d.cargo_destinatario
            FROM agrupaciones a
            INNER JOIN documentos d ON d.nur = a.padre AND d.original = '1'
            WHERE a.hijo = '$hijo'
            LIMIT 1";
        return db::query(Database::SELECT, $sql)->execute();
    }

    // listado admin: todas las hojas de ruta generadas en el sistema, ordenadas por fecha de creacion reciente
    /*
     * Listado de administracion. $f: q (texto), anio, tipo (id_tipo), estado (estado actual de la hoja de ruta:
     * 0 = sin derivar, o el estado de la ultima derivacion oficial: 1 no recibido, 2 pendiente, 6 agrupado, 10 archivado...).
     * Tambien acepta un texto (forma anterior: solo busqueda).
     */
    protected function filtroAdmin($f, &$params) {
        if (!is_array($f)) {
            $f = array('q' => (string) $f);
        }
        $w = array("d.original = '1'");
        $q = trim((string) Arr::get($f, 'q', ''));
        if ($q !== '') {
            $w[] = '(d.nur LIKE :q OR d.cite_original LIKE :q OR d.referencia LIKE :q OR d.nombre_destinatario LIKE :q OR u.nombre LIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }
        if ((int) Arr::get($f, 'anio', 0) > 0) {
            $w[] = 'd.fecha_creacion >= :desde AND d.fecha_creacion < :hasta';
            $params[':desde'] = (int) $f['anio'] . '-01-01';
            $params[':hasta'] = ((int) $f['anio'] + 1) . '-01-01';
        }
        if ((int) Arr::get($f, 'tipo', 0) > 0) {
            $w[] = 'd.id_tipo = :tipo';
            $params[':tipo'] = (int) $f['tipo'];
        }
        $estado = Arr::get($f, 'estado', '');
        if ($estado !== '' && $estado !== NULL) {
            $w[] = self::SQL_ESTADO_HR . ' = :estado';
            $params[':estado'] = (int) $estado;
        }
        return ' WHERE ' . implode(' AND ', $w) . ' ';
    }

    // estado actual: 0 si el documento no se derivo; si no, el de la ultima derivacion oficial
    const SQL_ESTADO_HR = "IF(d.estado = 0, 0, (SELECT s.estado FROM seguimiento s WHERE s.nur = d.nur AND s.oficial > 0 ORDER BY s.id DESC LIMIT 1))";

    public function todasAdmin($o, $i, $f = array()) {
        $params = array();
        $where = $this->filtroAdmin($f, $params);
        $o = max(0, (int) $o);
        $i = max(1, (int) $i);
        $sql = "SELECT d.id, d.nur, d.codigo, d.cite_original, d.referencia, d.estado,
                d.nombre_destinatario, d.cargo_destinatario, d.fecha_creacion, d.id_user,
                u.nombre AS creado_por, u.username, u.genero, p.proceso, t.tipo,
                (SELECT COUNT(*) FROM agrupaciones a WHERE a.padre = d.nur OR a.hijo = d.nur) AS agrupado,
                " . self::SQL_ESTADO_HR . " AS estado_hr,
                (SELECT s.nombre_receptor FROM seguimiento s WHERE s.nur = d.nur AND s.oficial > 0 ORDER BY s.id DESC LIMIT 1) AS con_quien
            FROM documentos d
            LEFT JOIN users u ON u.id = d.id_user
            LEFT JOIN procesos p ON p.id = d.id_proceso
            LEFT JOIN tipos t ON t.id = d.id_tipo
            $where
            ORDER BY d.fecha_creacion DESC
            LIMIT $o, $i";
        $query = db::query(Database::SELECT, $sql);
        foreach ($params as $k => $v) {
            $query->param($k, $v);
        }
        return $query->execute();
    }

    public function contarTodas($f = array()) {
        $params = array();
        $where = $this->filtroAdmin($f, $params);
        $sql = "SELECT COUNT(*) AS n FROM documentos d LEFT JOIN users u ON u.id = d.id_user $where";
        $query = db::query(Database::SELECT, $sql);
        foreach ($params as $k => $v) {
            $query->param($k, $v);
        }
        $r = $query->execute()->as_array();
        return isset($r[0]['n']) ? (int) $r[0]['n'] : 0;
    }
    public function select($sql) {
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    /**
     * "Mis seguimientos": una fila por hoja de ruta en la que el usuario intervino como creador (documento
     * original) o como emisor (seguimiento.derivado_por), con su situacion actual y su ultimo envio.
     * Situacion: curso (alguien la tiene sin recibir o pendiente), sin_derivar, archivada, agrupada o concluida.
     * El paso "oficial = 1" es la rama vigente de la hoja de ruta (los pasos ya derivados quedan en 2).
     * $f: q (texto), desde / hasta (Y-m-d, sobre la ultima actividad).
     */
    protected function sqlMisHojas($id_user, array $f, &$params)
    {
        $params[':id'] = (int) $id_user;
        $where = array();
        if (!empty($f['q'])) {
            $where[] = '(m.nur LIKE :q OR d.codigo LIKE :q OR d.cite_original LIKE :q OR d.referencia LIKE :q
                    OR ms.nombre_receptor LIKE :q OR ms.proveido LIKE :q)';
            $params[':q'] = '%' . $f['q'] . '%';
        }
        $actividad = "GREATEST(IFNULL(d.fecha_creacion, '1970-01-01'), IFNULL(ms.fecha_emision, '1970-01-01'))";
        if (!empty($f['desde'])) {
            $where[] = "$actividad >= :desde";
            $params[':desde'] = $f['desde'] . ' 00:00:00';
        }
        if (!empty($f['hasta'])) {
            $where[] = "$actividad <= :hasta";
            $params[':hasta'] = $f['hasta'] . ' 23:59:59';
        }
        // m: hojas del usuario (creadas y/o derivadas) con cuantos envios hizo, cuantos siguen sin recibir
        // y su ultimo envio; doc: el documento original de la hoja (aunque lo haya creado otra persona)
        return "SELECT m.nur, m.creada, m.envios, m.sin_recibir,
                    d.id AS id_documento, d.codigo, d.cite_original, d.referencia, d.fecha_creacion,
                    ms.id AS mi_id, ms.nombre_receptor AS mi_para, ms.cargo_receptor AS mi_cargo, ms.a_oficina AS mi_oficina,
                    ms.fecha_emision AS mi_fecha, ms.fecha_recepcion AS mi_recepcion, ms.estado AS mi_estado,
                    ms.oficial AS mi_oficial, ms.proveido AS mi_proveido,
                    $actividad AS actividad,
                    CASE
                        WHEN EXISTS (SELECT 1 FROM seguimiento s WHERE s.nur = m.nur AND s.oficial = 1 AND s.estado IN (1, 2)) THEN 'curso'
                        WHEN NOT EXISTS (SELECT 1 FROM seguimiento s WHERE s.nur = m.nur) THEN 'sin_derivar'
                        WHEN EXISTS (SELECT 1 FROM seguimiento s WHERE s.nur = m.nur AND s.oficial = 1 AND s.estado = 10) THEN 'archivada'
                        WHEN EXISTS (SELECT 1 FROM seguimiento s WHERE s.nur = m.nur AND s.oficial = 1 AND s.estado = 6) THEN 'agrupada'
                        ELSE 'concluida'
                    END AS situacion
                FROM (
                    SELECT g.*, IFNULL(g.id_doc, (SELECT MIN(d2.id) FROM documentos d2 WHERE d2.nur = g.nur AND d2.original = 1)) AS doc
                    FROM (
                        SELECT x.nur, MAX(x.creada) AS creada, SUM(x.envio) AS envios, SUM(x.abierto) AS sin_recibir,
                               MAX(x.id_seg) AS ultimo_id, MIN(x.id_doc) AS id_doc
                        FROM (
                            SELECT d.nur, 1 AS creada, 0 AS envio, 0 AS abierto, NULL AS id_seg, d.id AS id_doc
                                FROM documentos d USE INDEX (INDEX_ID_USER__FECHA_CREACION)
                                WHERE d.id_user = :id AND d.original = 1 AND d.nur <> ''
                            UNION ALL
                            SELECT s.nur, 0, 1, IF(s.estado = 1, 1, 0), s.id, NULL
                                FROM seguimiento s WHERE s.derivado_por = :id
                        ) x
                        GROUP BY x.nur
                    ) g
                ) m
                LEFT JOIN documentos d ON d.id = m.doc
                LEFT JOIN seguimiento ms ON ms.id = m.ultimo_id"
                . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
    }

    private function ejecutarConParams($sql, array $params)
    {
        $query = db::query(Database::SELECT, $sql);
        foreach ($params as $k => $v) {
            $query->param($k, $v);
        }
        return $query->execute();
    }

    /** Condiciones sobre la fila ya calculada (alias y): alcance, situacion y "sin recibir". */
    private function filtroMisHojas(array $f, &$params)
    {
        $where = array();
        if (isset($f['alcance']) && $f['alcance'] === 'creadas') {
            $where[] = 'y.creada > 0';
        } elseif (isset($f['alcance']) && $f['alcance'] === 'derivadas') {
            $where[] = 'y.envios > 0';
        }
        if (!empty($f['sin_recibir'])) {
            $where[] = 'y.sin_recibir > 0';
        }
        if (!empty($f['situacion'])) {
            $where[] = 'y.situacion = :situacion';
            $params[':situacion'] = $f['situacion'];
        }
        return $where ? ' WHERE ' . implode(' AND ', $where) : '';
    }

    /**
     * Conteo agrupado de "Mis seguimientos" (con q y fechas, sin los demas filtros), para los contadores:
     * filas array(situacion, creada 0/1, derivada 0/1, sin_recibir 0/1, n).
     */
    public function resumenMisHojas($id_user, array $f)
    {
        $params = array();
        $sql = "SELECT y.situacion, IF(y.creada > 0, 1, 0) AS creada, IF(y.envios > 0, 1, 0) AS derivada,
                    IF(y.sin_recibir > 0, 1, 0) AS sin_recibir, COUNT(*) AS n
                FROM (" . $this->sqlMisHojas($id_user, $f, $params) . ") y
                GROUP BY 1, 2, 3, 4";
        return $this->ejecutarConParams($sql, $params)->as_array();
    }

    /** Una pagina de "Mis seguimientos" (la actividad mas reciente primero). */
    public function misHojas($id_user, array $f, $offset, $limite)
    {
        $params = array();
        $sql = "SELECT y.* FROM (" . $this->sqlMisHojas($id_user, $f, $params) . ") y"
                . $this->filtroMisHojas($f, $params)
                . " ORDER BY y.actividad DESC, y.nur DESC LIMIT " . (int) $offset . ", " . (int) $limite;
        return $this->ejecutarConParams($sql, $params)->as_array();
    }

    /** Envios del usuario que aun no fueron recibidos (se pueden cancelar), por hoja de ruta. */
    public function enviosSinRecibir($id_user, array $nurs)
    {
        if (!$nurs) {
            return array();
        }
        $filas = DB::query(Database::SELECT, 'SELECT s.id, s.nur, s.nombre_receptor, s.cargo_receptor, s.a_oficina,
                    s.fecha_emision, s.oficial
                FROM seguimiento s
                WHERE s.derivado_por = :u AND s.estado = 1 AND s.nur IN :nurs
                ORDER BY s.id')
                ->param(':u', (int) $id_user)
                ->param(':nurs', array_values(array_unique($nurs)))
                ->execute();
        $envios = array();
        foreach ($filas as $f) {
            $envios[$f['nur']][] = $f;
        }
        return $envios;
    }

    /**
     * Quien tiene ahora cada hoja de ruta: los pasos oficiales abiertos (sin recibir o pendientes);
     * si no hay ninguno, el ultimo paso registrado (quien la archivo, agrupo o el ultimo receptor).
     * Devuelve array(nur => array(paso, ...)).
     */
    public function poseedoresActuales(array $nurs) {
        if (!$nurs) {
            return array();
        }
        $lista = implode(',', array_map(array(Database::instance(), 'escape'), array_unique($nurs)));
        $sql = "SELECT s.id, s.nur, s.oficial, s.estado, s.derivado_a, s.nombre_receptor, s.cargo_receptor, s.a_oficina,
                    s.nombre_emisor, s.fecha_emision, s.fecha_recepcion
                FROM seguimiento s
                WHERE s.nur IN ($lista)
                ORDER BY s.id ASC";
        $pasos = array();
        foreach (db::query(Database::SELECT, $sql)->execute() as $s) {
            $pasos[$s['nur']][] = $s;
        }
        $poseedores = array();
        foreach ($pasos as $nur => $lista_pasos) {
            $abiertos = array();
            foreach ($lista_pasos as $s) {
                if ((int) $s['oficial'] === 1 && in_array((int) $s['estado'], array(1, 2), TRUE)) {
                    $abiertos[] = $s;
                }
            }
            if (!$abiertos) {
                $ultimo = NULL;
                foreach ($lista_pasos as $s) {
                    if ((int) $s['oficial'] === 1) {
                        $ultimo = $s;
                    }
                }
                $abiertos[] = $ultimo ? $ultimo : end($lista_pasos);
            }
            $poseedores[$nur] = $abiertos;
        }
        return $poseedores;
    }

}

?>
