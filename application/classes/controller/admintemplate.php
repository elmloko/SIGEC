<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_AdminTemplate extends Controller_Template {

    public $template = 'templates/layout_admin';

    public function before() {
        //parent::before();
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            if ((int) $auth->get_user()->habilitado !== 1) {
                // cuenta deshabilitada: se cierra la sesion (destruye sesion nativa + tokens "recordarme") y se expulsa
                $auth->logout(TRUE, TRUE);
                $this->request->redirect('/login');
            }
            //si no es un administrador lo sacamos del sistema
            if ($auth->get_user()->nivel != 5) {
                $this->request->redirect('user/logout');
            }
            $this->user = $auth->get_user();
            $session = Session::instance();
            // ingreso con la contraseña por defecto: primero debe cambiarla (ver Controller_DefaultTemplate)
            if ($session->get('debe_cambiar_pass')) {
                $this->request->redirect('/user/pass');
            }
            //actualizamos o adicionamos session
            $usuario_sesion = ORM::factory('sesiones')
                    ->where('user_id', '=', $auth->get_user()->id)
                    ->and_where('session', '=', $session->id())
                    ->find();
            $usuario_sesion->user_id = $auth->get_user()->id;
            $usuario_sesion->session = $session->id();
            $usuario_sesion->last_active = time();
            $usuario_sesion->save();
            //$this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($auth->get_user()->nivel);
            parent::before();
        } else {
            $url = substr($_SERVER['REQUEST_URI'], 1);
            $this->request->redirect('/login?url=' . $url);
        }
        if ($this->auto_render) {
            $this->template->title = 'SIGEC';
            $this->template->meta_keywords = '';
            $this->template->meta_description = '';
            $this->template->meta_copywrite = '';
            $this->template->header = '';
            $this->template->content = '';
            $this->template->menu = '';
            $this->template->footer = 'Ivan Marcelo Chacolla';
            $this->template->adminmenu = '';
            $this->template->styles = array();
            $this->template->scripts = array();
            $this->template->menutop = '';
            $this->template->usuario = $this->user;
            $this->template->genero = 'hombre';
            $this->template->username = '';
            $this->template->nombre = '';
            $this->template->cargo = '';
            $this->template->email = '';
            $this->template->submenu = View::factory('user/menu');
            $this->template->titulo = '';
            $this->template->descripcion = '';
            $this->template->controller = 'index';
            $this->template->theme = '#modx-topbar{border-bottom: 2px solid #1a549a;} #bos-main-blocks h2 a,h2.titulo v,.colorcito {color:#1a549a;}#menu-left ul li a:hover,#menu-left ul li:hover {color:#fff; background: #1a549a; font-weight: bold; } html #modx-topnav ul.modx-subnav li a:hover {background-color:#1a549a;} input#searchsubmit:hover {background-color:#1a549a;} #icon-logo{background:#1a549a url(/media/images/icon_user.png) scroll left no-repeat; }.button2{border: 1px solid#1a549a;background-color:#1a549a;}.button2:hover, .button2:focus {background:#1a549a;}.jOrgChart .node { background-color:#1a549a;}.widget .title {background: none repeat scroll 0 0 #1a549a;} legend {border: 1px solid #1a549a;}fieldset { border: 2px solid#1a549a;}.proveido {color:#1a549a;} span.dias4{ background: #1a549a url("/media/images/fondo_transparente.png") no-repeat top left; } ';
            $this->template->menubar = 'menubar-pin';
        }
    }

    /**
     * Fill in default values for our properties before rendering the output.
     */
    public function after() {

        if ($this->auto_render) {
            $styles = array(
                //'media/css/themes/'.$this->template->theme.'.css'=>'screen',
                'static/css/theme-3/libs/morris/morris.core.css' => 'screen',
                'static/css/theme-3/libs/rickshaw/rickshaw.css' => 'screen',
                'static/css/theme-3/material-design-iconic-font.min.css' => 'screen',
                'static/css/theme-3/font-awesome.min.css' => 'screen',
                'static/css/theme-3/materialadmin.css' => 'all',
                'static/css/theme-3/bootstrap.css' => 'all',
                // 'media/css/input.css'=>'screen',
                'media/css/print.css' => 'print',
                //'media/css/main.css' => 'screen',
                'media/css/style.css' => 'screen',
                    // 'media/css/flick/jquery-ui-1.8.21.custom.css'=>'all',                                              
                    // 'media/css/modx-min.css'=>'screen',
                    //'media/css/reset.css'=>'screen',
                    // 'media/css/jquery.toastmessage.css'=>'screen'
            );
            $scripts = array(
                //'media/js/panel.js',
                'static/js/core/source/AppVendor.js',
                'static/js/core/source/AppNavSearch.js',
                'static/js/core/source/AppForm.js',
                'static/js/core/source/AppCard.js',
                'static/js/core/source/AppOffcanvas.js',
                'static/js/core/source/AppNavigation.js',
                'static/js/core/source/App.js',
                // rickshaw necesita d3, que no se carga aqui (y ninguna vista de admin lo usa)
                //'static/js/libs/rickshaw/rickshaw.min.js',
                //'static/js/libs/d3/d3.v3.js',
               // 'static/js/libs/d3/d3.min.js',
                'static/js/libs/jquery-validation/dist/jquery.validate.min.js',
                
                'static/js/libs/nanoscroller/jquery.nanoscroller.min.js',
                'static/js/libs/moment/moment.min.js',
                'static/js/libs/autosize/jquery.autosize.min.js',
                //'static/js/libs/spin.js/spin.min.js',
                'static/js/libs/bootstrap/bootstrap.min.js',
                'static/js/libs/jquery/jquery-1.11.2.min.js',
            );

            // Add defaults to template variables.
            $this->template->styles = array_reverse(array_merge($this->template->styles, $styles));
            $this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));
        }
        // Run anything that needs to run after this.

        parent::after();
    }

    public function save($entidad, $user, $accion) {
        $vitacora = ORM::factory('vitacora');
        $vitacora->id_entidad = $entidad;
        $vitacora->id_usuario = $user;
        $vitacora->fecha_hora = date('Y-m-d H:i:s');
        $vitacora->accion_realizada = $accion;
        $vitacora->ip_usuario = Request::$client_ip;
        $vitacora->save();
    }

}

?>