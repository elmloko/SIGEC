<?php

defined('SYSPATH') or die('no tiene acceso');

//descripcion del modelo productos
class Model_Seguimiento extends ORM
{

    protected $_table_names_plural = false;

    //totales del sistema por estado, para el dashboard de administrador (sin filtrar por usuario)
    public function totalesSistema()
    {
        $sql = "SELECT e.id, e.estado AS titulo, e.plural, e.ui,
                (SELECT COUNT(*) FROM seguimiento s WHERE s.estado = e.id) AS cantidad
            FROM estados e
            ORDER BY e.id";
        return db::query(Database::SELECT, $sql)->execute();
    }

    //nueva forma de obtener estados para dashboard

    public function nestados($id_user)
    {
        /*
        $sql = "SELECT MAX(x.norecibido) AS norecibido,MAX(x.pendientes) as pendientes,MAX(x.archivo) as archivo,
                MAX(x.documentos) as documentos
                FROM (
                SELECT COUNT(*)  as norecibido,0 AS pendientes,0 as archivo,0 as documentos
                 FROM seguimiento WHERE derivado_a='$id_user' and estado='1'
                UNION
                SELECT 0  as norecibido,COUNT(*) AS pendientes,0 as archivo, 0 as documentos
                 FROM seguimiento WHERE derivado_a='$id_user' and estado='2'
                UNION
                SELECT 0  as norecibido,0 AS pendientes,COUNT(*)  as archivo, 0 as documentos
                 FROM seguimiento WHERE derivado_a='$id_user' and estado='10'
                UNION
                SELECT 0  as norecibido,0 AS pendientes,0 as archivo, COUNT(*)  as documentos
                FROM documentos WHERE id_user='$id_user'
                ) AS x";
        */

        /*
        $sql = "SELECT 
                    MAX(resultados.norecibido) AS norecibido,
                    MAX(resultados.pendientes) AS pendientes,
                    MAX(resultados.archivo) AS archivo,
                    MAX(resultados.documentos) AS documentos
                FROM
                    ((SELECT 
                        COUNT(1) AS norecibido,
                            0 AS pendientes,
                            0 AS archivo,
                            0 AS documentos
                    FROM
                        seguimiento s
                    INNER JOIN documentos AS d ON s.nur = d.nur
                    WHERE
                        (s.derivado_a = '$id_user')
                            AND (s.estado = '1')
                            AND (d.original = '1')) UNION ALL (SELECT 
                        0 AS norecibido,
                            COUNT(1) AS pendientes,
                            0 AS archivo,
                            0 AS documentos
                    FROM
                        seguimiento s
                    INNER JOIN documentos AS d ON s.nur = d.nur
                    WHERE
                        (s.derivado_a = '$id_user')
                            AND (s.estado = '2')
                            AND (d.original = '1')) UNION ALL (SELECT 
                        0 AS norecibido,
                            0 AS pendientes,
                            COUNT(1) AS archivo,
                            0 AS documentos
                    FROM
                        seguimiento s
                    INNER JOIN documentos AS d ON s.nur = d.nur
                    WHERE
                        (s.derivado_a = '$id_user')
                            AND (s.estado = '10')
                            AND (d.original = '1')) UNION ALL (SELECT 
                        0 AS norecibido,
                            0 AS pendientes,
                            0 AS archivo,
                            COUNT(*) AS documentos
                    FROM
                        documentos
                    WHERE
                        id_user = '$id_user')) AS resultados";
        */

        /*
        $sql = "SELECT 
                    MAX(CASE
                        WHEN resultado.estado = 1 THEN resultado.total
                    END) norecibido,
                    MAX(CASE
                        WHEN resultado.estado = 2 THEN resultado.total
                    END) pendientes,
                    MAX(CASE
                        WHEN resultado.estado = 10 THEN resultado.total
                    END) archivo,
                    MAX(CASE
                        WHEN resultado.estado = - 1 THEN resultado.total
                    END) documentos
                FROM
                    (
                        (
                            SELECT 
                                s.estado, COUNT(1) AS total
                            FROM
                                seguimiento s
                            WHERE
                                s.derivado_a = '$id_user'
                                    AND s.estado IN (1 , 2, 10)
                            GROUP BY estado)
                            UNION
                            (SELECT 
                                - 1 AS estado, COUNT(1) AS total
                            FROM
                                documentos
                            WHERE
                                id_user = '$id_user'
                        )
                    ) AS resultado";
        */

        /*
        $sql = "SELECT
                    MAX(resultados.norecibido) AS norecibido,
                    MAX(resultados.pendientes) AS pendientes,
                    MAX(resultados.archivo) AS archivo,
                    MAX(resultados.documentos) AS documentos
                FROM
                    ((SELECT
                        SUM(CASE
                                WHEN s.estado = '1' THEN 1
                                ELSE 0
                            END) norecibido,
                            SUM(CASE
                                WHEN s.estado = '2' THEN 1
                                ELSE 0
                            END) pendientes,
                            SUM(CASE
                                WHEN s.estado = '10' THEN 1
                                ELSE 0
                            END) archivo,
                            0 AS documentos
                    FROM
                        seguimiento s
                    INNER JOIN documentos AS d ON s.nur = d.nur
                    WHERE
                        (s.derivado_a = '$id_user')
                            AND (d.original = '1')
                    GROUP BY s.estado) UNION (SELECT
                        0 AS norecibido,
                            0 AS pendientes,
                            0 AS archivo,
                            COUNT(*) AS documentos
                    FROM
                        documentos
                    WHERE
                        id_user = '$id_user')) AS resultados";
        */

        /*
        $sql = "SELECT 
                    SUM(IF(s.estado = 1, '1', 0)) AS norecibido,
                    SUM(IF(s.estado = 2, '1', 0)) AS pendientes,
                    SUM(IF(s.estado = 10, '1', 0)) AS archivo,
                    (SELECT 
                            COUNT(1) AS documentos
                        FROM
                            documentos
                        WHERE
                            id_user = '$id_user') AS documentos
                FROM
                    seguimiento s
                        INNER JOIN
                    documentos AS d ON s.nur = d.nur
                WHERE
                    (s.estado IN (1 , 2, 10))
                        AND (d.original = '1')
                        AND (s.derivado_a = '$id_user')
                ORDER BY s.estado";
        */

        $sql = "SELECT 
                    SUM(IF(s.estado = 1, '1', 0)) AS norecibido,
                    SUM(IF(s.estado = 2, '1', 0)) AS pendientes,
                    SUM(IF(s.estado = 10, '1', 0)) AS archivo,
                    (
                        SELECT 
                            COUNT(1) AS documentos
                        FROM
                            documentos USE INDEX (INDEX_ID_USER)
                        WHERE
                            id_user = '$id_user'
                    ) AS documentos
                FROM
                    seguimiento s USE INDEX (INDEX_NUR , INDEX_DERIVADO_A__ESTADO)
                        INNER JOIN
                    documentos AS d USE INDEX (INDEX_NUR) ON s.nur = d.nur
                WHERE
                    (s.estado IN (1 , 2, 10))
                        AND (d.original = '1')
                        AND (s.derivado_a = '$id_user');";

        return db::query(Database::SELECT, $sql)->execute();
    }

