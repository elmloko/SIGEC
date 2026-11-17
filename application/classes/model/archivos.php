<?php

defined('SYSPATH') or die('no tiene acceso');

class Model_Archivos extends ORM {

    protected $_table_names_plural = false;

    public function listar($id_user) {
        $id_user = SqlSafe::value($id_user); // se pega en el SQL entre comillas
        $sql = "SELECT a.id,d.id as id_documento,d.codigo,d.cite_original,a.fecha, t.tipo,d.nur,d.referencia,a.tamanio,a.nombre_archivo,a.extension
                FROM archivos a INNER JOIN documentos d ON a.id_documento=d.id
                INNER JOIN tipos t ON d.id_tipo=t.id
                WHERE a.id_user='$id_user'
                AND a.estado='1'
                ORDER BY a.fecha DESC";
        return db::query(Database::SELECT, $sql)->execute();
    }

    // ordenes permitidos en "Archivos digitales" (clave del GET => ORDER BY)
    public static $ordenes = array(
        'fecha-desc' => 'a.fecha DESC, a.id DESC',
        'fecha-asc' => 'a.fecha ASC, a.id ASC',
        'nombre-asc' => 'SUBSTRING(a.nombre_archivo, 14) ASC',
        'tamanio-desc' => '(a.tamanio + 0) DESC',
    );

    /**
     * Filtros de "Archivos digitales" leidos del GET y saneados:
     * q (texto), desde / hasta (AAAA-MM-DD), orden (clave de $ordenes), pagina.
     */
    public static function filtros(array $get) {
        $es_fecha = function ($v) {
            return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) !== FALSE;
        };
        $desde = (string) Arr::get($get, 'desde', '');
        $hasta = (string) Arr::get($get, 'hasta', '');
        $orden = (string) Arr::get($get, 'orden', '');
        return array(
            'q' => mb_substr(trim((string) Arr::get($get, 'q', '')), 0, 100),
            'desde' => $es_fecha($desde) ? $desde : '',
            'hasta' => $es_fecha($hasta) ? $hasta : '',
            'orden' => isset(self::$ordenes[$orden]) ? $orden : 'fecha-desc',
            'pagina' => max(1, (int) Arr::get($get, 'pagina', 1)),
        );
    }

    // FROM + WHERE comun (archivos activos del usuario, con texto y fechas)
    private function desde_donde($id_user, array $f) {
        $sql = " FROM archivos a
                INNER JOIN documentos d ON a.id_documento = d.id
                INNER JOIN tipos t ON d.id_tipo = t.id
                WHERE a.id_user = :u AND a.estado = '1'";
        $params = array(':u' => (int) $id_user);
        if ($f['desde'] !== '') {
            $sql .= ' AND a.fecha >= :desde';
            $params[':desde'] = $f['desde'] . ' 00:00:00';
        }
        if ($f['hasta'] !== '') {
            $sql .= ' AND a.fecha < DATE_ADD(:hasta, INTERVAL 1 DAY)';
            $params[':hasta'] = $f['hasta'];
        }
        // cada palabra debe aparecer en el nombre, cite, codigo, referencia, hoja de ruta o tipo
        $palabras = $f['q'] === '' ? array() : array_slice(preg_split('/\s+/u', $f['q']), 0, 6);
        foreach ($palabras as $i => $p) {
            $sql .= " AND CONCAT_WS(' ', a.nombre_archivo, d.cite_original, d.codigo, d.referencia, d.nur, t.tipo) LIKE :p$i";
            $params[":p$i"] = '%' . str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $p) . '%';
        }
        return array($sql, $params);
    }

    // cantidad de archivos que cumplen los filtros
    public function contar($id_user, array $f) {
        list($desde, $params) = $this->desde_donde($id_user, $f);
        return (int) DB::query(Database::SELECT, 'SELECT COUNT(*) AS n' . $desde)->parameters($params)->execute()->get('n');
    }

    // una pagina de archivos con los datos de su documento
    public function buscar($id_user, array $f, $offset, $limite) {
        list($desde, $params) = $this->desde_donde($id_user, $f);
        $sql = 'SELECT a.id, d.id AS id_documento, d.codigo, d.cite_original, a.fecha, t.tipo, d.nur, d.referencia,
                       a.tamanio, a.nombre_archivo, a.extension' . $desde
                . ' ORDER BY ' . self::$ordenes[$f['orden']]
                . ' LIMIT ' . (int) $offset . ', ' . (int) $limite;
        return DB::query(Database::SELECT, $sql)->parameters($params)->execute()->as_array();
    }

    // resumen de todos los archivos del usuario (sin filtros): total, PDF, este mes, este año y espacio
    public function resumen($id_user) {
        $sql = "SELECT COUNT(*) AS total,
                       SUM(a.extension LIKE '%pdf%' OR a.nombre_archivo LIKE '%.pdf') AS pdf,
                       SUM(a.fecha >= :mes) AS mes,
                       SUM(a.fecha >= :anio) AS anio,
                       SUM(a.tamanio + 0) AS bytes
                FROM archivos a
                INNER JOIN documentos d ON a.id_documento = d.id
                WHERE a.id_user = :u AND a.estado = '1'";
        return DB::query(Database::SELECT, $sql)
                        ->param(':u', (int) $id_user)
                        ->param(':mes', date('Y-m-01'))
                        ->param(':anio', date('Y-01-01'))
                        ->execute()->current();
    }

    /**
     * Todo lo que necesita la vista documentos/archivos: filtros, resumen, una pagina de archivos y el paginado.
     */
    public function pagina_vista($id_user, array $get, $por_pagina = 20) {
        $f = self::filtros($get);
        $total = $this->contar($id_user, $f);
        $paginas = max(1, (int) ceil($total / $por_pagina));
        $f['pagina'] = min($f['pagina'], $paginas);
        return array(
            'results' => $this->buscar($id_user, $f, ($f['pagina'] - 1) * $por_pagina, $por_pagina),
            'resumen' => $this->resumen($id_user),
            'f' => $f,
            'total' => $total,
            'paginas' => $paginas,
            'por_pagina' => $por_pagina,
        );
    }

}

?>
