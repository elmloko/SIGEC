<?php
 defined('SYSPATH') or die('No direct script access.');

 class Controller_IndexTemplate extends Controller_Template
  {
     public $template = 'templates/layout_inicio';
     public function before()
      {
         // Run anything that need ot run before this.
         parent::before();
         if($this->auto_render)
          {
            // Initialize empty values
            $this->template->title            = 'SIGEC';
            $this->template->meta_keywords    = '';
            $this->template->meta_description = '';
            $this->template->meta_copywrite   = '';
            $this->template->header           = '';
            $this->template->content          = '';
            $this->template->menu             = '';
            $this->template->footer           = 'copyright Ivan Marcelo Chacolla';
            $this->template->adminmenu        = '';
            $this->template->styles           = array();
            $this->template->scripts          = array();   
            $this->template->menutop          = '';            
            $this->template->titulo           = '';            
            $this->template->descripcion      = '';            
            $this->template->menutop          = '';            
            $this->template->username          = '';            
            $this->template->submenu          = View::factory('user/menu');
            $this->template->controller       = 'index';            
            $this->template->theme            = '#modx-topbar{border-bottom: 2px solid #1a549a;} #bos-main-blocks h2 a,h2.titulo v,.colorcito {color:#1a549a;}#menu-left ul li a:hover,#menu-left ul li:hover {color:#fff; background: #1a549a; font-weight: bold; } html #modx-topnav ul.modx-subnav li a:hover {background-color:#1a549a;} input#searchsubmit:hover {background-color:#1a549a;} #icon-logo{background:#1a549a url(/media/images/icon_user.png) scroll left no-repeat; }.button2{border: 1px solid#1a549a;background-color:#1a549a;}.button2:hover, .button2:focus {background:#1a549a;}.jOrgChart .node { background-color:#1a549a;}.widget .title {background: none repeat scroll 0 0 #1a549a;} legend {border: 1px solid #1a549a;}fieldset { border: 2px solid#1a549a;}.proveido {color:#1a549a;} span.dias4{ background: #1a549a url("/media/images/fondo_transparente.png") no-repeat top left; } ';            
          }
      }

     /**
      * Fill in default values for our properties before rendering the output.
      */
     public function after()
      {
         if($this->auto_render)
          {
             // Define defaults
             $styles                  = array(  
                                              //'media/css/themes/'.$this->template->theme.'.css'=>'all',   
                                              'media/css/farbtastic.css'=>'screen',
                                              'media/css/dp_calendar.css'=>'all',                                               
                                              'media/css/print.css'=>'print',
                                              'media/css/main.css'=>'screen',                                              
                                              'media/css/input.css'=>'screen',
                                              'media/css/style.css'=>'screen',
                                              'media/css/flick/jquery-ui-1.8.21.custom.css'=>'all',                                              
                                              'media/css/modx-min.css'=>'screen',
                                              'media/css/reset.css'=>'screen',
                                              'media/css/jquery.toastmessage.css'=>'screen'
                                              );
             $scripts                 = array(                                                                                                                                            
                                              'media/js/panel.js',
                                              'media/js/farbtastic.js',
                                              'media/js/jquery.dp_calendar.min.js',
                                              'media/js/jquery.dp_calendar-es.js', 
                                              'media/js/jquery.toastmessage.js',      
                                              //'media/js/message.js',   
                                              'media/js/jquery-ui-1.8.21.custom.min.js',                                              
                                              //'media/js/main.js',                                                             
                                              'media/js/jquery-1.7.2.min.js',
                                              'media/js/date.js',
                                            );

             // Add defaults to template variables.
             $this->template->styles  = array_reverse(array_merge($this->template->styles, $styles));
             $this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));
           }
         // Run anything that needs to run after this.
         parent::after();
      }
 }
?>