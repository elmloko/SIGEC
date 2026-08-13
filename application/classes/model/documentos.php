<?php

defined('SYSPATH') or die('no tiene acceso');

//descripcion del modelo productos
class Model_Documentos extends ORM {

    protected $_table_names_plural = false;
    //un documento pertenece a un:
    protected $_belongs_to = array(
        'tipos' => array(
            'model' => 'tipos',
            'foreign_key' => 'id_tipo'
        ),
        'usuario' => array(
            'model' => 'users',
            'foreign_key' => 'id_user'
        ),
    );
    //nuris - documentos
    protected $_has_many = array(
        'nurs' => array(
            'model' => 'nurs',
            'through' => 'nurs_documentos',
            'foreign_key' => 'id_documento',
            'far_key' => 'nur',
        ),
    );

    public function generados($id){
        $sql="SELECT d.cantidad,t.plural as documento FROM (select count(*) as cantidad,id_tipo from documentos where id_user='$id'
group by id_tipo ) as d INNER JOIN tipos t ON d.id_tipo=t.id";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }
    
    
    public function actividad($id) {
        $sql = "SELECT count(*) as cantidad,UNIX_TIMESTAMP(fecha_creacion)*1000 as time  FROM documentos where id_user='$id'
        GROUP by YEAR(fecha_creacion),MONTH(fecha_creacion),DAY(fecha_creacion)";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }
    public function derivados($id) {
        $sql = "SELECT count(*) as cantidad,UNIX_TIMESTAMP(fecha_emision)*1000 as time,fecha_emision  FROM seguimiento
 where derivado_por='$id'
        GROUP by YEAR(fecha_emision),MONTH(fecha_emision),DAY(fecha_emision)";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    //tipo documentos
    public function tipoDocumentos($id) {
        $sql = "SELECT id_tipo,count(*) as cantidad 
                FROM documentos 
                WHERE id_tipo in (select id_tipo from usertipo where id_user='$id')
                AND id_user='$id'
                GROUP BY id_tipo";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function historial() {
        $sql = "SELECT CONCAT(UNIX_TIMESTAMP(DATE_FORMAT(fecha_creacion,'%Y-%m-%d 00:00:00')),'000') as fecha,COUNT(1) as a FROM documentos
                WHERE fecha_creacion IS NOT NULL
                GROUP BY DATE_FORMAT(fecha_creacion,'%Y-%m-%d 00:00:00')";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function tdocumentos($id_oficina) {
        $sql = "SELECT  COUNT(*) as cantidad, UPPER(t.plural) as documento, t.id FROM documentos d INNER JOIN tipos t ON t.id=d.id_tipo
                WHERE d.id_oficina='$id_oficina'
                AND t.abreviatura IS NOT NULL
                GROUP BY t.id";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    //nuevo 
    public function zdoc($id) {
        $sql = "SELECT x.id,x.referencia,x.nombre_destinatario,x.cargo_destinatario,t.tipo,DATE_FORMAT(x.fecha_creacion,'%d-%m-%Y') as fecha_creacion,x.nur,cite_original FROM
                (select * from documentos where  id_user='$id'  order by fecha_creacion DESC limit 10 ) as x 
                INNER JOIN tipos t ON x.id_tipo=t.id";
        return DB::query(Database::SELECT, $sql)->execute();
    }

    public function ejecutarsql($sql) {
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function ejecutarsql_array($sql) {
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function ejecutarsql_object($sql) {
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function seguimiento($nur) {
        $sql = "SELECT 1 as suma,IF(s.oficial,'OFICIAL','COPIA') as ofi,'-->' as flecha, s.*,e.* FROM seguimiento s INNER JOIN estados as e ON s.estado=e.id
            WHERE nur='$nur'";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    //el reporte
    public function reporte() {
        $sql = "SELECT 1 as suma,s.id,IF(s.oficial,'Oficial','Copia') as oficial,s.a_oficina, d.nur,d.cite_original,d.nombre_remitente,d.cargo_remitente,d.institucion_remitente,d.referencia,
                DATE_FORMAT(d.fecha_creacion,'%d/%m/%Y') as fecha_creacion,s.nombre_receptor,s.cargo_receptor,
                DATE_FORMAT(s.fecha_emision,'%d/%m/%Y') as fecha_emision,
                DATE_FORMAT(s.fecha_recepcion,'%d/%m/%Y') as fecha_recepcion,
                e.estado
                FROM documentos d INNER JOIN seguimiento s ON d.nur=s.nur
                INNER JOIN estados e ON s.estado=e.id
                WHERE s.estado IN (1,2,10)
                AND d.id_oficina='48'
                AND d.id_user in (37,39) 
                AND d.codigo like 'D%' ORDER BY nur";

        $A = "UNION
                SELECT 1 as suma,d.id,d.nur,d.cite_original,d.nombre_remitente,d.cargo_remitente,d.institucion_remitente,d.referencia,
                DATE_FORMAT(d.fecha_creacion,'%d/%m/%Y') as fecha_creacion,
                '' as nombre_receptor,'' as cargo_receptor,
                '' as fecha_emision,
                '' as fecha_recepcion,
                'No derivado' as estado
                FROM documentos d 

                WHERE d.estado='0'
                AND d.id_oficina='48'
                AND d.id_user in (37,39)
                AND d.codigo like 'D%'
                ORDER BY nur";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function documentosTipo($id) {
        $sql = "SELECT COUNT(t.plural) as cantidad,t.plural as  documento,CONCAT(m.mes_corto,' ',YEAR(d.fecha_creacion)) as fecha 
                FROM documentos d 
                INNER JOIN tipos t ON d.id_tipo=t.id
                INNER JOIN meses m ON MONTH(d.fecha_creacion)=m.id
                WHERE D.id_oficina='$id'
                GROUP BY t.id,CONCAT(MONTH(d.fecha_creacion),'/',YEAR(d.fecha_creacion))
                ORDER BY YEAR(d.fecha_creacion),MONTH(d.fecha_creacion)";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public function agrupados($id) {
        $sql = "SELECT id_tipo, COUNT(*) as n FROM documentos WHERE id_user='$id' GROUP BY id_tipo";
        return DB::query(1, $sql)->execute();
    }

    public function agrupaciones($id, $o, $i) {
        $sql = "SELECT a.padre,a.hijo,a.fecha, d.cite_original,d.cargo_destinatario,d.nombre_destinatario,d.referencia FROM agrupaciones a
        INNER JOIN documentos d ON a.hijo=d.nur
        WHERE d.original=1
        AND a.id_user='$id'
        ORDER by a.fecha DESC
        LIMIT $o,$i";
        return DB::query(1, $sql)->execute();
    }

    //ultimos 10 documentos generados
    public function recientes($id) {
        $sql = "SELECT d.id,d.codigo,d.nombre_destinatario,d.cargo_destinatario,d.nombre_via,d.cargo_via,d.nombre_remitente,d.cargo_remitente,d.fecha_creacion,d.referencia,d.nur,t.tipo,d.estado
            FROM documentos d            
            INNER JOIN tipos t ON t.id=d.id_tipo
            WHERE d.id_user='$id'
            ORDER BY d.fecha_creacion DESC
            LIMIT 10";
        return $this->_db->query(1, $sql);
    }

    //lista de documentos
    public function documentos() {
        $sql = "SELECT c.id, c.titulo, c.nombre_documento, t.tipo
              FROM documentos c LEFT JOIN  tipos t ON c.id_tipo=t.id";
        return DB::query(1, $sql)->execute();
    }

    public function detalle($id, $user) {
        $sql = "SELECT d.id,d.codigo,d.id_tipo,t.plural,d.nombreDestinatario,d.cargoDestinatario,d.nombreVia,d.cargoVia,d.nombreRemitente,d.cargoRemitente,d.fecha_creacion,d.referencia,a.codigo as hr, t.tipo,d.contenido,d.id_archivo
            FROM documentos d
            INNER JOIN hojasruta h ON h.id_documento=d.id
            INNER JOIN asignados a ON a.id=h.id_nur
            INNER JOIN tipos t ON t.id=d.id_tipo
            WHERE d.id='$id'
            AND d.id_user='$user'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function vista($codigo) {
        $sql = "SELECT d.id,d.codigo,d.nombreDestinatario,d.cargoDestinatario,d.nombreVia,d.cargoVia,d.nombreRemitente,d.cargoRemitente,d.fecha_creacion,d.referencia,a.codigo as hr, t.tipo,d.contenido,d.id_archivo
            FROM documentos d
            INNER JOIN hojasruta h ON h.id_documento=d.id
            INNER JOIN asignados a ON a.id=h.id_nur
            INNER JOIN tipos t ON t.id=d.id_tipo
            WHERE d.codigo='$codigo'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function descripcion($nur) {
        $sql = "SELECT d.id as id_doc, d.codigo as documento, d.nombre_destinatario as destinatario, d.cargo_destinatario as cargo, d.referencia, d.nombre_remitente as remitente, d.cargo_remitente as cargo_r,
        h.fecha, a.codigo as nur,t.tipo, p.proceso FROM documentos d
        INNER JOIN hojasruta h ON h.id_documento=d.id
        INNER JOIN asignados a ON h.nur=a.id
        INNER JOIN tipos t ON t.id=d.id_tipo
        INNER JOIN procesos p ON p.id=h.id_proceso
        WHERE h.nur='$nur'
        and h.id_seguimiento='-1'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function documento($id, $id_user) {
        $sql = "SELECT d.id as id_doc, d.id_tipo, d.codigo as documento, d.nombreDestinatario as destinatario, d.cargoDestinatario as cargo, d.referencia, d.nombreRemitente as remitente, d.cargoRemitente as cargo_r,
      d.nombreVia,d.cargoVia,  h.fecha, a.codigo as nur,t.tipo, p.proceso FROM documentos d
        INNER JOIN hojasruta h ON h.id_documento=d.id
        INNER JOIN asignados a ON h.id_nur=a.id
        INNER JOIN tipos t ON t.id=d.id_tipo
        INNER JOIN procesos p ON p.id=h.id_proceso
        WHERE d.id='$id'       
        and d.id_user='$id_user'";
        return $this->_db->query(Database::SELECT, $sql, TRUE);
    }

    public function externos($id_user, $o, $i) {
        $sql = "SELECT d.id, d.codigo, d.citeOriginal, d.estado, d.nombreDestinatario,d.cargoDestinatario, d.institucionDestinatario, d.nombreRemitente,d.cargoRemitente,d.institucionRemitente, d.referencia,d.adjuntos, d.nroHojas, d.nur, d.id_nur,de.fecha, g.grupo,m.motivo,p.proceso FROM documentos d 
            INNER JOIN descripcion de ON d.id=de.id_documento
            INNER JOIN grupos g ON de.id_grupo=g.id
            INNER JOIN motivos m ON de.id_motivo=m.id
            INNER JOIN procesosx p ON de.id_proceso=p.id
            WHERE d.id_user='$id_user'
            AND d.id_tipo='6'             
            ORDER BY d.fecha_creacion DESC
            LIMIT $o,$i"; //6=documentos externos
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function documentos_nuevos($id_user) {
        $sql = "SELECT * FROM tipos t 
        WHERE t.id NOT IN (SELECT id_tipo FROM usertipo WHERE id_user='$id_user')
        and t.doc='0'";
        return $this->_db->query(database::SELECT, $sql, TRUE);
    }

    //busqueda
    public function contarHR($text, $entidad) {

        $sql = "SELECT COUNT(*) as count
                   FROM documentos d 
                   WHERE d.nur LIKE '%$text%' ";

        return db::query(Database::SELECT, $sql)->execute();
    }

    public function contar($text, $entidad) {

        $sql = "SELECT COUNT(*) as count
                   FROM documentos d 
                   WHERE d.nombre_remitente LIKE '%$text%'  
                   OR  d.cargo_remitente LIKE '%$text%'  
                   OR d.nombre_destinatario LIKE '%$text%'  
                   OR d.cargo_destinatario LIKE '%$text%' 
                   OR d.referencia LIKE '%$text%' 
                   OR d.cite_original LIKE '%$text%' 
                   OR d.nur LIKE '%$text%' 
                   OR d.institucion_remitente LIKE '%$text%' 
                   OR d.adjuntos LIKE '%$text%'";

        return db::query(Database::SELECT, $sql)->execute();
    }

    public function contar2($where) {
        $sql = "SELECT COUNT(*) as count  FROM documentos  d ";
        $sql.=$where;
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function buscar($where, $o, $i, $entidad) {

        $sql = "SELECT UPPER(t.tipo) as tipo,
            d.cite_original,
            d.nur,
            d.nombre_destinatario,d.cargo_destinatario,
            d.nombre_remitente,
            d.cargo_remitente,
            d.referencia,d.estado,d.original
            ,DATE_FORMAT(d.fecha_creacion,'<b>%d/%m/%Y </b><br/> %H:%i:%s') as fecha_creacion,
            d.institucion_remitente ,d.adjuntos
                  FROM documentos d INNER JOIN tipos t ON d.id_tipo=t.id
                    $where
                LIMIT $o,$i";
        echo $sql;
        return db::query(Database::SELECT, $sql)->execute();
    }

    //busqueda de hojas de ruta
    public function buscarHR($text, $o, $i, $entidad) {

        $sql = "SELECT UPPER(t.tipo) as tipo,
            d.cite_original,
            d.nur,
            d.nombre_destinatario,d.cargo_destinatario,
            d.nombre_remitente,
            d.cargo_remitente,
            d.referencia,d.estado,d.original
            ,DATE_FORMAT(d.fecha_creacion,'<b>%d/%m/%Y </b><br/> %H:%i:%s') as fecha_creacion,
            d.institucion_remitente ,d.adjuntos
                  FROM documentos d INNER JOIN tipos t ON d.id_tipo=t.id
                   WHERE 
                    d.nur LIKE '%$text%' 
                LIMIT $o,$i";
        return db::query(Database::SELECT, $sql)->execute();
    }

    //
    public function search($where, $o, $i)
    {
        $sql = "SELECT 
                    d.institucion_remitente,
                    d.id,
                    d.nur,
                    d.cite_original,
                    d.nombre_destinatario,
                    d.cargo_destinatario,
                    d.nombre_remitente,
                    d.cargo_remitente,
                    d.referencia,
                    d.fecha_creacion,
                    t.tipo
                FROM
                    documentos d
                        INNER JOIN
                    tipos t ON d.id_tipo = t.id ";
        $sql .= $where . " ORDER BY d.fecha_creacion DESC , d.id_tipo ASC LIMIT $o , $i";

        return db::query(Database::SELECT, $sql)->execute();
    }

    /* MUESTRA EL TOTAL DE RESULTADOS SIN LIMIT, PARA SACAR EL REPORTE EXCEL */
    public function search_all($where)
    {
        $sql = "SELECT 
                    d.institucion_remitente,
                    d.id,
                    d.nur,
                    d.cite_original,
                    d.nombre_destinatario,
                    d.cargo_destinatario,
                    d.nombre_remitente,
                    d.cargo_remitente,
                    d.referencia,
                    d.fecha_creacion,
                    t.tipo
                FROM
                    documentos d
                        INNER JOIN
                    tipos t ON d.id_tipo = t.id ";
        //$sql .= $where;
        $sql .= $where;

        return db::query(Database::SELECT, $sql)->execute();
    }

    public function pendiente_ventanilla($id_user) {
        $sql = "SELECT d.id, d.cite_original, d.nur, d.nombre_destinatario,d.cargo_destinatario,d.institucion_destinatario,d.nombre_remitente,d.cargo_remitente,d.institucion_remitente,d.referencia,d.hojas,d.fecha_creacion,d.estado
            FROM documentos d 
            INNER JOIN descripcion de ON d.id=de.id_documento            
            WHERE d.id_user='$id_user'
            AND d.estado='0'";
        return db::query(Database::SELECT, $sql)->execute();
    }

    /* Correlativos */

    public function correlativos($id_oficina) {
        $sql = "SELECT t.plural,c.correlativo,t.id FROM correlativo c INNER JOIN oficinas o ON c.id_oficina=o.id
                INNER JOIN tipos t ON c.id_tipo=t.id
                WHERE c.id_oficina='$id_oficina'";
        return db::query(Database::SELECT, $sql)->execute();
    }

    /* documentos generado por oficina */

    public function documentos_generados($id_oficina) {
        $sql = "SELECT COUNT(*) as cantidad,t.plural,t.id FROM documentos d INNER JOIN tipos t ON d.id_tipo=t.id
        INNER JOIN oficinas o on o.id=d.id_oficina
        WHERE d.id_oficina='$id_oficina'
        GROUP BY t.action";
        return db::query(Database::SELECT, $sql)->execute();
    }

}

?>
