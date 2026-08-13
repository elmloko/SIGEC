<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_Search extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            //menu top de acuerdo al nivel
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($this->user->nivel);
            parent::before();
            $this->template->titulo = '<v>Busqueda </v> /';
            $this->template->username = $this->user->nombre;
            if ($this->user->theme != null) {
                $this->template->theme = $this->user->theme;
            }
        } else {
            $url = substr($_SERVER['REQUEST_URI'], 1);
            $this->request->redirect('/login?url=' . $url);
        }
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'search');
        $oSM = New Model_menus();
        $submenus = $oSM->submenus('search');
        $this->template->submenu = View::factory('templates/submenu')->bind('smenus', $submenus)->set('titulo', 'Busqueda');
        parent::after();
    }

    //listar nuris generados por el usuario logeado
    public function action_index() {
        if (isset($_GET['q'])) {
            $text = strtoupper(trim(Arr::get($_GET, 'q', '')));
            if ($text != '') {
                //  $entidad = $this->user->id_entidad;
                // if ($this->user->prioridad == 1)
                $entidad = 0;
                $oDocumento = New Model_Documentos();
                $count = $oDocumento->contarHR($text, $entidad);
                $count = $count[0]['count'];
                // Creamos una instancia de paginacion + configuracion
                $pagination = Pagination::factory(array(
                            'total_items' => $count,
                            'current_page' => array('source' => 'query_string', 'key' => 'page'),
                            'items_per_page' => 10,
                            'view' => 'pagination/floating',
                ));
                $results = $oDocumento->buscarHR($text, $pagination->offset, $pagination->items_per_page, $entidad);
                // Render the pagination links
                $page_links = $pagination->render();
                //tipos para los tabs       
                //vitacora

                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre.', realizó una busqueda encontrando <b>' . $count . '</b> resultados para <b>\'' . $text . '\'</b>');
                $this->template->title = ' Resultados de la busqueda';
                $this->template->titulo .= 'Resultados';
                $descripcion = '<b>' . $count . '</b> hojas de ruta encontrados para <b>\'' . $text . '\'</b>';
                $this->template->styles = array('media/css/tablas.css' => 'all');
                $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
                $this->template->content = View::factory('busqueda/result')
                        ->bind('descripcion', $descripcion)
                        ->bind('results', $results)
                        ->bind('page_links', $page_links)
                        ->bind('count', $count)
                        ->bind('name', $text);
            } else {
                $this->request->redirect('search/advanced');
            }
        } else {
            $this->request->redirect('login');
        }
    }

    public function action_documentos() {
        $this->template->titulo.="Busqueda basica";
        $this->template->descripcion.="Busqueda rapida";
        $this->template->styles = array('media/css/search.css' => 'screen');
        $this->template->scripts = array('media/js/scriptsearch.js');
        $this->template->content = View::factory('busqueda/documentos');
    }

    public function action_advanced() {
        $count=0;
        $result=array();
        $result_all = array();
         $page_links ="";
        $mensajes = array();
        if (isset($_GET['buscar'])) {

            $conditions = array();

            $start = trim(Arr::get($_GET, 'start', ''));
            $end = trim(Arr::get($_GET, 'end', ''));
            if ($start != '' && $end != '') {
                $f1 = (new DateTime($start))->format('Y-m-d') . " 00:00:00";
                $f2 = (new DateTime($end))->format('Y-m-d') . " 23:59:59";
                $conditions[] = "d.fecha_creacion BETWEEN '$f1' AND '$f2'";
            }
            if (Arr::get($_GET, 'tipo', 0) > 0) {
                $conditions[] = "id_tipo = '" . $_GET['tipo'] . "'";
            }
            if (trim(Arr::get($_GET, 'cite_original', '')) != '') {
                $conditions[] = "d.cite_original like '%" . $_GET['cite_original'] . "%'";
            }
            if (trim(Arr::get($_GET, 'referencia', '')) != '') {
                $conditions[] = "d.referencia like '%" . $_GET['referencia'] . "%'";
            }
            if (trim(Arr::get($_GET, 'nur', '')) != '') {
                $conditions[] = "d.nur like '%" . $_GET['nur'] . "%'";
            }
            if (trim(Arr::get($_GET, 'destinatario', '')) != '') {
                $conditions[] = "d.nombre_destinatario like '%" . $_GET['destinatario'] . "%'";
            }
            if (trim(Arr::get($_GET, 'remitente', '')) != '') {
                $conditions[] = "d.nombre_remitente like '%" . $_GET['remitente'] . "%'";
            }
            if (trim(Arr::get($_GET, 'entidad', '')) != '') {
                $conditions[] = "d.institucion_remitente like '%" . $_GET['entidad'] . "%'";
            }

            if (empty($conditions)) {
                $mensajes['info'] = 'Ingrese al menos un criterio de búsqueda (hoja de ruta, cite, destinatario, remitente, entidad o un rango de fechas) antes de buscar.';
                $where = '';
            } else {
                $where = " WHERE " . implode(' AND ', $conditions);
                $oDocumento = New Model_Documentos();
                $count = $oDocumento->contar2($where);
                $count = $count[0]['count'];
                if ($count > 0) {
                    //echo $count;
                    // Creamos una instancia de paginacion + configuracion
                    $pagination = Pagination::factory(array(
                                'total_items' => $count,
                                'current_page' => array('source' => 'query_string', 'key' => 'page'),
                                'items_per_page' => 10,
                                'view' => 'pagination/floating',
                    ));

                    $result = $oDocumento->search($where, $pagination->offset, $pagination->items_per_page);

                    /* MUESTRA EL TOTAL DE RESULTADOS SIN LIMIT, PARA SACAR EL REPORTE EXCEL */
                    $result_all = $oDocumento->search_all($where);

                    // Render the pagination links
                    $page_links = $pagination->render();
                }


                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre.', realizó una busqueda encontrando <b>' . $count . '</b> resultados para <b>' . $where . '</b>');
            }
           // echo $where;

            /*
              $text = $_GET['texto'];
              $where = " WHERE ";
              $campos = New ArrayIterator($_GET['campo']);
              foreach ($campos as $c) {
              $where.="d." . $c . " LIKE '%$text%' OR ";
              }
              $where = substr($where, 0, -3);

              $oDocumento = New Model_Documentos();
              $count = $oDocumento->contar2($where);
              $count = $count[0]['count'];
              if ($count > 0) {
              // Creamos una instancia de paginacion + configuracion
              $pagination = Pagination::factory(array(
              'total_items' => $count,
              'current_page' => array('source' => 'query_string', 'key' => 'page'),
              'items_per_page' => 15,
              'view' => 'pagination/floating',
              ));
              $results = $oDocumento->search($where, $pagination->offset, $pagination->items_per_page);
              // Render the pagination links
              $page_links = $pagination->render();
              //tipos para los tabs
              $this->save($this->user->id_entidad, $this->user->id, 'Realizó una busqueda encontrando <b>' . $count . '</b> resultados para <b>\'' . $text . '\'</b>');
              $this->template->title = ' Resultados de la busqueda';
              $this->template->titulo .=' Busqueda avanzada';
              $this->template->descripcion = '<b>' . $count . '</b> resultados encontrados para <b>\'' . $text . '\'</b>';
              $this->template->styles = array('media/css/tablas.css' => 'screen');
              $this->template->scripts = array('media/js/tablesort.min.js');
              $this->template->content = View::factory('busqueda/result')
              ->bind('results', $results)
              ->bind('page_links', $page_links)
              ->bind('count', $count)
              ->bind('name', $text);
              } else {
              $mensajes['Sin exito!: '] = "No se encontro ningun resultado para <b>'$text'</b>.";
              $this->template->title .='| formulario de busqueda';
              $this->template->titulo .=' Busqueda avanzada';
              $this->template->descripcion .='Realizar busqueda bajo criterios';
              $this->template->content = View::factory('busqueda/form_advanced')
              ->bind('mensajes', $mensajes);
              }
             * 
             */
        }


        $oTipos = ORM::factory('tipos')->find_all();
        $tipos = array(0 => "Todos los tipos");
        foreach ($oTipos as $t) {
            $tipos[$t->id] = $t->tipo;
        }

        $this->template->styles = array(
            'static/css/theme-3/libs/bootstrap-datepicker/datepicker3.css' => 'screen',
            'static/css/theme-3/libs/select2/select2.css' => 'screen',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'static/js/libs/bootstrap-datepicker/bootstrap-datepicker.js',
            'static/js/libs/select2/select2.min.js'
        );


        $this->template->title .=' / Busqueda avanzada';
        $this->template->titulo .=' Busqueda avanzada';
        $this->template->descripcion .='Realizar busqueda bajo criterios';
        $this->template->content = View::factory('busqueda/form_advanced')
                ->bind('page_links',  $page_links )
                ->bind('result', $result)
                ->bind('result_all', $result_all)
                ->bind('tipos', $tipos)
                ->bind('count', $count)
                ->bind('mensajes', $mensajes);
    }

    public function action_advanced_old() {
        $mensajes = array();
        if (isset($_GET['buscar'])) {
            $text = $_GET['texto'];
            $where = " WHERE ";
            $campos = New ArrayIterator($_GET['campo']);
            foreach ($campos as $c) {
                $where.="d." . $c . " LIKE '%$text%' OR ";
            }
            $where = substr($where, 0, -3);

            $oDocumento = New Model_Documentos();
            $count = $oDocumento->contar2($where);
            $count = $count[0]['count'];
            if ($count > 0) {
                // Creamos una instancia de paginacion + configuracion
                $pagination = Pagination::factory(array(
                            'total_items' => $count,
                            'current_page' => array('source' => 'query_string', 'key' => 'page'),
                            'items_per_page' => 15,
                            'view' => 'pagination/floating',
                ));
                $results = $oDocumento->search($where, $pagination->offset, $pagination->items_per_page);
                // Render the pagination links
                $page_links = $pagination->render();
                //tipos para los tabs       
                $this->save($this->user->id_entidad, $this->user->id, 'Realizó una busqueda encontrando <b>' . $count . '</b> resultados para <b>\'' . $text . '\'</b>');
                $this->template->title = ' Resultados de la busqueda';
                $this->template->titulo .=' Busqueda avanzada';
                $this->template->descripcion = '<b>' . $count . '</b> resultados encontrados para <b>\'' . $text . '\'</b>';
                $this->template->styles = array('media/css/tablas.css' => 'screen');
                $this->template->scripts = array('media/js/tablesort.min.js');
                $this->template->content = View::factory('busqueda/result')
                        ->bind('results', $results)
                        ->bind('page_links', $page_links)
                        ->bind('count', $count)
                        ->bind('name', $text);
            } else {
                $mensajes['Sin exito!: '] = "No se encontro ningun resultado para <b>'$text'</b>.";
                $this->template->title .='| formulario de busqueda';
                $this->template->titulo .=' Busqueda avanzada';
                $this->template->descripcion .='Realizar busqueda bajo criterios';
                $this->template->content = View::factory('busqueda/form_avanzada')
                        ->bind('mensajes', $mensajes);
            }
        } else {
            $this->template->title .=' / Busqueda avanzada';
            $this->template->titulo .=' Busqueda avanzada';
            $this->template->descripcion .='Realizar busqueda bajo criterios';
            $this->template->content = View::factory('busqueda/form_avanzada')
                    ->bind('mensajes', $mensajes);
        }
    }

}

?>
