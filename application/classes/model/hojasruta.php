<?php

defined('SYSPATH') or die('no tiene acceso');

//descripcion del modelo productos
class Model_Hojasruta extends ORM {

    protected $_table_names_plural = false;

    //protected $_sorting = array('fecha_publicacion' => 'DESC');
    //mis hojas de ruta creadas por un usuario
    public function HRhijos($p) {
        $sql = "SELECT d.nur,d.referencia,d.cite_original,IF(s.oficial>0,'Oficial','Copia') as oficial,
            DATE_FORMAT(s.fecha_recepcion,'%d-%m-%Y %H:%i:%s') as fecha_recepcion
            FROM  (select hijo,fecha,id_seguimiento from agrupaciones where padre='$p') as x, documentos d,seguimiento s 
            WHERE d.nur=x.hijo
            AND x.id_seguimiento=s.id
            AND d.original='1'"; //important
        return db::query(Database::SELECT, $sql)->execute();
    }

    //ajsx hoja ruta imprimir
    public function hojaruta($like) {
        $sql = "SELECT id_user,nur FROM nurs where nur like '%$like%'
                order by fecha_creacion DESC
                limit 10"; //important
        return db::query(Database::SELECT, $sql)->execute();
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
            FROM seguimiento WHERE derivado_a='$id' and estado='2') as s 
            INNER JOIN documentos as d ON s.nur=d.nur
            INNER JOIN acciones a ON s.accion=a.id
            WHERE d.original='1' ORDER BY s.fecha_recepcion DESC"; //important
        return db::query(Database::SELECT, $sql)->execute();
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
        $sql = "SELECT * FROM agrupaciones a INNER  JOIN nurs n ON a.padre=n.nur WHERE a.padre='$nur'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function hijos($nur) {
        $sql = "SELECT * FROM seguimiento s 
            INNER JOIN nurs n ON s.nur=n.nur 
            INNER JOIN agrupaciones a ON s.id=a.id_seguimiento 
            WHERE a.padre='$nur'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function select($sql) {
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

}

?>
