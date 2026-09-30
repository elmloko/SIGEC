<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Hojasruta extends Controller_AdminTemplate
{

    protected $user;
    protected $menus;

    public function before()
    {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($this->user->nivel);
            parent::before();
            $this->template->titulo = '<v>Hojas de Ruta / </v> ';
            $this->template->descripcion = '';
            $this->template->username = $this->user->nombre;
        } else {
            $this->request->redirect('/login');
        }
    }

    public function after()
    {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'admin');
        $oSM = New Model_menus();
        $submenus = $oSM->submenus('admin');
        $this->template->submenu = View::factory('templates/submenu')->bind('smenus', $submenus)->set('titulo', 'Administrar');
        parent::after();
    }

    public function action_index()
    {
        $this->request->redirect('/admin/hojasruta/lista');
    }

    // listado general de todas las hojas de ruta generadas en el sistema
    public function action_lista()
    {
        $filtros = array(
            'q' => trim((string) Arr::get($_GET, 'q', '')),
            'anio' => (int) Arr::get($_GET, 'anio', 0),
            'tipo' => (int) Arr::get($_GET, 'tipo', 0),
            'estado' => Arr::get($_GET, 'estado', '') === '' ? '' : (int) Arr::get($_GET, 'estado'),
        );
        $items_per_page = 50;

        $oHR = New Model_Hojasruta();
        $count = $oHR->contarTodas($filtros);

        $pagination = Pagination::factory(array(
            'total_items' => $count,
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'items_per_page' => $items_per_page,
            'view' => 'pagination/floating',
        ));

        $hojasruta = $oHR->todasAdmin($pagination->offset, $items_per_page, $filtros);
        $page_links = $pagination->render();

        // opciones de los filtros
        $anios = DB::query(Database::SELECT, "SELECT DISTINCT YEAR(fecha_creacion) AS a FROM documentos WHERE original = 1 AND fecha_creacion IS NOT NULL ORDER BY a DESC")
                ->execute()->as_array(NULL, 'a');
        $tipos = DB::query(Database::SELECT, "SELECT id, tipo FROM tipos ORDER BY tipo")->execute()->as_array('id', 'tipo');
        $estados = DB::query(Database::SELECT, "SELECT id, estado FROM estados ORDER BY id")->execute()->as_array('id', 'estado');

        $this->template->title .= ' Hojas de Ruta';
        $this->template->titulo .= 'Documentos y hojas de ruta';
        $this->template->descripcion .= ' Listado general de hojas de ruta generadas en el sistema';
        $this->template->content = View::factory('admin/hojasruta/lista')
            ->bind('hojasruta', $hojasruta)
            ->bind('count', $count)
            ->bind('page_links', $page_links)
            ->bind('filtros', $filtros)
            ->bind('anios', $anios)
            ->bind('tipos', $tipos)
            ->bind('estados', $estados)
            ->set('aviso', Session::instance()->get_once('hr_aviso', ''))
            ->set('q', $filtros['q']);
    }
    // editar los datos del documento asociado a la hoja de ruta
    public function action_editar($id = '')
    {
        $error = array();
        $info = array();

        $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
        if (!$documento->loaded()) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        // adjuntar un archivo digital al documento
        if (isset($_POST['adjuntar']) AND !empty($_FILES['archivo']['name'])) {
            try {
                $sub_directorio = date('Y_m');
                if (RemoteArchivo::is_enabled()) {
                    $filename = uniqid() . $_FILES['archivo']['name'];
                    RemoteArchivo::upload($_FILES['archivo']['tmp_name'], $sub_directorio . '/' . $filename);
                } else {
                    $path = rtrim(Kohana::$config->load('archivo')->get('path'), '/\\') . '/' . $sub_directorio;
                    if (!is_dir($path) AND !mkdir($path, 0777, TRUE)) {
                        throw new Exception('No se pudo crear la carpeta de archivos.');
                    }
                    $filename = upload::save($_FILES['archivo'], NULL, $path);
                }
                $nuevo = ORM::factory('archivos');
                $nuevo->nombre_archivo = basename($filename);
                $nuevo->extension = $_FILES['archivo']['type'];
                $nuevo->tamanio = $_FILES['archivo']['size'];
                $nuevo->id_user = $this->user->id;
                $nuevo->id_documento = $documento->id;
                $nuevo->sub_directorio = $sub_directorio;
                $nuevo->fecha = date('Y-m-d H:i:s');
                $nuevo->estado = 1;
                $nuevo->save();
                $this->save($this->user->id_entidad, $this->user->id, 'Administrador adjunto un archivo al documento <b>' . $documento->codigo . '</b> (hoja de ruta ' . $documento->nur . ')');
                $info['Exito!'] = 'Se adjuntó el archivo.';
            } catch (Exception $e) {
                Kohana::$log->add(Log::ERROR, 'adjuntar archivo: ' . $e->getMessage());
                $error['Error'] = 'No se pudo subir el archivo.';
            }
        }

        // quitar un archivo digital (baja logica: el archivo queda en el servidor)
        if (isset($_POST['quitar_archivo'])) {
            $arch = ORM::factory('archivos', (int) $_POST['quitar_archivo']);
            if ($arch->loaded() AND (int) $arch->id_documento === (int) $documento->id) {
                $arch->estado = 0;
                $arch->save();
                $this->save($this->user->id_entidad, $this->user->id, 'Administrador quito el archivo <b>' . substr($arch->nombre_archivo, 13) . '</b> del documento ' . $documento->codigo);
                $info['Exito!'] = 'Se quitó el archivo digital.';
            } else {
                $error['Error'] = 'Ese archivo no pertenece a este documento.';
            }
        }

        if (isset($_POST['editar'])) {
            $nur_original = $documento->nur;
            $nuevo_nur = trim($_POST['nur']);
            $nuevo_codigo = trim($_POST['codigo']);
            $nueva_fecha = trim($_POST['fecha_creacion']);

            if ($nuevo_nur === '') {
                $error['Error'] = 'El número de hoja de ruta no puede quedar vacío.';
            } elseif ($nuevo_codigo === '') {
                $error['Error'] = 'El código del documento no puede quedar vacío.';
            } elseif (trim($_POST['referencia']) === '') {
                $error['Error'] = 'La referencia no puede quedar vacía.';
            } else {
                // la fecha se guarda tal cual en la base: si no es valida rompe el expediente
                $f = date_create($nueva_fecha);
                if (!$f OR $f->format('Y-m-d H:i:s') !== $nueva_fecha) {
                    $error['Error'] = 'La fecha de creación debe tener el formato AAAA-MM-DD HH:MM:SS (por ejemplo ' . date('Y-m-d H:i:s') . ').';
                } elseif ($f->format('Y') < 2000 OR $f > new DateTime('+1 day')) {
                    $error['Error'] = 'La fecha de creación está fuera de lo razonable: revise el año.';
                }
            }

            // validamos que el codigo no se duplique con otro documento
            if (sizeof($error) == 0 AND $nuevo_codigo !== $documento->codigo) {
                $existe_codigo = ORM::factory('documentos')
                    ->where('codigo', '=', $nuevo_codigo)
                    ->and_where('id', '!=', $documento->id)
                    ->find();
                if ($existe_codigo->loaded()) {
                    $error['Error'] = 'Ya existe otro documento con el codigo <b>' . HTML::chars($nuevo_codigo) . '</b>.';
                }
            }

            // validamos que el nuevo nur no exista ya (para no fusionar dos expedientes)
            if (sizeof($error) == 0 && $nuevo_nur !== $nur_original) {
                $existe_nur = ORM::factory('nurs')->where('nur', '=', $nuevo_nur)->find();
                if ($existe_nur->loaded()) {
                    $error['Error'] = 'Ya existe otra hoja de ruta con el NUR <b>' . HTML::chars($nuevo_nur) . '</b>.';
                }
            }

            if (sizeof($error) == 0) {
                if ($nuevo_nur !== $nur_original) {
                    // el cambio toca seis tablas: o se hace entero o no se hace, para no partir el expediente
                    $db = Database::instance();
                    $db->begin();
                    try {
                        $enur_old = $db->escape($nur_original);
                        $enur_new = $db->escape($nuevo_nur);

                        db::query(Database::UPDATE, 'UPDATE seguimiento SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                        db::query(Database::UPDATE, 'UPDATE agrupaciones SET padre = ' . $enur_new . ' WHERE padre = ' . $enur_old)->execute();
                        db::query(Database::UPDATE, 'UPDATE agrupaciones SET hijo = ' . $enur_new . ' WHERE hijo = ' . $enur_old)->execute();
                        db::query(Database::UPDATE, 'UPDATE hojasruta SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                        db::query(Database::UPDATE, 'UPDATE documentos SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                        db::query(Database::UPDATE, 'UPDATE nurs SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollback();
                        Kohana::$log->add(Log::ERROR, 'cambio de NUR: ' . $e->getMessage());
                        $error['Error'] = 'No se pudo cambiar el número de hoja de ruta: no se modificó nada.';
                    }

                    if (sizeof($error) == 0) {
                        $this->save($this->user->id_entidad, $this->user->id, 'Administrador cambio el NUR ' . $nur_original . ' a ' . $nuevo_nur . ' (actualizacion en cascada)');

                        // recargamos el documento porque su nur ya cambio directamente en la BD
                        $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
                    }
                }
            }

            if (sizeof($error) == 0) {

                $documento->codigo = $nuevo_codigo;
                $documento->cite_original = trim($_POST['cite_original']);
                $documento->referencia = trim($_POST['referencia']);
                $documento->nombre_remitente = trim($_POST['nombre_remitente']);
                $documento->cargo_remitente = trim($_POST['cargo_remitente']);
                $documento->nombre_destinatario = trim($_POST['nombre_destinatario']);
                $documento->cargo_destinatario = trim($_POST['cargo_destinatario']);
                $documento->fecha_creacion = trim($_POST['fecha_creacion']);
                $documento->save();

                $this->save($this->user->id_entidad, $this->user->id, 'Administrador edito el registro del documento de la hoja de ruta ' . $documento->nur);

                $info['Exito!'] = 'Se actualizaron correctamente los datos del documento.';
            }
        }

        // que arrastra consigo el cambio de numero: se le muestra al administrador antes de guardar
        $alcance = DB::query(Database::SELECT, 'SELECT
                    (SELECT COUNT(*) FROM seguimiento WHERE nur = :nur) AS pasos,
                    (SELECT COUNT(*) FROM documentos WHERE nur = :nur) AS documentos,
                    (SELECT COUNT(*) FROM agrupaciones WHERE padre = :nur OR hijo = :nur) AS agrupaciones')
                ->param(':nur', (string) $documento->nur)
                ->execute()->current();
        $autor = ORM::factory('users', $documento->id_user);
        $archivos = ORM::factory('archivos')->where('id_documento', '=', $documento->id)
                ->and_where('estado', '=', 1)->order_by('fecha')->find_all();

        $this->template->title .= ' / Editar ' . $documento->nur;
        $this->template->titulo .= ' Editar ' . $documento->nur;
        $this->template->descripcion .= ' Editar los datos del documento de la hoja de ruta';
        $this->template->content = View::factory('admin/hojasruta/editar')
            ->bind('documento', $documento)
            ->set('alcance', $alcance)
            ->set('autor', $autor->loaded() ? $autor->nombre : '')
            ->set('archivos', $archivos)
            ->set('aviso_error', Session::instance()->get_once('hr_aviso_error', ''))
            ->bind('error', $error)
            ->bind('info', $info);
    }

    // crea un informe (documento) minimo para un paso de la linea de tiempo que todavia no
    // tiene ninguno adjuntado, y de una vez manda a completarlo (datos + archivo) en editarinforme
    public function action_crearinforme($id_seguimiento = '')
    {
        $seguimiento = ORM::factory('seguimiento', $id_seguimiento);
        if (!$seguimiento->loaded()) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        $nur = Arr::get($_GET, 'nur', $seguimiento->nur);

        // si ya se habia creado un informe para este paso (ej. el usuario volvio a hacer clic),
        // no creamos otro: vamos directo a editar el que ya existe
        $existente = ORM::factory('documentos')->where('id_seguimiento', '=', $seguimiento->id)->find();
        if ($existente->loaded()) {
            $this->request->redirect('/admin/hojasruta/editarinforme/' . $existente->id . '?seg=' . $seguimiento->id . '&nur=' . urlencode($nur));
        }

        $documento = ORM::factory('documentos');
        $documento->nur = $nur;
        $documento->id_seguimiento = $seguimiento->id;
        $documento->id_user = $this->user->id;
        $documento->original = 0;
        $documento->fecha_creacion = date('Y-m-d H:i:s');
        // 'codigo' es UNIQUE en la BD: le agregamos algo variable para que nunca choque
        $documento->codigo = 'Informe paso ' . $seguimiento->id . ' - ' . date('YmdHis');
        // precargamos con los datos del propio paso (quien lo emitio / quien lo recibio),
        // igual que hace route/responder, para no dejar el formulario en blanco
        $documento->nombre_destinatario = $seguimiento->nombre_emisor;
        $documento->cargo_destinatario = $seguimiento->cargo_emisor;
        $documento->nombre_remitente = $seguimiento->nombre_receptor;
        $documento->cargo_remitente = $seguimiento->cargo_receptor;
        $documento->referencia = $seguimiento->proveido;
        $documento->save();

        $this->save($this->user->id_entidad, $this->user->id, 'Administrador creo un informe para el paso ' . $seguimiento->id . ' de la hoja de ruta ' . $nur);

        $this->request->redirect('/admin/hojasruta/editarinforme/' . $documento->id . '?seg=' . $seguimiento->id . '&nur=' . urlencode($nur));
    }

    // editar el informe (documento) generado en un paso especifico de la linea de tiempo del
    // seguimiento, incluyendo reemplazar/agregar su archivo adjunto. A diferencia de
    // documento/edit, aqui no se exige que el documento pertenezca al usuario logeado.
    public function action_editarinforme($id = '')
    {
        $error = array();
        $info = array();

        $documento = ORM::factory('documentos', $id);
        if (!$documento->loaded()) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        // paso de la linea de tiempo (seguimiento) donde se genero/adjunto este informe,
        // para poder corregir la fecha que se ve en "Seguimiento del proceso".
        // Va por query string ("?seg=") porque la ruta admin solo admite un segmento de id.
        $id_seguimiento = Arr::get($_GET, 'seg', '');
        $seguimiento = ($id_seguimiento !== '') ? ORM::factory('seguimiento', $id_seguimiento) : ORM::factory('seguimiento', $documento->id_seguimiento);

        // NUR de la hoja de ruta desde donde se entro (para "Volver al seguimiento"): no siempre
        // coincide con $documento->nur, porque el informe/archivo de un paso puede pertenecer a
        // un documento distinto al de la hoja de ruta que se esta viendo.
        $nur_origen = Arr::get($_GET, 'nur', $documento->nur);

        if (isset($_POST['editar'])) {
            $documento->titulo = trim(Arr::get($_POST, 'titulo', ''));
            $documento->referencia = trim(Arr::get($_POST, 'referencia', ''));
            $documento->contenido = Arr::get($_POST, 'descripcion', '');
            $documento->nombre_destinatario = trim(Arr::get($_POST, 'destinatario', ''));
            $documento->cargo_destinatario = trim(Arr::get($_POST, 'cargo_des', ''));
            $documento->institucion_destinatario = trim(Arr::get($_POST, 'institucion_des', ''));
            $documento->nombre_remitente = trim(Arr::get($_POST, 'remitente', ''));
            $documento->cargo_remitente = trim(Arr::get($_POST, 'cargo_rem', ''));
            $documento->mosca_remitente = trim(Arr::get($_POST, 'mosca', ''));
            $documento->copias = trim(Arr::get($_POST, 'copias', ''));
            // la columna es numerica: con la base en modo estricto, un texto como "." hace fallar el guardado
            $documento->hojas = (int) Arr::get($_POST, 'hojas', 0);
            $documento->nombre_via = trim(Arr::get($_POST, 'via', ''));
            $documento->cargo_via = trim(Arr::get($_POST, 'cargovia', ''));
            $nueva_fecha_creacion = trim(Arr::get($_POST, 'fecha_creacion', ''));
            if ($nueva_fecha_creacion !== '') {
                $documento->fecha_creacion = $nueva_fecha_creacion;
            }
            $documento->save();

            if ($seguimiento->loaded()) {
                $nueva_fecha_paso = trim(Arr::get($_POST, 'fecha_paso', ''));
                if ($nueva_fecha_paso !== '') {
                    $seguimiento->fecha_emision = $nueva_fecha_paso;
                    $seguimiento->save();
                }
            }

            $this->save($this->user->id_entidad, $this->user->id, 'Administrador edito el informe <b>' . $documento->codigo . '</b> (NUR ' . $documento->nur . ')');
            $info['Exito!'] = 'Se actualizaron correctamente los datos del informe.';
        }

        if (isset($_POST['adjuntar']) && !empty($_FILES['archivo']['name'])) {
            $sub_directorio = date('Y_m');
            if (RemoteArchivo::is_enabled()) {
                $filename = uniqid() . $_FILES['archivo']['name'];
                RemoteArchivo::upload($_FILES['archivo']['tmp_name'], $sub_directorio . '/' . $filename);
            } else {
                $path = rtrim(Kohana::$config->load('archivo')->get('path'), '/\\') . '/' . $sub_directorio;
                if (!is_dir($path)) {
                    if (!mkdir($path, 0777, TRUE)) {
                        throw new Exception("No se puedo crear el directorio!");
                    }
                }
                $filename = upload::save($_FILES['archivo'], NULL, $path);
            }

            $archivo = ORM::factory('archivos');
            $archivo->nombre_archivo = basename($filename);
            $archivo->extension = $_FILES['archivo']['type'];
            $archivo->tamanio = $_FILES['archivo']['size'];
            $archivo->id_user = $this->user->id;
            $archivo->id_documento = $documento->id;
            $archivo->sub_directorio = $sub_directorio;
            $archivo->fecha = date('Y-m-d H:i:s');
            $archivo->estado = 1;
            $archivo->save();

            $this->save($this->user->id_entidad, $this->user->id, 'Administrador adjunto un archivo al informe <b>' . $documento->codigo . '</b> (NUR ' . $documento->nur . ')');
            $info['Exito!'] = 'Archivo adjuntado correctamente.';
        }

        $archivos = ORM::factory('archivos')
            ->where('id_documento', '=', $documento->id)
            ->and_where('estado', '=', 1)
            ->find_all();

        $this->template->title .= ' / Editar informe ' . $documento->codigo;
        $this->template->titulo .= ' Editar informe ' . $documento->codigo;
        $this->template->descripcion .= ' Editar los datos y el adjunto del informe generado en este paso de la hoja de ruta ' . $documento->nur;
        $this->template->content = View::factory('admin/hojasruta/editarinforme')
            ->bind('documento', $documento)
            ->bind('archivos', $archivos)
            ->bind('seguimiento', $seguimiento)
            ->bind('nur_origen', $nur_origen)
            ->bind('error', $error)
            ->bind('info', $info);
    }

    // elimina (soft-delete) un adjunto de un informe, sin restriccion de propietario (solo admin)
    public function action_eliminararchivoinforme($id = '')
    {
        // conservamos seg/nur para volver exactamente a la misma pantalla (ver nota en action_editarinforme)
        $volver = '?' . http_build_query(array_filter(array(
            'seg' => Arr::get($_GET, 'seg', ''),
            'nur' => Arr::get($_GET, 'nur', ''),
        )));

        $archivo = ORM::factory('archivos', $id);
        if ($archivo->loaded()) {
            $id_documento = $archivo->id_documento;
            $archivo->estado = 0;
            $archivo->save();
            $this->save($this->user->id_entidad, $this->user->id, 'Administrador elimino un adjunto del informe id ' . $id_documento);
            $this->request->redirect('/admin/hojasruta/editarinforme/' . $id_documento . $volver);
        }
        $this->request->redirect('/admin/hojasruta/lista');
    }

    // detalle de agrupacion: muestra si la hoja de ruta fue agrupada como padre y/o como hijo
    public function action_grupo($id = '')
    {
        $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
        if (!$documento->loaded()) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        $oHR = New Model_Hojasruta();
        $hijos = $oHR->HRhijos($documento->nur);
        $padre = $oHR->HRpadre($documento->nur);

        $this->template->title .= ' / Agrupacion ' . $documento->nur;
        $this->template->titulo .= ' Agrupacion de ' . $documento->nur;
        $this->template->descripcion .= ' Documentos agrupados en esta hoja de ruta';
        $this->template->content = View::factory('admin/hojasruta/grupo')
            ->bind('documento', $documento)
            ->bind('hijos', $hijos)
            ->bind('padre', $padre);
    }

    // quita una hoja de ruta de un grupo (deshace lo hecho por bandeja/agruparf)
    public function action_desagrupar($id_agrupacion = '')
    {
        if (!isset($_POST['confirmar'])) {
            $this->request->redirect('/admin/hojasruta/lista');
        }
        $documento_id = (int) Arr::get($_POST, 'documento_id', 0);

        $agrupacion = ORM::factory('agrupaciones', $id_agrupacion);
        if ($agrupacion->loaded()) {
            $padre = $agrupacion->padre;
            $hijo = $agrupacion->hijo;

            // revertimos el estado "Agrupado" (6) del seguimiento afectado a "Recibido/Accion pendiente" (2)
            $seguimiento = ORM::factory('seguimiento', $agrupacion->id_seguimiento);
            if ($seguimiento->loaded() && (int) $seguimiento->estado === 6) {
                $seguimiento->estado = 2;
                $seguimiento->save();
            }

            $agrupacion->delete();

            // si el nur "padre" ya no tiene ninguna otra hoja de ruta agrupada, quitamos el aviso "Agrupado con:"
            // de todos sus pasos de seguimiento (el flag "hijo" se propaga a cada derivacion del expediente)
            $quedan = ORM::factory('agrupaciones')->where('padre', '=', $padre)->count_all();
            if ($quedan == 0) {
                $epadre = Database::instance()->escape($padre);
                db::query(Database::UPDATE, "UPDATE seguimiento SET hijo = '0' WHERE nur = " . $epadre)->execute();
            }

            $this->save($this->user->id_entidad, $this->user->id, 'Administrador desagrupo la hoja de ruta ' . $hijo . ' del grupo ' . $padre);
        }

        $this->request->redirect('/admin/hojasruta/grupo/' . $documento_id);
    }

    // revierte (cancela) la ultima derivacion de la hoja de ruta, solo si aun no fue recibida
    public function action_revertir($id = '')
    {
        // volvemos a la pagina desde donde se pidio revertir (lista de hojas de ruta o el seguimiento de una en particular)
        $redirect_to = '/admin/hojasruta/lista';
        if (!empty($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) === $_SERVER['HTTP_HOST']) {
            $redirect_to = $_SERVER['HTTP_REFERER'];
        }

        if (!isset($_POST['confirmar'])) {
            $this->request->redirect($redirect_to);
        }

        $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
        if (!$documento->loaded() || $documento->nur === '') {
            $this->request->redirect($redirect_to);
        }
        $nur = $documento->nur;

        // tomamos el ultimo paso de seguimiento (la derivacion mas reciente) de esta hoja de ruta
        // (solo llega aqui un administrador nivel 5: Controller_AdminTemplate::before() ya lo exige)
        $seg = ORM::factory('seguimiento')->where('nur', '=', $nur)->order_by('id', 'DESC')->find();

        if ($seg->loaded() && (int) $seg->estado === 1) {
            $padre = $seg->id_seguimiento;
            $oficial = $seg->oficial;
            $seg->delete();

            if ($padre > 0) {
                // habia un seguimiento anterior en la cadena: lo restauramos a pendiente
                $oSeg = New Model_Seguimiento();
                $oSeg->delete_deriv($padre);
                $seguimiento = ORM::factory('seguimiento', array('id' => $padre));
                if ($seguimiento->oficial == 2) {
                    $seguimiento->oficial = 1;
                }
                $seguimiento->estado = 2; //pendiente
                $seguimiento->save();
            } else {
                // era la primera derivacion: el documento vuelve a estado "no derivado"
                if ($oficial == 1) {
                    $documento->estado = 0;
                    $documento->save();
                }
            }

            $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' revirtio la derivacion de la hoja de ruta ' . $nur);
        }

        $this->request->redirect($redirect_to);
    }

    // agrega un destinatario adicional EN COPIA a una derivacion ya enviada (en representacion del
    // usuario que la hizo), para cuando este se olvido de incluir a alguien. Solo administradores.
    public function action_agregar($id_ref = '')
    {
        // solo administradores (nivel 5) pueden suplantar al remitente original para agregar un destinatario
        if ((int) $this->user->nivel !== 5) {
            $this->request->redirect('/');
        }

        $errors = array();

        $ref = ORM::factory('seguimiento', $id_ref);
        if (!$ref->loaded()) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        $documento = ORM::factory('documentos')->where('nur', '=', $ref->nur)->and_where('original', '=', 1)->find();
        if (!$documento->loaded()) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        $remitente = ORM::factory('users', $ref->derivado_por);

        if (isset($_POST['enviar'])) {
            $id_destino = (int) Arr::get($_POST, 'destino', 0);
            $accion = (int) Arr::get($_POST, 'accion', 0);
            $proveido = trim(Arr::get($_POST, 'proveido', ''));

            if ($id_destino <= 0) {
                $errors['Error'] = 'Debe seleccionar un destinatario.';
            }
            if ($proveido === '') {
                $errors['Error'] = 'Debe ingresar un proveido.';
            }

            if (sizeof($errors) == 0) {
                $destino = ORM::factory('users', $id_destino);
                $oficina_destino = ORM::factory('oficinas', $destino->id_oficina);

                $seg = ORM::factory('seguimiento');
                $seg->id_seguimiento = $ref->id_seguimiento;
                $seg->nur = $ref->nur;
                $seg->derivado_por = $ref->derivado_por;
                $seg->nombre_emisor = $ref->nombre_emisor;
                $seg->cargo_emisor = $ref->cargo_emisor;
                $seg->fecha_emision = date('Y-m-d H:i:s');
                $seg->derivado_a = $destino->id;
                $seg->nombre_receptor = $destino->nombre;
                $seg->cargo_receptor = $destino->cargo;
                $seg->estado = 1; //no recibido
                $seg->accion = $accion;
                $seg->oficial = 0; //siempre en copia: no se debe duplicar el tramite oficial ya existente
                $seg->hijo = $ref->hijo;
                $seg->proveido = $proveido;
                $seg->adjuntos = json_encode(array());
                $seg->de_oficina = $ref->de_oficina;
                $seg->a_oficina = $oficina_destino->oficina;
                $seg->id_de_oficina = $ref->id_de_oficina;
                $seg->id_a_oficina = $oficina_destino->id;
                $seg->prioridad = $ref->prioridad;
                $seg->save();

                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' agrego a ' . $destino->nombre . ' como destinatario en copia de la hoja de ruta ' . $ref->nur . ' (en representacion de ' . $ref->nombre_emisor . ', quien lo olvido)');

                $this->request->redirect('/route/trace/?hr=' . $ref->nur);
            }
        }

        $oDestino = New Model_Destinatarios();
        $destinatarios = array();
        foreach ($oDestino->dependientes($ref->derivado_por) as $l) {
            $destinatarios[$l['id']] = $l['oficina'] . ' - ' . Text::limit_words($l['nombre'], 6, '');
        }
        foreach ($oDestino->superior($remitente->superior) as $l) {
            $destinatarios[$l['id']] = $l['oficina'] . ' - ' . Text::limit_words($l['nombre'], 6, '');
        }
        foreach ($oDestino->destinos($ref->derivado_por) as $l) {
            $destinatarios[$l->id] = $l->oficina . ' - ' . Text::limit_words($l->nombre, 6, '');
        }

        $acciones = array();
        foreach (ORM::factory('acciones')->find_all() as $a) {
            $acciones[$a->id] = $a->accion;
        }

        $this->template->title .= ' / Agregar destinatario a ' . $ref->nur;
        $this->template->titulo .= ' Agregar destinatario (en copia) a ' . $ref->nur;
        $this->template->descripcion .= ' Para cuando el usuario olvido incluir a alguien en una derivacion ya enviada';
        $this->template->scripts = array('static/js/libs/select2/select2.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');
        $this->template->content = View::factory('admin/hojasruta/agregar')
            ->bind('documento', $documento)
            ->bind('ref', $ref)
            ->bind('destinatarios', $destinatarios)
            ->bind('acciones', $acciones)
            ->bind('errors', $errors);
    }

    // eliminacion DEFINITIVA (DROP) de la hoja de ruta y todo su historial relacionado
    public function action_eliminar($id = '')
    {
        if (!isset($_POST['confirmar'])) {
            $this->request->redirect('/admin/hojasruta/lista');
        }

        $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
        if ($documento->loaded()) {
            $nur = $documento->nur;
            $codigo = $documento->codigo;

            $this->save($this->user->id_entidad, $this->user->id, 'Administrador elimino DEFINITIVAMENTE la hoja de ruta ' . $nur . ' (documento ' . $codigo . ')');

            if ($nur !== '' && $nur !== NULL) {
                // se borran cinco tablas: o cae todo el expediente o no cae nada
                $db = Database::instance();
                $db->begin();
                try {
                    $enur = $db->escape($nur);

                    $ids_documentos = array();
                    $docs = ORM::factory('documentos')->where('nur', '=', $nur)->find_all();
                    foreach ($docs as $d) {
                        $ids_documentos[] = (int) $d->id;
                    }
                    if (!empty($ids_documentos)) {
                        db::query(Database::DELETE, 'DELETE FROM archivos WHERE id_documento IN (' . implode(',', $ids_documentos) . ')')->execute();
                    }

                    db::query(Database::DELETE, 'DELETE FROM seguimiento WHERE nur = ' . $enur)->execute();
                    db::query(Database::DELETE, 'DELETE FROM agrupaciones WHERE padre = ' . $enur . ' OR hijo = ' . $enur)->execute();
                    db::query(Database::DELETE, 'DELETE FROM hojasruta WHERE nur = ' . $enur)->execute();
                    db::query(Database::DELETE, 'DELETE FROM documentos WHERE nur = ' . $enur)->execute();
                    db::query(Database::DELETE, 'DELETE FROM nurs WHERE nur = ' . $enur)->execute();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollback();
                    Kohana::$log->add(Log::ERROR, 'eliminar hoja de ruta: ' . $e->getMessage());
                    Session::instance()->set('hr_aviso_error', 'No se pudo eliminar la hoja de ruta ' . $nur . ': no se borró nada.');
                    $this->request->redirect('/admin/hojasruta/editar/' . (int) $id);
                }
            }
            Session::instance()->set('hr_aviso', 'Se eliminó definitivamente la hoja de ruta ' . ($nur !== '' ? $nur : $codigo) . '.');
        }

        $this->request->redirect('/admin/hojasruta/lista');
    }

}

?>
