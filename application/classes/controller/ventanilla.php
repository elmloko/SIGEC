<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_ventanilla extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            if ($auth->get_user()->nivel != 4)
                $this->request->redirect('user/logout');
            //menu-top de acuerdo al nivel
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($this->user->nivel);
            parent::before();
            $this->template->title = 'Ventanilla ';
            $this->template->username = $this->user->nombre;
            $this->template->titulo = '<v>Ventanilla / </v>';
            if ($this->user->theme != null) {
                $this->template->theme = $this->user->theme;
            }
        } else {

            $url = substr($_SERVER['REQUEST_URI'], 1);
            $this->request->redirect('/login?url=' . $url);
        }
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'ventanilla');
        $oSM = New Model_menus();
        $submenus = $oSM->submenus('ventanilla');
        $docs = FALSE;
        if ($this->user->nivel == 4) {
            $docs = TRUE;
        }
        $this->template->submenu = View::factory('templates/submenu')->bind('smenus', $submenus)->bind('doc', $docs);
        parent::after();
    }

    public function action_principal() {
        $user = ORM::factory('users', $this->user->id);
        $oficina = ORM::factory('oficinas', $user->id_oficina);
        // un solo recorrido en vez de tres consultas de conteo
        $cuentas = DB::query(Database::SELECT, 'SELECT COUNT(*) AS total,
                    SUM(estado = 0) AS pendientes,
                    SUM(estado = 1) AS derivados,
                    SUM(DATE(fecha_creacion) = CURDATE()) AS hoy
                FROM documentos WHERE id_user = :id')
                ->param(':id', (int) $this->user->id)
                ->execute()->current();
        $antiguo = DB::query(Database::SELECT, 'SELECT MAX(DATEDIFF(NOW(), fecha_creacion)) AS dias
                FROM documentos WHERE id_user = :id AND estado = 0')
                ->param(':id', (int) $this->user->id)
                ->execute()->get('dias');

        $this->template->title = 'Ventanilla';
        $this->template->titulo = 'Ventanilla';
        $this->template->content = View::factory('ventanilla/menu')
                ->bind('user', $user)
                ->set('oficina', $oficina->loaded() ? $oficina->oficina : '')
                ->set('cuentas', $cuentas)
                ->set('antiguo', (int) $antiguo);
    }

    public function action_recibida() {

        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->template->content = View::factory('ventanilla/recibida')
                ->bind('user', $this->user);
    }
    // correspondencia recibida en ventanilla que todavia no se derivo
    public function action_pendientes() {
        // los datos se arman aqui: antes los traia una grilla que pedia
        // /ajaxd/ventanillajsonp/{id} con el id en la direccion y los filtros en SQL
        $q = trim(Arr::get($_GET, 'q', ''));
        // 'antiguos' separa el arrastre historico de lo que hay que derivar ahora
        $antiguos = Arr::get($_GET, 'antiguos', '');
        $where = 'd.id_user = :id AND d.estado = 0';
        if ($antiguos === '1') {
            $where .= ' AND d.fecha_creacion < NOW() - INTERVAL 1 YEAR';
        } elseif ($antiguos === '0') {
            $where .= ' AND d.fecha_creacion >= NOW() - INTERVAL 1 YEAR';
        }
        if ($q !== '') {
            $where .= ' AND (d.nur LIKE :q OR d.cite_original LIKE :q OR d.referencia LIKE :q
                    OR d.nombre_remitente LIKE :q OR d.institucion_remitente LIKE :q OR d.nombre_destinatario LIKE :q)';
        }
        $count = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM documentos d WHERE ' . $where)
                ->param(':id', (int) $this->user->id)
                ->param(':q', '%' . $q . '%')
                ->execute()->get('n');
        $pagination = Pagination::factory(array(
                    'total_items' => $count,
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => 40,
                    'view' => 'pagination/floating',
        ));
        $documentos = DB::query(Database::SELECT, 'SELECT d.id, d.nur, d.codigo, d.cite_original, d.referencia,
                    d.nombre_remitente, d.cargo_remitente, d.institucion_remitente,
                    d.nombre_destinatario, d.cargo_destinatario, d.fecha_creacion, d.hojas, d.adjuntos,
                    DATEDIFF(NOW(), d.fecha_creacion) AS dias
                FROM documentos d WHERE ' . $where . '
                ORDER BY d.fecha_creacion DESC LIMIT :desde, :cuantos')
                ->param(':id', (int) $this->user->id)
                ->param(':q', '%' . $q . '%')
                ->param(':desde', (int) $pagination->offset)
                ->param(':cuantos', (int) $pagination->items_per_page)
                ->execute()->as_array();

        $this->template->title .= ' / Pendientes';
        $this->template->titulo = 'Pendientes de derivar';
        $this->template->descripcion = 'Ventanilla | Correspondencia recibida y todavía sin derivar';
        $reparto = DB::query(Database::SELECT, 'SELECT
                    SUM(fecha_creacion >= NOW() - INTERVAL 1 YEAR) AS recientes,
                    SUM(fecha_creacion < NOW() - INTERVAL 1 YEAR) AS viejos
                FROM documentos WHERE id_user = :id AND estado = 0')
                ->param(':id', (int) $this->user->id)
                ->execute()->current();

        $this->template->content = View::factory('ventanilla/pendientes')
                ->set('documentos', $documentos)
                ->set('count', $count)
                ->set('q', $q)
                ->set('antiguos', $antiguos)
                ->set('reparto', $reparto)
                ->set('page_links', $pagination->render())
                ->bind('user', $this->user);
    }

    // todo lo que esta ventanilla registro, derivado o no
    public function action_listar() {
        $q = trim(Arr::get($_GET, 'q', ''));
        $estado = Arr::get($_GET, 'estado', '');
        $where = 'd.id_user = :id';
        if ($q !== '') {
            $where .= ' AND (d.nur LIKE :q OR d.cite_original LIKE :q OR d.referencia LIKE :q
                    OR d.nombre_remitente LIKE :q OR d.institucion_remitente LIKE :q OR d.nombre_destinatario LIKE :q)';
        }
        if ($estado === '0' OR $estado === '1') {
            $where .= ' AND d.estado = ' . (int) $estado;
        }
        $count = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM documentos d WHERE ' . $where)
                ->param(':id', (int) $this->user->id)
                ->param(':q', '%' . $q . '%')
                ->execute()->get('n');
        $pagination = Pagination::factory(array(
                    'total_items' => $count,
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => 40,
                    'view' => 'pagination/floating',
        ));
        $documentos = DB::query(Database::SELECT, 'SELECT d.id, d.nur, d.codigo, d.cite_original, d.referencia,
                    d.nombre_remitente, d.cargo_remitente, d.institucion_remitente,
                    d.nombre_destinatario, d.cargo_destinatario, d.fecha_creacion, d.estado,
                    DATEDIFF(NOW(), d.fecha_creacion) AS dias
                FROM documentos d WHERE ' . $where . '
                ORDER BY d.fecha_creacion DESC LIMIT :desde, :cuantos')
                ->param(':id', (int) $this->user->id)
                ->param(':q', '%' . $q . '%')
                ->param(':desde', (int) $pagination->offset)
                ->param(':cuantos', (int) $pagination->items_per_page)
                ->execute()->as_array();
        $totales = DB::query(Database::SELECT, 'SELECT COUNT(*) AS total, SUM(estado = 0) AS pendientes,
                    SUM(estado = 1) AS derivados FROM documentos WHERE id_user = :id')
                ->param(':id', (int) $this->user->id)
                ->execute()->current();

        $this->template->title .= ' / Recepcionados';
        $this->template->titulo = 'Correspondencia recepcionada';
        $this->template->descripcion = 'Ventanilla | Todo lo registrado por este usuario';
        $this->template->content = View::factory('ventanilla/lista_documentos')
                ->set('documentos', $documentos)
                ->set('count', $count)
                ->set('q', $q)
                ->set('estado', $estado)
                ->set('totales', $totales)
                ->set('page_links', $pagination->render());
    }

    public function action_pendientes_old() {
        $oVentanilla = New Model_documentos();
        $pendientes = $oVentanilla->pendiente_ventanilla($this->user->id);
        $this->template->title = 'Ventanilla | Pendientes';
        $this->template->titulo .='Pendientes';
        $this->template->descripcion .='Correspondencia recibida para derivacion';
        $this->template->styles = array('media/css/tablas.css' => 'screen');
        $this->template->content = View::factory('ventanilla/lista_pendientes')
                ->bind('pendientes', $pendientes);
    }

    //modulo para ventanilla    
    public function action_index() {
        //solo usuarios nivel 4 (ventanilla) pueden realizar esta accion
        if ($this->user->nivel == 4) {
            $errors = array();
            // si se envio datos mediante el metodo post creamos el documento
            $tipo = ORM::factory('tipos')->where('id', '=', 6)->find();
            if ($_POST) {
                /* $oOficina = New Model_Oficinas();    
                  $correlativo=$oOficina->correlativo($this->user->id_oficina, $tipo->id);
                  $abre=$oOficina->tipo($tipo->id);
                  $sigla=$oOficina->sigla($this->user->id_oficina);
                  if($abre!='') $abre=$abre.'/';
                  $codigo=$abre.$sigla.' Nº '.$correlativo.'/'.date('Y');
                 */
                $entidad = $this->user->id_entidad;
                $entidad = ORM::factory('entidades', $entidad);
                $oExt = New Model_nurs();
                // entidad/
                $codigo = $oExt->correlativo(-3, $entidad->doc_externo, $entidad->id, date('Y'));
                //CREAMOS UN NUEVO DOCUMENTO                
                $documento = ORM::factory('documentos');
                $documento->codigo = $codigo;
                $documento->cite_original = $_POST['cite'];
                $documento->id_tipo = 6;
                $documento->nombre_destinatario = $_POST['destinatario'];
                $documento->cargo_destinatario = $_POST['cargodes'];
                $documento->institucion_destinatario = $_POST['instituciondes'];
                // $fecha=strtotime($_POST['year'].'-'.$_POST['mes'].'-'.$_POST['dia']);
                $documento->nombre_remitente = $_POST['remitente'];
                $documento->cargo_remitente = $_POST['cargorem'];
                $documento->institucion_remitente = $_POST['institucionrem'];
                $documento->referencia = $_POST['descripcion'];
                $documento->adjuntos = $_POST['adjunto'];
                $documento->original = 1;
                $documento->hojas = (int) Arr::get($_POST, 'hojas', 0);
                // $documento->id_proceso=Arr::get($_POST,'proceso',1);
                $documento->fecha_creacion = date('Y-m-d H:i:s');
                $documento->id_user = $this->user->id;
                $documento->id_oficina = $this->user->id_oficina;
                $documento->id_proceso = 4;
                $documento->id_entidad = $this->user->id_entidad;
                $documento->nur = ''; // evita error "Field 'nur' doesn't have a default value"; se sobreescribe abajo
                $documento->save();
                if ($documento->id) {
                    $oNur = New Model_nurs();
                    //$nur=$oNur->correlativo(-1, $entidad->sigla2.'/',$this->user->id_entidad);
                    $nur = $oNur->correlativo(-2, $entidad->nur_externo, $entidad, date('Y'));
                    $nur_asignado = $oNur->asignarNur($nur, $this->user->id, $this->user->nombre);
                    $documento->nur = $nur_asignado;
                    $documento->save();
                    //cazamos al documento con el nur asignado
                    $rs = $documento->has('nurs', $nur_asignado);
                    $documento->add('nurs', $nur_asignado);
                    //descripcion el documento
                    $descripcion = ORM::factory('descripcion');
                    $descripcion->id_documento = $documento->id;
                    //$descripcion->id_grupo=$_POST['grupo'];
                    $descripcion->id_motivo = $_POST['motivo'];
                    // $descripcion->id_proceso=Arr::get($_POST,'proceso',1);
                    $descripcion->id_user = $this->user->id;
                    $descripcion->fecha = date('Y-m-d H:i:s');
                    $descripcion->save();
                    /*
                      $post = Validation::factory ( $_FILES )
                      ->rule('archivo', 'Upload::not_empty')
                      ->rule('archivo', 'Upload::type', array(':value', array('pdf')))
                      ->rule('archivo', 'Upload::size', array(':value', '20M'));
                      //si pasa la validacion guardamamos
                      if ($post->check ())
                      {
                      $path='archivos/'.date('Y_m');
                      if(!is_dir($path))
                      {
                      // Creates the directory
                      if(!mkdir($path, 0777, TRUE))
                      {
                      // On failure, throws an error
                      throw new Exception("No se puedo crear el directorio!");
                      exit;
                      }
                      }
                      $filename = upload::save ( $_FILES ['archivo'],uniqid().substr($documento->nur,-10).'.pdf',$path );
                      $archivo = ORM::factory ( 'archivos' ); //instanciamos el modelo
                      $archivo->nombre_archivo = basename($filename);
                      $archivo->extension = $_FILES ['archivo'] ['type'];
                      $archivo->tamanio = $_FILES ['archivo'] ['size'];
                      $archivo->id_user = $this->user->id;
                      $archivo->id_documento = $documento->id;
                      $archivo->sub_directorio = date('Y_m');
                      $archivo->fecha = date('Y-m-d H:i:s');
                      $archivo->save ();
                      }
                     */
                    $_POST = array();
                    $_FILES = array();
                    $this->request->redirect('ventanilla/edit/' . $documento->id);
                }
            }
            $days = array();
            for ($i = 1; $i < 32; $i++) {
                $days[$i] = $i;
            }
            $months = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
            $years = array();
            $year = date('Y');
            for ($i = ($year - 1); $i <= $year; $i++) {
                $years[$i] = $i;
            }
            $grupos = array();
            //GRUPOS
            $result = ORM::factory('grupos')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $grupos[$r->id] = $r->grupo;
            }
            $motivos = array();
            //GRUPOS
            $result = ORM::factory('motivos')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $motivos[$r->id] = $r->motivo;
            }
            //PROCESOS
            $procesos = array();
            $result = ORM::factory('procesosx')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $procesos[$r->id] = $r->proceso;
            }
            //destinatarios
            $oDestinatario = New Model_Destinatarios();
            $destinos = $oDestinatario->destinos($this->user->id);
            $oficina = $this->user->id_oficina;

            // Instituciones y remitentes ya usados por esta ventanilla. Sirven para que al
            // escribir se elija uno existente en vez de teclear otra variante: hoy la misma
            // institucion esta escrita de varias formas y eso rompe busquedas y reportes.
            $instituciones = DB::query(Database::SELECT, "SELECT institucion_remitente AS v, COUNT(*) AS n
                    FROM documentos
                    WHERE id_user = :id AND TRIM(REPLACE(institucion_remitente, '.', '')) <> ''
                    GROUP BY institucion_remitente ORDER BY n DESC LIMIT 400")
                    ->param(':id', (int) $this->user->id)->execute()->as_array();
            $remitentes = DB::query(Database::SELECT, "SELECT nombre_remitente AS n, cargo_remitente AS c,
                        institucion_remitente AS i, COUNT(*) AS veces
                    FROM documentos
                    WHERE id_user = :id AND TRIM(REPLACE(nombre_remitente, '.', '')) <> ''
                      AND fecha_creacion >= NOW() - INTERVAL 3 YEAR
                    GROUP BY nombre_remitente, cargo_remitente, institucion_remitente
                    ORDER BY veces DESC LIMIT 400")
                    ->param(':id', (int) $this->user->id)->execute()->as_array();
            // lo registrado hoy: para ver lo hecho y notar si algo se esta repitiendo
            $hoy = DB::query(Database::SELECT, "SELECT id, nur, cite_original, referencia, nombre_remitente,
                        institucion_remitente, estado, DATE_FORMAT(fecha_creacion, '%H:%i') AS hora
                    FROM documentos WHERE id_user = :id AND DATE(fecha_creacion) = CURDATE()
                    ORDER BY id DESC LIMIT 12")
                    ->param(':id', (int) $this->user->id)->execute()->as_array();

            $this->template->scripts = array('static/js/libs/select2/select2.min.js', 'static/js/libs/bootstrap-datepicker/bootstrap-datepicker.js');
            $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all', 'static/css/theme-1/libs/bootstrap-datepicker/datepicker3.css' => 'screen');

            //$this->template->scripts    = array('ckeditor/adapters/jquery.js','ckeditor/ckeditor.js');            
            $this->template->title .= ' / Recepción de documentos externos';
            $this->template->titulo .= 'Recepción';
            $this->template->descripcion = ' Formulario de recepcion de correspondencia externa';
            $this->template->content = View::factory('ventanilla/reception')
                    ->bind('days', $days)
                    ->bind('months', $months)
                    ->bind('years', $years)
                    ->bind('grupos', $grupos)
                    ->bind('motivos', $motivos)
                    ->bind('procesos', $procesos)
                    ->bind('destinos', $destinos)
                    ->set('instituciones', $instituciones)
                    ->set('remitentes', $remitentes)
                    ->set('hoy', $hoy)
                    ->bind('oficina', $oficina);
        }
    }

    //editar recepcion de documentos mediante vetanilla
    public function action_edit($id) {
        $error = array();
        $info = array();
        if (isset($_POST['submit'])) {
            $documento = ORM::factory('documentos', $id);
            $documento->cite_original = $_POST['cite'];
            $documento->id_tipo = 6;
            $documento->nombre_destinatario = $_POST['destinatario'];
            $documento->cargo_destinatario = $_POST['cargodes'];
            $documento->institucion_destinatario = $_POST['instituciondes'];
            // $fecha=strtotime($_POST['year'].'-'.$_POST['mes'].'-'.$_POST['dia']);
            $documento->nombre_remitente = $_POST['remitente'];
            $documento->cargo_remitente = $_POST['cargorem'];
            $documento->institucion_remitente = $_POST['institucionrem'];
            $documento->referencia = $_POST['descripcion'];
            $documento->adjuntos = $_POST['adjunto'];
            $documento->hojas = (int) Arr::get($_POST, 'hojas', 0);
            // $documento->id_proceso=Arr::get($_POST,'proceso',1);
            $documento->id_proceso = 4;
            $documento->save();
            if ($_FILES['archivo']['name'] != '') {
                $post = Validation::factory($_FILES)
                        ->rule('archivo', 'Upload::not_empty')
                        ->rule('archivo', 'Upload::type', array(':value', array('pdf','PDF')))
                 ->rule('archivo', 'Upload::size', array(':value', '1024M'));
                //si pasa la validacion guardamamos 
                if ($post->check()) {
                    $path = rtrim(Kohana::$config->load('archivo')->get('path'), '/\\') . '/' . date('Y_m');
                    if (!is_dir($path)) {
                        // Creates the directory 
                        if (!mkdir($path, 0777, TRUE)) {
                            // On failure, throws an error 
                            throw new Exception("No se puedo crear el directorio!");
                            exit;
                        }
                    }
                    $filename = upload::save($_FILES ['archivo'], uniqid() . substr($documento->nur, -10) . '.pdf', $path);
                    //$archivo = ORM::factory('archivos', $_POST['id_archivo']); //intanciamos el modelo proveedor
                    $archivo = ORM::factory('archivos'); //intanciamos el modelo proveedor
                    $archivo->nombre_archivo = basename($filename);
                    $archivo->extension = $_FILES ['archivo'] ['type'];
                    $archivo->tamanio = $_FILES ['archivo'] ['size'];
                    $archivo->id_user = $this->user->id;
                    $archivo->id_documento = $documento->id;
                    $archivo->sub_directorio = date('Y_m');
                    $archivo->fecha = date('Y-m-d H:i:s');
                    $archivo->save();
                    $info['Documento escaneado: '] = 'Documento subido con exito';
                } else {
                    $error['Documento escaneado: '] = 'El documento debe ser pdf y de un tamaño no mayor a 50M';
                }
            }
            $_POST = array();
            $_FILES = array();
        }
        $documento = ORM::factory('documentos')->where('id', '=', $id)->and_where('id_user', '=', $this->user->id)->find();
        if ($documento->loaded()) {
            $days = array();
            for ($i = 1; $i < 32; $i++) {
                $days[$i] = $i;
            }
            $months = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
            $years = array();
            $year = date('Y');
            for ($i = ($year - 1); $i <= $year; $i++) {
                $years[$i] = $i;
            }
            $grupos = array();
            //GRUPOS
            $result = ORM::factory('grupos')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $grupos[$r->id] = $r->grupo;
            }
            $motivos = array();
            //GRUPOS
            $result = ORM::factory('motivos')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $motivos[$r->id] = $r->motivo;
            }
            //PROCESOS
            $procesos = array();
            $result = ORM::factory('procesosx')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $procesos[$r->id] = $r->proceso;
            }
            $archivo = ORM::factory('archivos')->where('id_documento', '=', $id)
                    ->find();
            //destinatarios            
            $oDestinatario = New Model_Destinatarios();
            $destinos = $oDestinatario->destinos($this->user->id);
            $this->template->title.=' / Editar ' . substr($documento->nur, 1);
            //$this->template->titulo.='Editar ' . substr($documento->codigo, 1);
            $this->template->descripcion = 'Editar correspondencia entrante';
            $this->template->content = View::factory('ventanilla/editar')
                    ->bind('documento', $documento)
                    ->bind('days', $days)
                    ->bind('months', $months)
                    ->bind('years', $years)
                    ->bind('grupos', $grupos)
                    ->bind('motivos', $motivos)
                    ->bind('procesos', $procesos)
                    ->bind('archivo', $archivo)
                    ->bind('destinos', $destinos)
                    ->bind('error', $error)
                    ->bind('info', $info);
        } else {
            $this->template->title.=' / Editar ' . substr($documento->codigo, 1);
            $this->template->titulo.='Editar ' . substr($documento->codigo, 1);
            $this->template->descripcion = 'Editar correspondencia entrante';
            $this->template->content = '<div role="alert" class="alert alert-danger">
                    No tiene permisos para editar este documento. cualquier duda comuniquese con el administrador.
                </div>';
        }
    }

    public function action_outbox() {
        $info = array();
        $oSeg = New Model_Seguimiento();
        $entrada = $oSeg->enviados($this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->title .= ' / Correspondencia enviada';
        $this->template->titulo = '<v>Correspondencia / </v> Enviada';
        $this->template->descripcion = 'CORRESPONDENCIA ENVIADA QUE AUN NO FUE RECIBIDA POR EL DESTINATARIO';
        $this->template->content = View::factory('correspondencia/enviados')
                ->bind('entrada', $entrada)
                ->bind('info', $info);
    }

}

?>
