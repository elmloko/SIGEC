<?php
defined('SYSPATH') or die ('no tiene acceso');
//descripcion del modelo productos
class Model_niveles extends ORM{
    protected $_table_names_plural = false;    
    public function menus($n){
        $sql="SELECT m.id, m.menu, m.descripcion, m.controlador,s.id as id_submenu  ,s.submenu,s.accion,s.descripcion,m.logo FROM menus m
        INNER JOIN nivelmenu n ON m.id=n.id_menu 
        INNER JOIN submenus s ON m.id=s.id_menu
        WHERE n.id_nivel='$n'
        AND s.habilitado='1'
        ORDER BY m.index";
        return $this->_db->query(Database::SELECT, $sql,TRUE);        
    }
    
    
}

?>
