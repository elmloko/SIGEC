<?php
 defined('SYSPATH') or die('No direct script access.');
 
 class Controller_Minitemplate extends Controller_Template
  {
     public $template = 'templates/mini';
     public function before()
      {
         // Run anything that need ot run before this.
         parent::before();
         if($this->auto_render)
          {
            // Initialize empty values
            $this->template->title            = '';
            $this->template->theme            = 'azul';
            $this->template->content          = '';           
            $this->template->styles           = array();
            $this->template->scripts          = array();
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
             $styles = array(
                //'media/css/themes/'.$this->template->theme.'.css'=>'screen',
                'static/css/animate.css' => 'screen',
                'static/css/theme-1/libs/multi-select/multi-select.css' => 'screen',
                'static/css/theme-1/libs/morris/morris.core.css' => 'screen',
                'static/css/theme-1/libs/rickshaw/rickshaw.css' => 'screen',
                'static/css/theme-1/material-design-iconic-font.min.css' => 'screen',
                'static/css/theme-1/font-awesome.min.css' => 'screen',
                'static/css/theme-1/materialadmin.css' => 'all',
                'static/css/theme-1/bootstrap.css' => 'all',
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
               // 'static/js/libs/rickshaw/rickshaw.min.js',
              //  'static/js/libs/d3/d3.v3.js',
               // 'static/js/libs/d3/d3.min.js',
                'static/js/libs/multi-select/jquery.multi-select.js',                
                'static/js/libs/jquery-validation/dist/jquery.validate.min.js',
                'static/js/libs/nanoscroller/jquery.nanoscroller.min.js',
                'static/js/libs/moment/moment.min.js',
                'static/js/libs/autosize/jquery.autosize.min.js',
                'static/js/libs/spin.js/spin.min.js',
                'static/js/libs/bootstrap/bootstrap.min.js',
                'static/js/libs/jquery/jquery-1.11.2.min.js',
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