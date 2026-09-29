<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Tipos extends Controller_AdminTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'admin');
        $oSM = New Model_menus();

        parent::after();
    }

    public function action_index() {
        if (isset($_POST['submit'])) {
            var_dump($_POST);
        }
        $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

        $tipos = ORM::factory('tipos')->find_all();
        $session = Session::instance();
        $mensaje = $session->get_once('tipo_mensaje', '');
        $error = $session->get_once('tipo_error', '');

        $this->template->content = View::factory('admin/config/tipo_documentos')
                ->bind('tipos', $tipos)
                ->bind('mensaje', $mensaje)
                ->bind('error', $error);
    }

    // elimina un tipo de documento solo si no hay documentos generados con ese tipo
    public function action_eliminar($id = '') {
        $session = Session::instance();
        $tipo = ORM::factory('tipos', $id);
        if ($this->request->method() !== Request::POST || !$tipo->loaded()) {
            $this->request->redirect('/admin/tipos');
        }
        $total = DB::select(array(DB::expr('COUNT(*)'), 'total'))
                ->from('documentos')
                ->where('id_tipo', '=', $tipo->id)
                ->execute()
                ->get('total');
        if ($total > 0) {
            $session->set('tipo_error', 'No se puede eliminar el tipo ' . $tipo->tipo . ' porque tiene ' . $total . ' documentos generados.');        } else {
            $nombre = $tipo->tipo;
            DB::delete('usertipo')->where('id_tipo', '=', $tipo->id)->execute();
            DB::delete('correlativo')->where('id_tipo', '=', $tipo->id)->execute();
            $tipo->delete();
            $session->set('tipo_mensaje', 'Se elimino el tipo de documento ' . $nombre . '.');
        }
        $this->request->redirect('/admin/tipos');
    }

}

?>
