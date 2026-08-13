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
        $this->template->titulo.=$this->user->username;
        $this->template->descripcion.=$this->user->nombre;
        
        
        
        $this->template->scripts = array('media/Highcharts/js/modules/exporting.js',
            'media/Highcharts/js/highcharts-more.js',
            'media/Highcharts/js/highcharts.js');
        $this->template->content = View::factory('admin/dashboard')
                ->bind('documentos', $documentos)
                ->bind('usuarios', $usuarios)
                ->bind('entidades', $entidades)
        ;
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
