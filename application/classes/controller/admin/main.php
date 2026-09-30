<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Main extends Controller_AdminTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', '');
        $oSM = New Model_menus();
        parent::after();
    }

    // lista de oficinas
    public function action_index() {
        $documentos = ORM::factory('documentos')->count_all();
        $entidades = ORM::factory('entidades')->count_all();
        $usuarios = ORM::factory('users')->count_all();
        $this->template->titulo .= 'Panel de administración';
        $this->template->descripcion .= $this->user->nombre;

        // hojas de ruta: documentos originales con numero asignado (antes $hojasruta no llegaba a la vista)
        $hojasruta = DB::query(Database::SELECT, "SELECT COUNT(*) AS n FROM documentos WHERE original = 1 AND nur <> ''")->execute()->get('n');
        $usuarios_activos = DB::query(Database::SELECT, "SELECT COUNT(*) AS n FROM users WHERE habilitado = 1")->execute()->get('n');

        // movimiento de hoy
        $hoy = DB::query(Database::SELECT, "SELECT
                (SELECT COUNT(*) FROM documentos WHERE fecha_creacion >= CURDATE()) AS documentos,
                (SELECT COUNT(*) FROM seguimiento WHERE fecha_emision >= CURDATE()) AS derivaciones,
                (SELECT COUNT(*) FROM seguimiento WHERE fecha_recepcion >= CURDATE()) AS recepciones,
                (SELECT COUNT(DISTINCT id_usuario) FROM vitacora WHERE fecha_hora >= CURDATE()) AS usuarios")
                ->execute()->current();

        // documentos generados por mes (ultimos 12 meses)
        $por_mes = DB::query(Database::SELECT, "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS mes, COUNT(*) AS n
                FROM documentos
                WHERE fecha_creacion >= DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH, '%Y-%m-01')
                GROUP BY mes ORDER BY mes")->execute()->as_array('mes', 'n');

        // tarjetas de resumen por estado
        $oSeg = New Model_Seguimiento();
        $estados = array();
        foreach ($oSeg->totalesSistema() as $t) {
            $estados[$t['id']] = array(
                'titulo' => $t['plural'],
                'descripcion' => $t['titulo'],
                'accion' => '/admin/hojasruta/lista',
                'cantidad' => $t['cantidad'],
                'icon' => $t['ui'] != '' ? $t['ui'] : 'fa fa-file-text',
            );
        }

        $this->template->scripts = array('media/Highcharts/js/highcharts.js');
        $this->template->content = View::factory('admin/dashboard')
                ->bind('documentos', $documentos)
                ->bind('usuarios', $usuarios)
                ->bind('usuarios_activos', $usuarios_activos)
                ->bind('entidades', $entidades)
                ->bind('hojasruta', $hojasruta)
                ->bind('hoy', $hoy)
                ->bind('por_mes', $por_mes)
                ->bind('estados', $estados);
    }
    // lista de oficinas
    public function action_index_entidades() {
        //$entidades = ORM::factory('entidades')->count_all();
        $entidades = ORM::factory('entidades')->find_all();
        $this->template->titulo.=$this->user->username;

        $this->template->descripcion.=$this->user->nombre;
        $this->template->content = View::factory('admin/lista_entidades')
                ->bind('entidades', $entidades);
    }

    public function action_lista($id = '') {
        $entidad = ORM::factory('entidades', array('id' => $id));
        if ($entidad->loaded()) {
            $oficinas = $entidad->oficinas->find_all();
            $this->template->content = View::factory('/admin/oficinas')
                    ->bind('oficinas', $oficinas);
        } else {
            $this->template->content = 'Error: No se encontro la entidad';
        }
    }

}

?>
