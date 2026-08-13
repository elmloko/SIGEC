<?php

defined('SYSPATH') or die('no tiene acceso');

//descripcion del modelo productos
class Model_Tipos extends ORM {

    protected $_table_names_plural = false;
    protected $_has_many = array(
        'documentos' => array(
            'model' => 'documentos',
            'foreign_key' => 'id_tipo'
        ),
        'tipo' => array(
            'through' => 'tipo_oficina',
            'foreign_key' => 'id_tipo',
            'far_key' => 'id_oficina',
        ),
        'user' => array(
            'model' => 'users',
            'through' => 'usertipo',
            'foreign_key' => 'id_tipo',
            'far_key' => 'id_user',
        ),
    );

//documentos que un usuario puede crear    
    public function misTipos($id) {

        /*
        $sql = "SELECT t.id, t.tipo,t.action,t.plural,t.descripcion FROM tipos t 
        INNER JOIN usertipo u ON u.id_tipo=t.id
        WHERE u.id_user='$id'";
        */
        $sql = "SELECT 
                    t.id, t.plural, t.action AS accion, t.descripcion, t.tipo
                FROM
                    tipos t
                        INNER JOIN
                    usertipo ut ON (t.id = ut.id_tipo)
                WHERE
                    (t.activo = 1) AND (ut.id_user = '$id');";

        //return $this->_db->query(Database::SELECT, $sql, TRUE);
        return db::query(Database::SELECT, $sql)->execute();
    }

    public function tipoUsuario($id) {
        $sql = "SELECT id,tipo FROM tipos WHERE id IN (select id_tipo from usertipo where id_user='$id')";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }
    public function quitar($id) {
        $sql = "DELETE  FROM usertipo where id_user='$id'";
        return db::query(Database::DELETE, $sql)->execute();
    }

    public function lista($id) {
        $sql = "SELECT d.id,d.plural,SUM(cc) as cantidad,d.action as accion,d.descripcion,d.tipo FROM 
                (SELECT t.id,t.plural,0 as cc,t.action,t.descripcion,t.tipo FROM usertipo u INNER JOIN tipos t ON u.id_tipo=t.id
                WHERE u.id_user='$id'
                UNION
                SELECT t.id,t.plural,COUNT(*) as cc,t.action,t.descripcion,t.tipo FROM documentos d INNER JOIN tipos t ON d.id_tipo=t.id
                WHERE id_user='$id'
                GROUP BY t.id ) as d
                WHERE d.id <> 6
                GROUP BY d.id";
        return db::query(Database::SELECT, $sql)->execute();
    }

}

?>
