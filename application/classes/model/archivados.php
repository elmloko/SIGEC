<?php
defined('SYSPATH') or die ('no tiene acceso');
class Model_Archivados extends ORM{
    protected $_table_names_plural = false;
    public function archivar($id_nuri,$id_user,$id_carpeta,$fecha){
        $archivo=ORM::factory('archivados');
        $archivo->id_nuri=$id_nuri;
        $archivo->id_user=$id_user;
        $archivo->id_carpeta=$id_carpeta;
        $archivo->fecha=$fecha;
        return $archivo->save();
    }    
    public function carpeta($id_carpeta,$id_user)
    {
        $sql="SELECT s.id as id_seg,a.id_user, a.observaciones, s.id_archivo, c.carpeta, s.accion,s.oficial,s.de_oficina, s.proveido, a.fecha as fecha_archivo, d.id as id_doc, d.nur, d.codigo,d.referencia
            FROM archivados a USE INDEX (IDX_NUR)
            INNER JOIN carpetas c USE KEY (PRIMARY) ON a.id_carpeta=c.id
            INNER JOIN (SELECT * FROM seguimiento s USE INDEX (INDEX_DERIVADO_A__ESTADO) WHERE derivado_a='$id_user'
            and estado='10') as s ON a.nur=s.nur
            INNER JOIN documentos d USE INDEX (INDEX_NUR) ON s.nur=d.nur
            WHERE c.id='$id_carpeta'
            AND d.original='1'";
        return db::query(Database::SELECT, $sql)->execute(); 
    }
    public function carpeta_OLD($id_carpeta,$id_oficina)
    {
        $sql="SELECT s.id as id_seg,a.id_user, a.observaciones, s.id_archivo, c.carpeta, s.accion,s.oficial,s.de_oficina, s.proveido, a.fecha as fecha_archivo, d.id as id_doc, d.nur, d.codigo,d.referencia
            FROM seguimiento s
            INNER JOIN archivados a ON a.id=s.id_archivo
            INNER JOIN carpetas c ON a.id_carpeta=c.id
            INNER JOIN documentos d ON d.nur=s.nur
            WHERE s.estado='10'
            AND c.id='$id_carpeta'
            AND d.original='1'
            AND s.derivado_a='$id_oficina'";
        return db::query(Database::SELECT, $sql)->execute(); 
    }
}
?>
