<?php
defined('SYSPATH') or die ('no tiene acceso');

class Model_Eventos extends ORM{
    protected $_table_names_plural = false;
    //obtener los eventos dato dos fechas
    
    public function lista($fecha_inicio,$fecha_final)
    {
        $sql="SELECT * FROM eventos WHERE fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_final'";
        return db::query(Database::SELECT, $sql)->execute();
    }
}
?>
