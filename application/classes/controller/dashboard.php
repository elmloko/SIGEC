<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_Dashboard extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        parent::before();
    }

    public function after() {
        
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'dashboard');
        $this->template->genero = $this->user->genero;
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        //$mAlertas = new Model_Alertas();
      //  $this->template->alertas = $mAlertas->lista($this->user->id);
	$this->template->alertas=array();
        //$this->template->alertas = array('fecha'=>'dasd');
        parent::after();
    }

    public function action_test_old() {


        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/map/maplace-0.1.3.min.js',
            'http://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7',
            'http://code.jquery.com/jquery-1.9.0.min.js',
        );


        $pgsqltest_db = Database::instance('rrhh');
        $query = DB::query(Database::SELECT, 'SELECT * FROM  f_relaborales_y_personas_nuevas_un_registro() where estado>=1')->execute($pgsqltest_db);
        $this->template->content = View::factory('proyectos/lista')
                ->bind('proyectos', $query);
    }

    public function action_test() {
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/map/maplace-0.1.3.min.js',
            'http://code.jquery.com/jquery-1.9.0.min.js',
            'http://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7',
        );
        $pgsqltest_db = Database::instance('postgresql');
        $query = DB::query(Database::SELECT, ' select nombre, tipo_pry, localidad, estado, area_ur, pob_benef, cx, cy from pry_proyecto')->execute($pgsqltest_db);
        $this->template->content = View::factory('proyectos/lista')
                ->bind('proyectos', $query);
    }

    public function action_index() {
        if($this->user->nivel==5){
            $this->request->redirect('/admin');            
        }        
        
        $ventanilla = array();
        $vista = 'index';
        $oSeguimiento = New Model_Seguimiento();
        $id = $this->user->id;
        if ($this->user->nivel == 4) {
//NUEVO
            $estados = $oSeguimiento->nestadosVentanilla($id);
            foreach ($estados as $e) {
                $aEstados = array(
                    2 => array(
                        'titulo' => 'Recibidas',
                        'descripcion' => 'Correspondencia recibida',
                        'accion' => '/ventanilla/recibida',
                        'cantidad' => $e['recibido'],
                        'icon' => 'fa fa-inbox',
                        'color' => 'warning',
                    ),
                    10 => array(
                        'titulo' => 'Pendientes',
                        'descripcion' => 'Correspondencia  recibida y  No derivada',
                        'accion' => '/ventanilla/pendientes',
                        'cantidad' => $e['pendientes'],
                        'icon' => 'md md-timer',
                        'color' => 'info',
                    ),
                );
            }
            $vista = 'ventanilla';
        } else {
            $estados = $oSeguimiento->nestados($id);
            foreach ($estados as $e) {
                $aEstados = array(
                    1 => array(
                        'titulo' => 'Entrada',
                        'descripcion' => 'Correspondencia Entrante',
                        'accion' => '/bandeja',
                        'cantidad' => $e['norecibido'],
                        'icon' => 'fa fa-inbox',
                        'color' => 'warning',
                    ),
                    2 => array(
                        'titulo' => 'Pendientes',
                        'descripcion' => 'Correspondencia Pendiente',
                        'accion' => '/bandeja/pendientes',
                        'cantidad' => $e['pendientes'],
                        'icon' => 'md md-timer',
                        'color' => 'danger',
                    ),
                    10 => array(
                        'titulo' => 'Archivo',
                        'descripcion' => 'Correspondencia archivada',
                        'accion' => '/bandeja/archivo',
                        'cantidad' => $e['archivo'],
                        'icon' => 'fa fa-archive',
                        'color' => 'info',
                    ),
                );
                $documentos = $e['documentos'];
            }
        }
// $num_doc = ORM::factory('documentos')->where('id_user', '=', $this->user->id)->count_all();

        $user = ORM::factory('users', array('id' => $id));
        //$oficina = $user->oficina->oficina;
        $this->template->title .=' / Inicio';
        $this->template->titulo = '<v>' . $this->user->nombre;
        $this->template->descripcion = $this->user->cargo;
// unset($estados[6]);
        //destinatarios

        $mUsers = New Model_Users();
        $destinatarios = $mUsers->destinatarios($id);

        $mDocumentos = New Model_Documentos();
        $ultimos_documentos = $mDocumentos->zdoc($id);
        $oTipo = New Model_Tipos();
        //$mistipos = $oTipo->lista($this->user->id);
        $mistipos = $oTipo->misTipos($this->user->id);


        $this->template->scripts = array('media/Highcharts/js/modules/exporting.js',
            'media/Highcharts/js/highcharts-more.js',
            'media/Highcharts/js/highcharts.js');
        /*
          $this->template->scripts = array(
          //'media/js/panel.js',
          'static/js/libs/flot/curvedLines.js',
          'static/js/libs/flot/jquery.flot.time.min.js',
          'static/js/libs/flot/jquery.flot.min.js',
          'static/js/libs/moment/moment.min.js',

          );
         */
        $this->template->content = View::factory($vista)
                ->bind('user', $user)
                //->bind('oficina', $oficina)
                ->bind('estados', $aEstados)
                ->bind('destinatarios', $destinatarios)
                ->bind('zdoc', $ultimos_documentos)
                ->bind('documentos', $documentos)
                ->bind('tipos', $mistipos)
        ;
    }

    public function action_buscar() {
        $this->request->redirect('busqueda/buscar');
    }

}

?>
