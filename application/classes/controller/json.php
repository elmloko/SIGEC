<?php
defined('SYSPATH') or die('Acceso denegado');
class Controller_json extends Controller{

    // solo para usuarios con sesion iniciada (antes era publico)
    // (se quitaron las acciones de prueba derivar, editarWord y pdf: no las usaba ninguna pantalla)
    public function before()
    {
        parent::before();
        if (!Auth::instance()->logged_in()) {
            $this->request->redirect('/login');
        }
    }

    //lista de las noticias
    public function action_noticias(){
        $noticias=  New Model_data();
        $notis=$noticias->noticias();
        //$noticias=ORM::factory('comunicados')->where('tipo','=','1')->find_all()->as_array();
        echo json_encode($notis);
    }
    public function action_sigla(){
        $sigla=ORM::factory('oficinas',HTML::chars(Arr::get($_POST, 'id')));
        echo HTML::chars($sigla->sigla);
    }
   }
?>
