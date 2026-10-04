<?php
defined('SYSPATH') or die ('no tiene acceso');

class Model_Eventos extends ORM{
    protected $_table_names_plural = false;
    //obtener los eventos dato dos fechas
    
    public function lista($fecha_inicio,$fecha_final)
    {
        $fecha_inicio = SqlSafe::value($fecha_inicio); // se pega en el SQL entre comillas
        $fecha_final = SqlSafe::value($fecha_final); // se pega en el SQL entre comillas
        $sql="SELECT * FROM eventos WHERE fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_final'";
        return db::query(Database::SELECT, $sql)->execute();
    }
}
?>
