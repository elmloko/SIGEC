<?php

defined('SYSPATH') or die('no tiene acceso');

class Model_Alertas extends ORM {

    protected $_table_names_plural = false;

    public function lista($id) {
        $sql = "SELECT x.*,DATE_FORMAT(a.fecha,'%d-%m-%Y') as fecha FROM (SELECT id,nur,nombre_emisor,proveido FROM seguimiento
    where id IN (SELECT id_seguimiento FROM alertas WHERE id_user='$id'  ) and estado <= 2 ) as x
    inner join alertas a ON x.id=a.id_seguimiento";
        //echo $sql;
        return db::query(Database::SELECT, $sql)->execute();
    }

}

?>
