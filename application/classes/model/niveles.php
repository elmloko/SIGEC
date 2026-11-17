<?php
defined('SYSPATH') or die ('no tiene acceso');
//descripcion del modelo productos
class Model_niveles extends ORM{
    protected $_table_names_plural = false;    
    // nivel del rol administrador (unico que ve el menu de Reportes)
    const NIVEL_ADMIN = 5;
    // nivel del rol usuario (funcionario; default de users.nivel), unico que ve "Mis indicadores"
    const NIVEL_USUARIO = 2;
    // jefe de oficina: ve las mismas pestanas que el usuario (y todas las hojas de ruta, ver Acceso)
    const NIVEL_JEFE = 3;

    /** ¿El nivel usa las pestanas del rol usuario (usuario y jefe de oficina)? */
    public static function con_pestanas_de_usuario($n)
    {
        return in_array((int) $n, array(self::NIVEL_USUARIO, self::NIVEL_JEFE), TRUE);
    }

    public function menus($n){
        $n = (int) $n;
        // el jefe de oficina usa los menus del usuario (nivelmenu del nivel 2), sin cambiar la base
        if ($n === self::NIVEL_JEFE) {
            $n = self::NIVEL_USUARIO;
        }
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
