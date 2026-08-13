<?php
 defined('SYSPATH') or die('No direct script access.');

 class Controller_documentTemplate extends Controller_Template
  {
     public $template = 'templates/layout_documento';
     public function before()
      {
         parent::before();
         if($this->auto_render)
          {
            $this->template->title            = 'AEV';
            $this->template->meta_keywords    = '';
            $this->template->meta_description = '';
            $this->template->meta_copywrite   = '';
            $this->template->header           = '';
            $this->template->content          = '';
            $this->template->menu             = '';
            $this->template->footer           = 'Ivan Marcelo Chacolla';
            $this->template->adminmenu        = '';
            $this->template->styles           = array();
            $this->template->scripts          = array();   
            $this->template->menutop          = '';
            $this->template->username         = '';
            $this->template->cargo            = '';
            $this->template->submenu          = View::factory('user/menu');
            $this->template->titulo           = '';                       
            $this->template->descripcion      = '';            
            $this->template->controller       = 'index';  
            $this->template->theme            = '#modx-topbar{border-bottom: 2px solid #1b91f3;} #bos-main-blocks h2 a,h2.titulo v,.colorcito {color:#1b91f3;}#menu-left ul li a:hover,#menu-left ul li:hover {color:#fff; background: #1b91f3; font-weight: bold; } html #modx-topnav ul.modx-subnav li a:hover {background-color:#1b91f3;} input#searchsubmit:hover {background-color:#1b91f3;} #icon-logo{background:#1b91f3 url(/media/images/icon_user.png) scroll left no-repeat; }.button2{border: 1px solid#1b91f3;background-color:#1b91f3;}.button2:hover, .button2:focus {background:#1b91f3;}.jOrgChart .node { background-color:#1b91f3;}
.widget .title {background: none repeat scroll 0 0 #1b91f3;}';
          }
      }
     /**
      * Fill in default values for our properties before rendering the output.
      */
     public function after()
      {
         if($this->auto_render)
          {             
             $styles                  = array(  
                                              //'media/css/themes/'.$this->template->theme.'.css'=>'screen',
                                              'media/css/input.css'=>'screen',
                                              'media/css/print.css'=>'print',
                                              'media/css/main.css'=>'screen',                                                                                            
                                              'media/css/style.css'=>'screen',
                                             'media/css/flick/jquery-ui-1.8.21.custom.css'=>'all',                                              
                                              'media/css/modx-min.css'=>'screen',
                                              'media/css/reset.css'=>'screen'
                                              );
             $scripts                 = array(
                                             'media/js/jquery-ui-1.8.21.custom.min.js',                                              
                                              'media/js/jquery.validate.js',                                                             
                                              'media/js/main_documento.js',                                                             
                                              'media/js/jquery-1.7.2.min.js'
                                              );

             // Add defaults to template variables.
             $this->template->styles  = array_reverse(array_merge($this->template->styles, $styles));
             $this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));
           }
         // Run anything that needs to run after this.
         parent::after();
      }
      public function save($entidad,$user,$accion)
	{
		$vitacora=ORM::factory('vitacora');                
                $vitacora->id_entidad=$entidad;
                $vitacora->id_usuario=$user;
                $vitacora->fecha_hora=date('Y-m-d H:i:s');
                $vitacora->accion_realizada=$accion;
                $vitacora->ip_usuario= Request::$client_ip;                         
                $vitacora->save();
	}
 }
?>