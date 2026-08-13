<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Historial extends Controller {

    protected $user;

    public function before() {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            parent::before();
        } else {
            $this->request->redirect('/login');
        }
    }

    public function action_historial() {
        $o_reports = New Model_documentos();
        $documentos = $o_reports->historial();
        $result=array();
        foreach ($documentos as $d) {
            $result[]=array($d['fecha'],$d['a']);
            
        }
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }


}