    //nueva forma de obtener estados para dashboard

    public function nestadosVentanilla($id_user)
    {
        $sql = "SELECT SUM(x.recibido) as recibido,SUM(x.pendientes) as pendientes FROM 
                (SELECT COUNT(*)  as recibido,0 AS pendientes
                FROM documentos WHERE id_user='$id_user' 
                UNION
                SELECT 0 as recibido,COUNT(*) AS pendientes
                FROM documentos WHERE id_user='$id_user' AND estado='0'
                ) as x";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function jsonpendientesUser($id_user)
    {
        /*
        $sql = "SELECT 
                    1 AS suma,
                    s.id,
                    s.nur,
                    UPPER(s.nombre_emisor) AS nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    UPPER(s.de_oficina) AS de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    (s.derivado_a = '$id_user')
                        -- AND (s.id_a_oficina = '73')
                        AND s.estado IN (1 , 2)
                        -- AND RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                ORDER BY dias2 DESC";
        */

        $sql = "SELECT 
                    1 AS suma,
                    s.id,
                    s.nur,
                    UPPER(s.nombre_emisor) AS nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    UPPER(s.de_oficina) AS de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT 
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT 
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado,
                    IF(s.prioridad = 0,
                        'NO URGENTE',
                        'URGENTE') AS prioridad
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    (s.derivado_a = '$id_user')
                        -- AND (s.id_a_oficina = '73')
                        AND s.estado IN (1 , 2)
                        -- AND RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                ORDER BY dias2 DESC";

        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    //lista de correspondencia pendiente y no recibida
    public function pendientesUsers($id_oficina)
    {
        $sql = "SELECT u.id,COUNT(*) as cantidad,UPPER(o.oficina) as oficina,u.nombre,u.cargo,DATE_FORMAT(FROM_UNIXTIME(u.last_login), '%d-%m-%Y %H:%i:%s') as ultimo_ingreso ,IF(e.id>1,'pendiente','no_recibido') as estado 
                FROM  seguimiento s 
                INNER JOIN users u ON s.derivado_a=u.id
                INNER JOIN oficinas o ON u.id_oficina=o.id
                INNER JOIN estados e ON e.id=s.estado
                WHERE s.estado in (1,2)
                AND s.id_a_oficina ='$id_oficina'
                and u.habilitado='1'
                GROUP BY u.id,e.id ORDER BY u.nombre";
        return db::query(Database::SELECT, $sql)->execute();
    }

    // REPORTE PENDIENTES AGRUPADO POR OFICINA
    public function jsonTotalPendientesAgrupadoPorOficina()
    {
        $sql = "SELECT 
                    id_a_oficina AS id,
                    COUNT(1) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    s.nombre_receptor AS nombre,
                    s.cargo_receptor AS cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s,
                    users u
                WHERE
                    (s.estado IN (1 , 2))
                        AND (s.derivado_a = u.id)
                        AND EXISTS( SELECT 
                            *
                        FROM
                            sigec.documentos d
                        WHERE
                            d.nur = s.nur)
                        -- AND (YEAR(s.fecha_emision) > '2013')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                        -- AND (s.fecha_recepcion BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                GROUP BY s.id_a_oficina , s.estado
                ORDER BY cantidad DESC";

        return db::query(Database::SELECT, $sql)->execute();
    }

    public function jsonPendientesDeUsuariosAgrupadoPorOficinaId($id_oficina)
    {
        $sql = "SELECT 
                    *
                FROM
                    (SELECT 
                        1 AS suma,
                            s.id,
                            s.nur,
                            UPPER(s.nombre_receptor) AS nombre_receptor,
                            UPPER(s.cargo_receptor) AS cargo_receptor,
                            UPPER(s.de_oficina) AS de_oficina,
                            s.fecha_emision AS fecha,
                            s.fecha_recepcion AS fecha2,
                            YEAR(s.fecha_emision) AS gestion,
                            RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                            RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                            RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                            IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                            s.proveido,
                            (SELECT 
                                    codigo
                                FROM
                                    documentos
                                WHERE
                                    nur = s.nur
                                ORDER BY id
                                LIMIT 1) codigo,
                            (SELECT 
                                    referencia
                                FROM
                                    documentos
                                WHERE
                                    nur = s.nur
                                ORDER BY id
                                LIMIT 1) referencia,
                            a.accion,
                            IF(s.estado > 1, 'PENDIENTE', 'NO RECIBIDO') AS estado
                    FROM
                        seguimiento s
                    INNER JOIN acciones a ON s.accion = a.id, users u
                    WHERE
                        s.id_a_oficina = '$id_oficina'
                            AND s.estado IN (1 , 2)
                            AND (s.derivado_a = u.id)
                            -- AND (YEAR(s.fecha_emision) > '2013')
                            -- AND RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5
                            -- AND (YEAR(s.fecha_emision) = '2016')
                            -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                    ORDER BY dias2 DESC) consulta
                WHERE
                    consulta.codigo IS NOT NULL";

        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    // FIN (REPORTE PENDIENTES AGRUPADO POR OFICINA)

    // [INICIO] PENDIENTES CON RETRASO
    public function pendientesConRetrasoUsersTodo()
    {
        /*
        $sql = "SELECT 
                    u.id,
                    COUNT(*) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    UPPER(u.nombre) AS nombre,
                    UPPER(u.cargo) AS cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    users u ON s.derivado_a = u.id
                WHERE
                    (s.estado IN ('1' , '2'))
                        AND (YEAR(s.fecha_recepcion) = YEAR(NOW()))
                        AND (RESTA_FECHAS(s.fecha_recepcion, NOW()) >= 5)
                GROUP BY s.derivado_a , s.estado
                ORDER BY u.nombre;";
        */

        $sql = "SELECT 
                    s.derivado_a AS id,
                    COUNT(s.estado) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    UPPER(s.nombre_receptor) AS nombre,
                    UPPER(s.cargo_receptor) AS cargo,
                    YEAR(s.fecha_emision) AS gestion,
                    DATE_FORMAT(FROM_UNIXTIME(s.fecha_recepcion),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s USE INDEX (INDEX_ESTADO)
                WHERE
                    (s.estado IN ('1' , '2'))
                        AND (YEAR(s.fecha_emision) >= YEAR(NOW()) - 1)
                        AND IF(s.estado = 1,
                        (RESTA2_FECHAS(s.fecha_emision, NOW()) >= 5),
                        (RESTA2_FECHAS(s.fecha_recepcion, NOW()) >= 5))
                GROUP BY s.derivado_a , s.estado;";

        return db::query(Database::SELECT, $sql)->execute();
    }
    // [FIN] PENDIENTES CON RETRASO

    // [INICIO] DETALLE PENDIENTES CON RETRASO
    public function jsonPendientesConRetrasoUserId($id_user)
    {
        /*
        $sql = "SELECT
                    1 AS suma,
                    s.id,
                    s.nur,
                    UPPER(s.nombre_emisor) AS nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    UPPER(s.de_oficina) AS de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado,
                    IF(s.prioridad = 0,
                        'NO URGENTE',
                        'URGENTE') AS prioridad
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    (s.derivado_a = '$id_user')
                        AND (s.estado IN (1 , 2))
                        -- AND (YEAR(s.fecha_recepcion) = YEAR(NOW()))
                        AND (RESTA_FECHAS(s.fecha_recepcion, NOW()) >= 5)
                ORDER BY dias2 DESC";
        */

        $sql = "SELECT 
                    1 AS suma,
                    s.id,
                    s.nur,
                    UPPER(s.nombre_emisor) AS nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    UPPER(s.de_oficina) AS de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT 
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT 
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado,
                    IF(s.prioridad = 0,
                        'NO URGENTE',
                        'URGENTE') AS prioridad
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    (s.derivado_a = '$id_user')
                        AND (s.estado IN (1 , 2))
                        AND IF(s.estado = 1,
                        (RESTA2_FECHAS(s.fecha_emision, NOW()) >= 5),
                        (RESTA2_FECHAS(s.fecha_recepcion, NOW()) >= 5))
                HAVING gestion >= (YEAR(NOW()) - 1)
                ORDER BY dias2 DESC;";

        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    // [FIN] DETALLE PENDIENTES CON RETRASO

    public function pendientesUsersTodo()
    {
        $sql = "SELECT 
                    u.id,
                    COUNT(*) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    UPPER(u.nombre) AS nombre,
                    UPPER(u.cargo) AS cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    users u ON s.derivado_a = u.id
                WHERE
                    s.estado IN ('1' , '2')
                        -- AND (RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5)
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                GROUP BY s.derivado_a , s.estado
                ORDER BY u.nombre";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function totalPendientesDespacho()
    {
        $sql = "SELECT 
                    u.id,
                    COUNT(*) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    UPPER(u.nombre) AS nombre,
                    UPPER(u.cargo) AS cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    users u ON s.derivado_a = u.id
                WHERE
                    s.estado IN ('1' , '2')
                         AND (s.id_a_oficina = '73')
                        -- AND (RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5)
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                GROUP BY s.derivado_a , s.estado
                ORDER BY u.nombre";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function jsonPendientesDespachoUser($id_user)
    {
        $sql = "SELECT 
                    1 AS suma,
                    s.id,
                    s.nur,
                    UPPER(s.nombre_emisor) AS nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    UPPER(s.de_oficina) AS de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT 
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT 
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    (s.derivado_a = '$id_user')
                        AND (s.id_a_oficina = '73')
                        AND s.estado IN (1 , 2)
                        -- AND RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                ORDER BY dias2 DESC";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function totalPendientesFueraDePlazo()
    {
        /*
        $sql = "SELECT
                    u.id,
                    COUNT(*) AS cantidad,
                    UPPER(o.oficina) AS oficina,
                    u.nombre,
                    u.cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(e.id > 1, 'pendiente', 'no_recibido') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    users u ON s.derivado_a = u.id
                        INNER JOIN
                    oficinas o ON u.id_oficina = o.id
                        INNER JOIN
                    estados e ON e.id = s.estado
                WHERE
                    s.estado IN (1 , 2)
                GROUP BY u.id , e.id
                HAVING (cantidad > 5) AND (estado like 'pendiente')
                ORDER BY u.nombre";
        */
        // $sql = "SELECT
        //             u.id,
        //             COUNT(*) AS cantidad,
        //             of.oficina,
        //             u.nombre,
        //             u.cargo,
        //             DATE_FORMAT(FROM_UNIXTIME(u.last_login),
        //                     '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
        //             IF(e.id > 1, 'pendiente', 'no_recibido') AS estado
        //         FROM
        //             oficinas of,
        //             users u,
        //             estados e,
        //             (SELECT
        //                 s.*, DATEDIFF(s.fecha_recepcion, s.fecha_emision) AS dias
        //             FROM
        //                 seguimiento s, oficinas o
        //             WHERE
        //                 (s.estado = 2)
        //                     AND (s.id_a_oficina = o.id)
        //             HAVING dias >= 5
        //             ORDER BY dias) AS resultados
        //         WHERE
        //             (resultados.id_a_oficina = of.id)
        //                 AND (resultados.derivado_a = u.id)
        //                 AND (e.id = resultados.estado)
        //         GROUP BY of.id";

        $sql = "SELECT
                    u.id,
                    COUNT(*) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    UPPER(u.nombre) AS nombre,
                    UPPER(u.cargo) AS cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    users u ON s.derivado_a = u.id
                WHERE
                    s.estado IN ('1' , '2')
                        AND (RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5)
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                GROUP BY s.derivado_a, s.estado
                ORDER BY u.nombre";


        return db::query(Database::SELECT, $sql)->execute();
    }

    public function jsonPendientesPorUsuarioId($id_user)
    {
        $sql = "SELECT
                    1 AS suma,
                    s.id,
                    s.nur,
                    UPPER(s.nombre_emisor) AS nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    UPPER(s.de_oficina) AS de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    s.derivado_a = '$id_user'
                        AND s.estado IN (1 , 2)
                        AND RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5
                        -- AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')
                ORDER BY dias2 DESC";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function jsonTodosLosPendientesFueraDePlazo()
    {
        $sql = "SELECT 
                    1 AS suma,
                    s.id,
                    s.nur,
                    s.nombre_emisor,
                    s.cargo_emisor,
                    s.de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    YEAR(s.fecha_emision) AS gestion,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                    RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                    RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                    IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                    s.proveido,
                    (SELECT 
                            codigo
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) codigo,
                    (SELECT 
                            referencia
                        FROM
                            documentos
                        WHERE
                            nur = s.nur
                        ORDER BY id
                        LIMIT 1) referencia,
                    a.accion,
                    IF(s.estado > 1,
                        'PENDIENTE',
                        'NO RECIBIDO') AS estado
                FROM
                    seguimiento s
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    s.estado IN (1 , 2)
                        AND RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5
                        AND (YEAR(s.fecha_emision) = '2016')
                        -- AND (s.fecha_emision BETWEEN '2016-01-01 00:00:00' AND '2016-12-31 23:59:00')";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    // REPORTE TRAMITES FUERA DE PLAZO POR OFICINA
    public function jsonTotalFueraDePlazoPorOficina()
    {
        $sql = "SELECT 
                    id_a_oficina AS id,
                    COUNT(1) AS cantidad,
                    UPPER(s.a_oficina) AS oficina,
                    s.nombre_receptor AS nombre,
                    s.cargo_receptor AS cargo,
                    DATE_FORMAT(FROM_UNIXTIME(u.last_login),
                            '%d-%m-%Y %H:%i:%s') AS ultimo_ingreso,
                    IF(s.estado > 1,
                        'pendiente',
                        'no_recibido') AS estado
                FROM
                    seguimiento s,
                    users u
                WHERE
                    (s.estado IN (1 , 2))
                        AND (s.derivado_a = u.id)
                        AND (RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5)
                        AND YEAR(s.fecha_emision) = YEAR(NOW())
                        AND EXISTS( SELECT 
                            *
                        FROM
                            sigec.documentos d
                        WHERE
                            d.nur = s.nur)
                GROUP BY s.id_a_oficina , s.estado
                ORDER BY cantidad DESC";

        return db::query(Database::SELECT, $sql)->execute();
    }

    public function jsonFueraDePlazoPorOficinaId($id_oficina)
    {
        $sql = "SELECT 
                    *
                FROM
                    (SELECT 
                        1 AS suma,
                            s.id,
                            s.nur,
                            UPPER(s.nombre_receptor) AS nombre_receptor,
                            UPPER(s.cargo_receptor) AS cargo_receptor,
                            UPPER(s.de_oficina) AS de_oficina,
                            s.fecha_emision AS fecha,
                            s.fecha_recepcion AS fecha2,
                            YEAR(s.fecha_emision) AS gestion,
                            RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias,
                            RESTA2_FECHAS(s.fecha_emision, NOW()) AS dias2,
                            RESTA2_FECHAS(s.fecha_recepcion, NOW()) AS dias3,
                            IF(s.oficial > 0, 'OFICIAL', 'COPIA') AS oficial,
                            s.proveido,
                            (SELECT 
                                    codigo
                                FROM
                                    documentos
                                WHERE
                                    nur = s.nur
                                ORDER BY id
                                LIMIT 1) codigo,
                            (SELECT 
                                    referencia
                                FROM
                                    documentos
                                WHERE
                                    nur = s.nur
                                ORDER BY id
                                LIMIT 1) referencia,
                            a.accion,
                            IF(s.estado > 1, 'PENDIENTE', 'NO RECIBIDO') AS estado
                    FROM
                        seguimiento s
                    INNER JOIN acciones a ON s.accion = a.id, users u
                    WHERE
                        s.id_a_oficina = '$id_oficina'
                            AND s.estado IN (1 , 2)
                            AND (s.derivado_a = u.id)
                            AND (RESTA2_FECHAS(NOW(), s.fecha_emision) >= 5)
                            AND YEAR(s.fecha_emision) = YEAR(NOW())
                    ORDER BY dias2 DESC) consulta
                WHERE
                    consulta.codigo IS NOT NULL";

        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }
    // FIN REPORTE TRAMITES FUERA DE PLAZO POR OFICINA

    //lista de correspondencia pendiente y no recibida
    public function pendientesTodo()
    {
        $sql = "SELECT o.id,COUNT(*) as cantidad,UPPER(o.oficina) as oficina,IF(e.id>1,'pendiente','no_recibido') as estado FROM  seguimiento s INNER JOIN oficinas o ON s.id_a_oficina=o.id
                INNER JOIN estados e ON e.id=s.estado
                WHERE s.estado in (1,2)
                GROUP BY o.oficina,e.id";
        return db::query(Database::SELECT, $sql)->execute();
    }

    //protected $_sorting = array('fecha_publicacion' => 'DESC');
    //total de procesos generados por oficina
    public function genOficina()
    {

    }

    public function sql($sql)
    {
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function hrArchivada($nur, $user)
    {
        $sql = "SELECT c.id,c.carpeta,a.observaciones,nur FROM archivados a INNER JOIN carpetas c ON a.id_carpeta=c.id WHERE a.nur='$nur' AND a.id_user='$user'";
        $result = db::query(Database::SELECT, $sql)->execute();
        if (isset($result[0]['nur'])) {
            return $result[0];
        } else {
            return false;
        }
    }

    public function enviadosTodos($id, $fecha1, $fecha2)
    {
        $sql = "SELECT e.estado,s.nur,s.nombre_emisor,s.cargo_emisor,DATE_FORMAT(s.fecha_emision,'%d/%m/%Y %H:%i:%s') as fecha_emision,
                s.nombre_receptor,s.cargo_receptor,s.a_oficina, DATE_FORMAT(s.fecha_recepcion,'%d/%m/%Y %H:%i:%s') as  fecha_recepcion,s.proveido,
                IF(s.oficial=2,'Copia','Oficial') as oficial
                 FROM seguimiento s INNER JOIN estados e ON s.estado=e.id
                WHERE s.derivado_por='$id'
                AND s.fecha_emision between '$fecha1' and '$fecha2' 
                ORDER BY s.a_oficina, s.fecha_emision DESC ,s.nur,s.id_seguimiento";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function enviadosOficina($id, $ido, $fecha1, $fecha2)
    {
        $sql = "SELECT e.estado,s.nur,s.nombre_emisor,s.cargo_emisor,DATE_FORMAT(s.fecha_emision,'%d/%m/%Y %H:%i:%s') as fecha_emision,
                s.nombre_receptor,s.cargo_receptor,s.a_oficina, DATE_FORMAT(s.fecha_recepcion,'%d/%m/%Y %H:%i:%s') as  fecha_recepcion,s.proveido,
                IF(s.oficial=2,'Copia','Oficial') as oficial
                 FROM seguimiento s INNER JOIN estados e ON s.estado=e.id
                WHERE S.id_a_oficina='$ido'
                AND s.derivado_por='$id'
                AND s.fecha_emision between '$fecha1' and '$fecha2' 
                ORDER BY s.fecha_emision DESC ,s.nur,s.id_seguimiento";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function por_recibir($id_user, $fecha)
    {
        $sql = "SELECT id,nur,nombre_emisor,cargo_emisor,de_oficina FROM seguimiento
        WHERE derivado_a='$id_user'
        and fecha_emision > '$fecha' ";
        return db::query(Database::SELECT, $sql)->execute();
    }

    //function para insertar un seguimiento      
    public function insertSeguimiento($n, $oficial, $de, $nombre_de, $cargo_de, $para, $rem, $cargo_rem, $estado, $accion, $proveido, $adjunto, $de_oficina, $a_oficina, $padre)
    {
        $seg = ORM::factory('seguimiento');
        $seg->nur = $n;
        $seg->derivado_por = $de;
        $seg->padre = $padre;
        $seg->nombre_emisor = $nombre_de;
        $seg->cargo_emisor = $cargo_de;
        $seg->derivado_a = $para;
        $seg->nombre_receptor = $rem;
        $seg->cargo_receptor = $cargo_rem;
        $seg->fecha_emision = date('Y-m-d H:i:s');
        $seg->derivado_a = $para;
        $seg->nombre_receptor = $rem;
        $seg->cargo_receptor = $cargo_rem;
        // $seg->fecha_recepccion=date('00-00-00 00:00:00');
        $seg->estado = $estado;  // 1=no recibido
        $seg->accion = $accion;
        $seg->oficial = $oficial;
        $seg->proveido = $proveido;
        $seg->adjuntos = json_encode($adjunto);
        $seg->de_oficina = $de_oficina;
        $seg->a_oficina = $a_oficina;
        $seg->save();
        return $seg->id;
    }

    //estados
    public function estado_a($id_estado, $id_user)
    {
        $sql = "SELECT s.id, s.padre,s.nur as id_nur, s.nombre_emisor,s.cargo_emisor,s.de_oficina,s.fecha_emision as fecha, c.accion, s.oficial, s.hijo, s.proveido,s.adjuntos,s.archivos, n.nur
             , d.codigo, d.nombreDestinatario, d.cargoDestinatario, p.proceso
              FROM seguimiento s
              INNER JOIN nurs n ON n.id=s.nur
              INNER JOIN nurs_documentos nd ON nd.id_nur=s.nur
              INNER JOIN documentos d ON nd.id_documento=d.id
              INNER JOIN acciones c ON s.accion=c.id      
              INNER JOIN procesos p ON p.id=d.id_proceso                                                
              WHERE s.estado='$id_estado'
              AND derivado_a='$id_user'
              AND d.original='1'"; //importante para saber cual es el documento original
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    //estados mejorado en velocidad
    public function estado($id_estado, $id_user)
    {
        $sql = "SELECT  s.id, s.padre,s.hijo,s.id_seguimiento,s.nur, s.nombre_emisor,UPPER(s.cargo_emisor) as cargo_emisor,s.de_oficina,
                s.fecha_emision as fecha,s.fecha_recepcion as fecha2, a.accion, s.oficial, s.hijo, s.proveido,s.adjuntos,s.archivos
                , d.codigo, d.nombre_destinatario, d.cargo_destinatario,UPPER(d.referencia) as referencia,d.id as id_doc,s.prioridad
                FROM 
                (SELECT *
                FROM seguimiento WHERE derivado_a='$id_user' and estado='$id_estado') as s 
                INNER JOIN documentos as d ON s.nur=d.nur
                INNER JOIN acciones a ON s.accion=a.id
                WHERE d.original='1' ORDER BY s.fecha_emision DESC"; // important
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    //estados mejorado en velocidad
    public function entrada($id_user)
    {
        /*
        $sql = "SELECT
                    s.id,
                    s.estado,
                    s.padre,
                    s.hijo,
                    s.id_seguimiento,
                    s.nur,
                    s.nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    s.de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    a.accion,
                    s.oficial,
                    s.hijo,
                    s.proveido,
                    s.adjuntos,
                    s.archivos,
                    d.codigo,
                    d.nombre_destinatario,
                    d.cargo_destinatario,
                    d.referencia,
                    d.id AS id_doc,
                    s.prioridad,
                    RESTA2_FECHAS(NOW(), s.fecha_emision) AS dias
                FROM
                    (SELECT
                        *
                    FROM
                        seguimiento
                    WHERE
                        derivado_a = '$id_user' AND estado = '1') AS s
                        INNER JOIN
                    documentos AS d ON s.nur = d.nur
                        INNER JOIN
                    acciones a ON s.accion = a.id
                WHERE
                    d.original = '1'
                ORDER BY s.fecha_emision DESC"; // important
        */

        $sql = "SELECT 
                    s.id,
                    s.estado,
                    s.padre,
                    s.hijo,
                    s.id_seguimiento,
                    s.nur,
                    s.nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    s.de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    a.accion,
                    s.oficial,
                    s.hijo,
                    s.proveido,
                    s.adjuntos,
                    s.archivos,
                    d.codigo,
                    d.nombre_destinatario,
                    d.cargo_destinatario,
                    d.referencia,
                    d.id AS id_doc,
                    s.prioridad,
                    RESTA2_FECHAS(NOW(), s.fecha_emision) AS dias
                FROM
                    (SELECT 
                        *
                    FROM
                        seguimiento USE INDEX (INDEX_NUR , INDEX_DERIVADO_A__ESTADO)
                    WHERE
                        derivado_a = '$id_user' AND estado = '1') AS s
                        INNER JOIN
                    documentos AS d USE INDEX (INDEX_NUR) ON s.nur = d.nur
                        INNER JOIN
                    acciones a USE INDEX (INDEX_ID) ON s.accion = a.id
                WHERE
                    d.original = '1'
                ORDER BY s.fecha_emision DESC;"; // important

        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    //estados mejorado en velocidad
    public function pendiente($id_user)
    {
        /*
        $sql = "SELECT  s.id, s.padre,s.hijo,s.id_seguimiento,s.nur, s.nombre_emisor,UPPER(s.cargo_emisor) as cargo_emisor,s.de_oficina,
            s.fecha_emision as fecha,s.fecha_recepcion as fecha2, a.accion, s.oficial, s.hijo, s.proveido,s.adjuntos,s.archivos
             , d.codigo, d.nombre_destinatario, d.cargo_destinatario,d.referencia,d.id as id_doc,s.prioridad, RESTA2_FECHAS(NOW(),s.fecha_recepcion) as dias
                FROM 
                (SELECT *
                FROM seguimiento WHERE derivado_a='$id_user' and estado='2') as s 
                INNER JOIN documentos as d ON s.nur=d.nur
                INNER JOIN acciones a ON s.accion=a.id
                WHERE d.original='1' ORDER BY s.fecha_emision DESC"; // important
        */

        $sql = "SELECT 
                    s.id,
                    s.estado,
                    s.padre,
                    s.hijo,
                    s.id_seguimiento,
                    s.nur,
                    s.nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    s.de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    a.accion,
                    s.oficial,
                    s.hijo,
                    s.proveido,
                    s.adjuntos,
                    s.archivos,
                    d.codigo,
                    d.nombre_destinatario,
                    d.cargo_destinatario,
                    d.referencia,
                    d.id AS id_doc,
                    s.prioridad,
                    RESTA2_FECHAS(NOW(), s.fecha_recepcion) AS dias
                FROM
                    (
                        SELECT 
                            *
                        FROM
                            seguimiento USE INDEX (INDEX_NUR , INDEX_DERIVADO_A__ESTADO)
                        WHERE
                            derivado_a = '$id_user' AND estado = '2'
                    ) AS s
                        INNER JOIN
                    documentos AS d USE INDEX (INDEX_NUR) ON s.nur = d.nur
                        INNER JOIN
                    acciones a USE INDEX (INDEX_ID) ON s.accion = a.id
                WHERE
                    d.original = '1'
                ORDER BY s.fecha_emision DESC;"; // important

        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    //hojas de ruta archivadas de un usuario (estado=10), para el drill-down de estadisticas
    public function archivo($id_user)
    {
        $sql = "SELECT
                    s.id,
                    s.estado,
                    s.padre,
                    s.hijo,
                    s.id_seguimiento,
                    s.nur,
                    s.nombre_emisor,
                    UPPER(s.cargo_emisor) AS cargo_emisor,
                    s.de_oficina,
                    s.fecha_emision AS fecha,
                    s.fecha_recepcion AS fecha2,
                    a.accion,
                    s.oficial,
                    s.hijo,
                    s.proveido,
                    s.adjuntos,
                    s.archivos,
                    d.codigo,
                    d.nombre_destinatario,
                    d.cargo_destinatario,
                    d.referencia,
                    d.id AS id_doc,
                    s.prioridad,
                    RESTA2_FECHAS(NOW(), s.fecha_recepcion) AS dias
                FROM
                    (
                        SELECT
                            *
                        FROM
                            seguimiento USE INDEX (INDEX_NUR , INDEX_DERIVADO_A__ESTADO)
                        WHERE
                            derivado_a = '$id_user' AND estado = '10'
                    ) AS s
                        INNER JOIN
                    documentos AS d USE INDEX (INDEX_NUR) ON s.nur = d.nur
                        INNER JOIN
                    acciones a USE INDEX (INDEX_ID) ON s.accion = a.id
                WHERE
                    d.original = '1'
                ORDER BY s.fecha_recepcion DESC;";

        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    //estados mejorado en velocidad
    public function enviados($id_user)
    {
        $sql = "SELECT  s.id, s.padre,s.nur, s.nombre_receptor,s.cargo_receptor,s.a_oficina,s.fecha_emision as fecha, a.accion, s.oficial, s.hijo, s.proveido,s.adjuntos,s.archivos
             ,d.id as id_documento, d.codigo, d.cite_original, d.nombre_destinatario, d.cargo_destinatario, d.referencia,s.prioridad,RESTA2_FECHAS(NOW(),s.fecha_emision) as dias
                FROM 
                (SELECT *
                FROM seguimiento  USE INDEX (INDEX_DERIVADO_POR__ESTADO) WHERE derivado_por='$id_user' and estado='1') as s 
                INNER JOIN documentos as d USE INDEX (INDEX_NUR) ON s.nur=d.nur
                INNER JOIN acciones a USE KEY (PRIMARY) ON s.accion=a.id
                WHERE d.original='1' ORDER BY s.fecha_emision DESC";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function seguimientox($id)
    {
        $sql = "SELECT *
            FROM seguimiento s
            INNER JOIN asignados a ON s.nur=a.id
            INNER JOIN acciones c ON s.accion=c.id
            INNER JOIN estados e ON s.estado=e.id
            WHERE s.nur='$id'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function seguimiento($id)
    {
        /*
        $sql = "SELECT s.id as id, s.padre,s.nur, s.derivado_por,s.derivado_a,s.nombre_emisor,s.nombre_receptor,s.cargo_emisor,s.cargo_receptor,s.de_oficina,
            s.a_oficina,s.id_de_oficina,s.id_a_oficina,DATE_FORMAT(s.fecha_emision,'%Y-%m-%d') as fecha_emision,DATE_FORMAT(s.fecha_recepcion,'%Y-%m-%d') as fecha_recepcion,
            TIME_FORMAT(s.fecha_recepcion,'%H:%i:%s') as hora_recepcion,TIME_FORMAT(s.fecha_emision,'%H:%i:%s') as hora_emision,
            s.adjuntos, s.archivos, c.accion,e.id as id_estado,e.estado,s.oficial,s.hijo,s.proveido,
            IF(s.oficial=0,'default-light',IF(s.oficial>1,'default','primary-light')) as color,e.ui,r.username as u1,d.username as u2,r.genero as s1,d.genero as s2
            FROM (SELECT * 
                        FROM seguimiento 
            WHERE nur='$id'
              ) as s
            INNER JOIN acciones c ON s.accion=c.id
            INNER JOIN estados e ON s.estado=e.id 
            INNER JOIN users r ON s.derivado_por=r.id
            INNER JOIN users d ON s.derivado_a=d.id
            ORDER BY s.id ASC,s.fecha_emision ASC ";
        */

        $sql = "SELECT 
                    s.id AS id,
                    s.padre,
                    s.nur,
                    s.derivado_por,
                    s.derivado_a,
                    s.nombre_emisor,
                    s.nombre_receptor,
                    s.cargo_emisor,
                    s.cargo_receptor,
                    s.de_oficina,
                    s.a_oficina,
                    s.id_de_oficina,
                    s.id_a_oficina,
                    DATE_FORMAT(s.fecha_emision, '%Y-%m-%d') AS fecha_emision,
                    DATE_FORMAT(s.fecha_recepcion, '%Y-%m-%d') AS fecha_recepcion,
                    TIME_FORMAT(s.fecha_recepcion, '%H:%i:%s') AS hora_recepcion,
                    TIME_FORMAT(s.fecha_emision, '%H:%i:%s') AS hora_emision,
                    s.adjuntos,
                    s.archivos,
                    c.accion,
                    e.id AS id_estado,
                    e.estado,
                    s.oficial,
                    s.hijo,
                    s.proveido,
                    IF(s.oficial = 0,
                        'default-light',
                        IF(s.oficial > 1,
                            'default',
                            'primary-light')) AS color,
                    e.ui,
                    r.username AS u1,
                    d.username AS u2,
                    r.genero AS s1,
                    d.genero AS s2
                FROM
                    (SELECT 
                        *
                    FROM
                        seguimiento
                    WHERE
                        nur = '$id') AS s
                        INNER JOIN
                    acciones c ON s.accion = c.id
                        INNER JOIN
                    estados e ON s.estado = e.id
                        INNER JOIN
                    users r ON s.derivado_por = r.id
                        INNER JOIN
                    users d ON s.derivado_a = d.id
                ORDER BY s.fecha_emision ASC;";
                
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function archivado($nur)
    {
        $sql = "SELECT a.fecha,a.observaciones,c.carpeta,c.fecha_creacion,c.id,c.id_oficina FROM archivados a INNER JOIN carpetas c ON c.id=a.id_carpeta
              WHERE a.nur='$nur'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function pendientes($id_user, $e)
    {

        $sql = "SELECT s.id, s.padre,s.nombre_emisor,s.oficial,s.cargo_emisor,s.de_oficina,s.fecha_emision as fecha,s.proveido,h.id_nur, h.id_seguimiento, a.codigo as documento, d.codigo, p.proceso,c.accion,s.adjuntos
            FROM seguimiento s
        INNER JOIN hojasruta h ON h.id_nur=s.nur
            INNER JOIN documentos d ON h.id_documento=d.id
            INNER JOIN asignados a ON a.id=h.id_nur
            INNER JOIN acciones c ON s.accion=c.id      
        INNER JOIN  procesos p ON p.id=h.id_proceso                                             
            WHERE s.estado='$e'
            and s.derivado_a='$id_user'
            and h.id_seguimiento='-1'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function carpetas($id_user)
    {
        $sql = "SELECT id, carpeta FROM carpetas WHERE id_user='$id_user'";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function nuris($id_user)
    {
        $sql = "SELECT * FROM seguimiento s
        INNER JOIN nurs n ON s.nur=n.id
        INNER JOIN documentos d ON d.id_nur=s.nur
        WHERE n.original='$id_user'
        AND s.derivado_por='2'
        AND d.id_seguimiento='0'";
    }

    public function nur($id_seg)
    {
        $sql = "SELECT s.id, s.nur FROM seguimiento s             
             WHERE s.id='$id_seg'";
        return db::query(Database::SELECT, $sql)->execute();
    }

    //cantidad de estados 
    public function estados($id_user)
    {
        $sql = "SELECT * FROM (SELECT s.estado,COUNT(*) as n 
        FROM seguimiento s 
        WHERE s.derivado_a='$id_user'
                AND s.estado<>'4'   GROUP BY s.estado ) as s 
                INNER JOIN estados e ON s.estado=e.id";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function derivado($id_seguimiento)
    {
        $sql = "SELECT s.id, n.nur,d.codigo,d.referencia,s.nombre_receptor,s.cargo_receptor,s.a_oficina,s.fecha_emision,s.proveido 
            FROM seguimiento s
            INNER JOIN nurs n ON s.nur=n.nur
            INNER JOIN users u ON s.derivado_a=u.id
            INNER JOIN oficinas o ON u.id_oficina=o.id
            INNER JOIN documentos d ON d.nur=s.nur
            WHERE s.id='$id_seguimiento'
            and d.original='1'";
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function delete_deriv($padre)
    {
        $sql = "DELETE FROM seguimiento WHERE padre='$padre'";
        return db::query(Database::DELETE, $sql)->execute();
    }

    public function delete_principal($id)
    {
        $sql = "DELETE FROM seguimiento WHERE id= '$id'";
        return db::query(Database::DELETE, $sql)->execute();
    }

    public function pendientes2($sql)
    {
        return db::query(Database::SELECT, $sql)->execute();
    }

}

?>

