<?php
defined('SYSPATH') or die ('no tiene acceso');
//descripcion del modelo productos
class Model_niveles extends ORM{
    protected $_table_names_plural = false;    
    // nivel del rol administrador (unico que ve el menu de Reportes)
    const NIVEL_ADMIN = 5;

    public function menus($n){
        $n = (int) $n;
        // Reportes (controlador 'reports') solo para el administrador, aunque nivelmenu lo asigne a otros niveles
        if ($n === self::NIVEL_ADMIN) {
            $filtro = "(m.id IN (SELECT id_menu FROM nivelmenu WHERE id_nivel = $n) OR m.controlador = 'reports')";
        } else {
            $filtro = "m.id IN (SELECT id_menu FROM nivelmenu WHERE id_nivel = $n) AND m.controlador <> 'reports'";
        }
        $sql="SELECT m.id, m.menu, m.descripcion, m.controlador,s.id as id_submenu  ,s.submenu,s.accion,s.descripcion,m.logo FROM menus m
        INNER JOIN submenus s ON m.id=s.id_menu
        WHERE $filtro
        AND s.habilitado='1'
        ORDER BY m.index";
        return $this->_db->query(Database::SELECT, $sql,TRUE);
    }
    
    
}

?>
