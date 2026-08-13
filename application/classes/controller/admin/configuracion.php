<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Configuracion extends Controller_AdminTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'user');
        $oSM = New Model_menus();

        parent::after();
    }

    public function action_tipos() {
        if (isset($_POST['submit'])) {
            var_dump($_POST);
        }
        $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

        $tipos = ORM::factory('tipos')->find_all();

        $this->template->content = View::factory('admin/configuracion/tipo_documentos')
                ->bind('tipos', $tipos);
    }

}

?>
