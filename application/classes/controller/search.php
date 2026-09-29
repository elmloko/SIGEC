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
        $count = 0;
        $result = array();
        $page_links = '';
        $mensajes = array();
        $db = Database::instance();
        // valores del usuario siempre escapados por la base de datos (antes se concatenaban tal cual: inyeccion SQL)
        $like = function ($valor) use ($db) {
            return $db->escape('%' . trim($valor) . '%');
        };
        $fecha = function ($valor) {
            $valor = trim($valor);
            if ($valor === '') {
                return NULL;
            }
            // acepta dd-mm-aaaa (formato anterior) o aaaa-mm-dd (selector de fecha)
            foreach (array('d-m-Y', 'Y-m-d') as $formato) {
                $d = DateTime::createFromFormat('!' . $formato, $valor);
                if ($d && $d->format($formato) === $valor) {
                    return $d;
                }
            }
            return FALSE;
        };

        // fechas: por defecto hoy; "todas=1" busca en cualquier fecha
        $todas_fechas = Arr::get($_GET, 'todas') == '1';
        $desde = $todas_fechas ? NULL : $fecha(Arr::get($_GET, 'start', isset($_GET['buscar']) ? '' : date('Y-m-d')));
        $hasta = $todas_fechas ? NULL : $fecha(Arr::get($_GET, 'end', isset($_GET['buscar']) ? '' : date('Y-m-d')));
        if ($desde === FALSE || $hasta === FALSE) {
            $mensajes['fecha'] = 'Una de las fechas no es válida; se ignoró el filtro de fechas.';
            $desde = $hasta = NULL;
        }
        if ($desde && $hasta && $desde > $hasta) {
            list($desde, $hasta) = array($hasta, $desde);
        }
        $filtros = array(
            'nur' => trim(Arr::get($_GET, 'nur', '')),
            'cite_original' => trim(Arr::get($_GET, 'cite_original', '')),
            'tipo' => (int) Arr::get($_GET, 'tipo', 0),
            'referencia' => trim(Arr::get($_GET, 'referencia', '')),
            'destinatario' => trim(Arr::get($_GET, 'destinatario', '')),
            'remitente' => trim(Arr::get($_GET, 'remitente', '')),
            'entidad' => trim(Arr::get($_GET, 'entidad', '')),
            'start' => $desde ? $desde->format('Y-m-d') : '',
            'end' => $hasta ? $hasta->format('Y-m-d') : '',
            'todas' => $todas_fechas,
        );

        if (isset($_GET['buscar'])) {
            $conditions = array();
            if ($desde) {
                $conditions[] = 'd.fecha_creacion >= ' . $db->escape($desde->format('Y-m-d') . ' 00:00:00');
            }
            if ($hasta) {
                $conditions[] = 'd.fecha_creacion <= ' . $db->escape($hasta->format('Y-m-d') . ' 23:59:59');
            }
            if ($filtros['tipo'] > 0) {
                $conditions[] = 'd.id_tipo = ' . $filtros['tipo'];
            }
            $campos = array(
                'nur' => 'd.nur',
                'cite_original' => 'd.cite_original',
                'referencia' => 'd.referencia',
                'destinatario' => 'd.nombre_destinatario',
                'remitente' => 'd.nombre_remitente',
                'entidad' => 'd.institucion_remitente',
            );
            foreach ($campos as $clave => $columna) {
                if ($filtros[$clave] !== '') {
                    $conditions[] = $columna . ' LIKE ' . $like($filtros[$clave]);
                }
            }

            if (empty($conditions)) {
                $mensajes['criterio'] = 'Ingrese al menos un criterio de búsqueda (hoja de ruta, cite, referencia, destinatario, remitente, entidad o un rango de fechas).';
            } else {
                $where = ' WHERE ' . implode(' AND ', $conditions);
                $oDocumento = New Model_Documentos();
                $count = $oDocumento->contar2($where);
                $count = (int) $count[0]['count'];
                if ($count > 0) {
                    $pagination = Pagination::factory(array(
                                'total_items' => $count,
                                'current_page' => array('source' => 'query_string', 'key' => 'page'),
                                'items_per_page' => 15,
                                'view' => 'pagination/floating',
                    ));
                    $result = $oDocumento->search($where, $pagination->offset, $pagination->items_per_page);
                    $page_links = $pagination->render();
                }
                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ', realizó una busqueda encontrando <b>' . $count . '</b> resultados para <b>' . HTML::chars($where) . '</b>');
            }
        }

        $oTipos = ORM::factory('tipos')->find_all();
        $tipos = array(0 => 'Todos los tipos');
        foreach ($oTipos as $t) {
            $tipos[$t->id] = $t->tipo;
        }

        $this->template->title .= ' / Busqueda avanzada';
        $this->template->titulo .= ' Busqueda avanzada';
        $this->template->descripcion .= 'Realizar busqueda bajo criterios';
        $this->template->content = View::factory('busqueda/form_advanced')
                ->set('page_links', $page_links)
                ->set('result', $result)
                ->set('tipos', $tipos)
                ->set('count', $count)
                ->set('filtros', $filtros)
                ->set('buscado', isset($_GET['buscar']))
                ->set('mensajes', $mensajes);
    }

    // version anterior de la busqueda (armaba el SQL con el texto y los nombres de columna del usuario); se redirige a la actual
    public function action_advanced_old() {
        $this->request->redirect('search/advanced');
    }

}

?>
