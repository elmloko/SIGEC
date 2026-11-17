<?php
defined('SYSPATH') or die ('no tiene acceso');
//descripcion del modelo productos
class Model_niveles extends ORM{
    protected $_table_names_plural = false;    
    const NIVEL_ADMIN = 5;
    // nivel del rol usuario (funcionario; default de users.nivel)
    const NIVEL_USUARIO = 2;
    const NIVEL_JEFE = 3;

    // pestanas de cada nivel: tabla nivelmenu (se edita en Configuraciones > Roles y permisos)
    public function menus($n){
        $n = (int) $n;
        // la pestana Reportes (controlador 'reports') depende del permiso "ver_reportes" del rol, no de nivelmenu
        if (Permisos::tiene($n, 'ver_reportes')) {
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
