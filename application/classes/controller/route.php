<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_route extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'route');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    //listar nuris generados por el usuario logeado
    public function action_index() {
        $count = ORM::factory('documentos')->where('original', '=', 1)->and_where('id_user', '=', $this->user->id)->count_all();
        $pagination = Pagination::factory(array(
                    'total_items' => $count,
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => 50,
                    'view' => 'pagination/floating',
        ));
        $oNur = New Model_Hojasruta();
        $result = $oNur->hojasruta($this->user->id, $pagination->offset, $pagination->items_per_page);
        $page_links = $pagination->render();
        $oDoc = New Model_Tipos();
        $documentos = $oDoc->misTipos($this->user->id);
        $options = array();
        foreach ($documentos as $d) {
            $options[$d->id] = $d->tipo;
        }
        $this->template->title .= ' / Lista de Hojas de Ruta generadas';
        $this->template->titulo.='Lista';
        $this->template->descripcion = ' lista de hojas de ruta generados';
        $this->template->styles = array('media/css/tablas.csss' => 'all', 'media/css/datatable.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->scripts = array('media/js/datatable.js',
            'media/js/resizable.min.js',
            'media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('hojaruta/index')
                ->bind('result', $result)
                ->bind('page_links', $page_links)
                ->bind('count', $count)
                ->bind('options', $options);
    }

    public function action_seguimiento() {
        $id = Arr::get($_GET, 'hr', '');
        if ($id != '') {
            //obtenemos el documento ligado al nur                                     
            $documento = ORM::factory('documentos')->where('nur', '=', $id)->and_where('original', '=', 1)->find();
            $tipo = ORM::factory('tipos', $documento->id_tipo);
            $proceso = ORM::factory('procesos', $documento->id_proceso);
            $detalle = array(
                'nur' => $id,
                'fecha' => $documento->fecha_creacion,
                'codigo' => $documento->cite_original,
                'id_documento' => $documento->id,
                'tipo' => $tipo->tipo,
                'proceso' => $proceso->proceso,
                'referencia' => $documento->referencia,
                'remitente' => $documento->nombre_remitente,
                'cargo_remitente' => $documento->cargo_remitente,
                'destinatario' => $documento->nombre_destinatario,
                'cargo_destinatario' => $documento->cargo_destinatario,
                'adjunto' => $documento->adjuntos,
            );
            $archivo = ORM::factory('archivos')
                    ->where('id_documento', '=', $documento->id)
                    ->find_all();
            $oSeg = New Model_Seguimiento();
            $seguimiento = $oSeg->seguimiento($id);

            $user = $this->user;
            //agrupaciones
            $agrupado = ORM::factory('agrupaciones')->where('hijo', '=', $id)->find();
            $this->template->title = 'Seguimiento a la hoja de ruta : ' . $detalle['nur'];
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->content = View::factory('hojaruta/seguimiento')
                    ->bind('seguimiento', $seguimiento)
                    ->bind('detalle', $detalle)
                    ->bind('archivo', $archivo)
                    ->bind('user', $user)
                    ->bind('agrupado', $agrupado);

            //var_dump($seguimiento);
        } else {
            $this->request->redirect('/route/ver');
        }
    }

    /* ver seguimiento */

    public function action_trace() {
        $id = Arr::get($_GET, 'hr', '');
        if ($id != '') {
            //obtenemos el documento ligado al nur                                     
            $documento = ORM::factory('documentos')->where('nur', '=', $id)->and_where('original', '=', 1)->find();
            $tipo = ORM::factory('tipos', $documento->id_tipo);
            $proceso = ORM::factory('procesos', $documento->id_proceso);


	    // DETALLE DEL TIEMPO DEL TRAMITE
            // $query = "call detalleTiempoDelTramite('$id')";

            // ?hr= viene de la URL: se enlaza como parametro (antes se concatenaba: inyeccion SQL)
            $query = "  SELECT
                            s.prioridad tipo_tramite,
                            a.fecha fecha_plazo_urgente,
                            RESTA2_FECHAS(s.fecha_emision, CURDATE()) tiempo_transcurrido
                        FROM
                            alertas a,
                            (SELECT
                                s.*
                            FROM
                                seguimiento s
                            WHERE
                                nur = :nur) AS s
                        WHERE
                            (a.id_seguimiento = s.id)
                        LIMIT 1";

            $resultSetTiempoDelTramite = db::query(Database::SELECT, $query)
                ->param(':nur', (string) $id)
                ->execute()
                ->as_array();
            if (!$resultSetTiempoDelTramite) {
                $resultSetTiempoDelTramite = array(array('tipo_tramite' => 0, 'fecha_plazo_urgente' => NULL, 'tiempo_transcurrido' => NULL));
            }

            $prioridad_tramite = $resultSetTiempoDelTramite[0]['tipo_tramite'];
            $tipo_tramite = "NO URGENTE";

            if (intval($prioridad_tramite) == 1) {
                $tipo_tramite = "URGENTE!!!";
            }

            //      Array Detalle Tiempo del Tramite
            $detalleTiempoDelTramite = array(
                // 'query' => $query,
                'resultSetTiempoDelTramite' => $resultSetTiempoDelTramite,
                'tipo_tramite' => $tipo_tramite,
                'fecha_plazo_urgente' => $resultSetTiempoDelTramite[0]['fecha_plazo_urgente'],
                'tiempo_transcurrido' => $resultSetTiempoDelTramite[0]['tiempo_transcurrido'],
            );


            // ARRAY <DETALLE>
            $detalle = array(
                'nur' => $id,
                'fecha' => $documento->fecha_creacion,
                'codigo' => $documento->cite_original,
                'id_documento' => $documento->id,
                'id_user' => $documento->id_user,
                'tipo' => $tipo->tipo,
                'proceso' => $proceso->proceso,
                'referencia' => $documento->referencia,
                'remitente' => $documento->nombre_remitente,
                'cargo_remitente' => $documento->cargo_remitente,
                'destinatario' => $documento->nombre_destinatario,
                'cargo_destinatario' => $documento->cargo_destinatario,
                'adjunto' => $documento->adjuntos,
            );
            $archivo = ORM::factory('archivos')
                    ->where('id_documento', '=', $documento->id)->and_where('estado', '=', '1')
                    ->find_all();
            //$seguimiento=ORM::factory('seguimiento')->where('nur','=',$id)->find_all();
            $oSeg = New Model_Seguimiento();
            $seguimiento = $oSeg->seguimiento($id);
            //$f = $oSeg->archivado($id);
            $oficina = $this->user->id_oficina;
            $user = $this->user;
//agrupaciones
            $agrupado = ORM::factory('agrupaciones')->where('hijo', '=', $id)->find();

            // === Documentos subidos en cada derivacion (para mostrarlos en el seguimiento) ===
            // Todos los documentos de este expediente (el original + los generados en cada respuesta/derivacion)
            $documentos_nur = ORM::factory('documentos')->where('nur', '=', $id)->find_all();
            $ids_documentos_nur = array();
            foreach ($documentos_nur as $dn) {
                $ids_documentos_nur[] = $dn->id;
            }

            // Todos los archivos subidos en cualquiera de esos documentos, del mas antiguo al mas nuevo
            $archivos_nur = array();
            if (!empty($ids_documentos_nur)) {
                $archivos_nur = ORM::factory('archivos')
                        ->where('id_documento', 'IN', $ids_documentos_nur)
                        ->and_where('estado', '=', '1')
                        ->order_by('fecha', 'ASC')
                        ->find_all()
                        ->as_array();
            }

            // Cada archivo se sube segundos antes de crearse la derivacion que lo envia,
            // por el mismo usuario que deriva: lo asignamos a ese paso del seguimiento.
            $archivos_por_seguimiento = array();
            $usados = array();
            foreach ($seguimiento as $s) {
                $archivos_por_seguimiento[$s->id] = array();
                $fecha_paso = strtotime($s->fecha_emision . ' ' . $s->hora_emision);
                $mejor_idx = null;
                foreach ($archivos_nur as $idx => $a) {
                    if (in_array($idx, $usados, TRUE))
                        continue;
                    if ((int) $a->id_user !== (int) $s->derivado_por)
                        continue;
                    $fecha_archivo = strtotime($a->fecha);
                    if ($fecha_archivo > $fecha_paso)
                        continue;
                    if ($mejor_idx === null || $fecha_archivo > strtotime($archivos_nur[$mejor_idx]->fecha)) {
                        $mejor_idx = $idx;
                    }
                }
                if ($mejor_idx !== null) {
                    $archivos_por_seguimiento[$s->id][] = $archivos_nur[$mejor_idx];
                    $usados[] = $mejor_idx;
                }
            }
            //$documento=$documento[0];      
            $this->template->title.=' / Seguimiento ' . $detalle['nur'];
            $this->template->titulo.='Seguimiento a ' . $detalle['nur'];
            $this->template->descripcion = 'Seguimiento del tramite o proceso';
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->content = View::factory('hojaruta/seguimiento')
                    ->bind('seguimiento', $seguimiento)
                    ->bind('detalle', $detalle)
		    ->bind('detalleTiempoDelTramite', $detalleTiempoDelTramite)
                    ->bind('archivo', $archivo)
                    ->bind('archivos_por_seguimiento', $archivos_por_seguimiento)
                    // ->bind('f', $f)
                    ->bind('oficina', $oficina)
                    ->bind('user', $user)
                    ->bind('agrupado', $agrupado)
                    ->set('derivacion_editable', $this->derivacion_editable($id) !== NULL);
        } else {
            $this->request->redirect('route/view');
        }
    }

    public function action_responder() {
        if ($_GET['id_seg']) {
            $id_seg = Arr::get($_GET, 'id_seg');
            $nur = Arr::get($_GET, 'n');
            $id_tipo = Arr::get($_GET, 'd');
            $seguimiento = ORM::factory('seguimiento', $id_seg);
            if ($seguimiento->loaded()) {
                $tipo = ORM::factory('tipos', $id_tipo);
                $oOficina = New Model_Oficinas();
                $correlativo = $oOficina->correlativo($this->user->id_oficina, $tipo->id, date('Y'));
                $abre = $oOficina->tipo($tipo->id);
                $sigla = $oOficina->sigla($this->user->id_oficina);


                $oficina = ORM::factory('oficinas', $this->user->id_oficina);
                $abre = $oOficina->tipo($tipo->id);
                $entidad = ORM::factory('entidades')->where('id', '=', $oficina->id_entidad)->find();
                //variables para el cite
                $ofi = $oficina->sigla;
                $cor = $correlativo;
                $ent = $entidad->sigla;
                $mosca = $this->user->mosca;
                $anio = date('Y');
                $aniom = date('Y');
                $tip = $tipo->abreviatura;
                if ($tipo->cite_propio > 0) {
                    eval("\$str = \"$tipo->cite\";");
                    $codigo = $str;
                } else {
                    $cite_propio = '';
                    eval("\$str = \"$tipo->cite_tipo\";");
                    $codigo = $str;
                }


                $proceso = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();

                $documento = ORM::factory('documentos');
                $documento->id_user = $this->user->id;
                $documento->codigo = $codigo;
                $documento->cite_original = $codigo;
                $documento->id_tipo = $id_tipo;
                $documento->nombre_destinatario = $seguimiento->nombre_emisor;
                $documento->cargo_destinatario = $seguimiento->cargo_emisor;
                $documento->nombre_remitente = $this->user->nombre;
                $documento->cargo_remitente = $this->user->cargo;
                $documento->fecha_creacion = date('Y-m-d H:i:s');
                $documento->nur = $nur;
                $documento->id_seguimiento = $id_seg;
                $documento->original = 0; //important !!                
                //$documento->id_proceso = $proceso->id;
                $documento->id_proceso = $proceso->id_proceso;
                $documento->id_oficina = $this->user->id_oficina;
                $documento->id_entidad = $this->user->id_entidad;
                $documento->save();
                if ($documento->id) {
                    $rs = $documento->has('nurs', $nur);
                    $documento->add('nurs', $nur);
                    $_POST = array();
                    $this->request->redirect('documento/edit/' . $documento->id);
                }
            } else {
                $this->templates->content = '<div class="info">Error: no se pudo generar el documento</div>';
            }
        } else {
            $this->template->content = View::factory('');
        }
    }

    public function action_generar_doc() {
        if ($_POST['aceptar']) {
            $nur = Arr::get($_POST, 'nur');
            $id_tipo = Arr::get($_POST, 'documento');
            $tipo = ORM::factory('tipos', $id_tipo);
            $oOficina = New Model_Oficinas();
            $correlativo = $oOficina->correlativo($this->user->id_oficina, $tipo->id, date('Y'));
            $abre = $oOficina->tipo($tipo->id);
            $sigla = $oOficina->sigla($this->user->id_oficina);
            if ($abre != '')
                $abre = $abre . '/';
            $codigo = $abre . $sigla . ' Nº ' . $correlativo . '/' . date('Y');
            //obtenemos el id_proceso del documento original
            $proceso = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();
            //generamos el documento
            $documento = ORM::factory('documentos');
            $documento->id_user = $this->user->id;
            $documento->codigo = $codigo;
            $documento->cite_original = $codigo;
            $documento->id_tipo = $id_tipo;
            $documento->fecha_creacion = date('Y-m-d H:i:s');
            $documento->nur = $nur;
            $documento->id_seguimiento = 0;
            $documento->original = 0; //important !!                
            $documento->id_proceso = $proceso->id;
            $documento->id_oficina = $this->user->id_oficina;
            $documento->id_entidad = $this->user->id_entidad;
            $documento->save();
            if ($documento->id) {
                //cazamos al documento con el nur asignado
                $rs = $documento->has('nurs', $nur);
                $documento->add('nurs', $nur);
                $_POST = array();
                $this->request->redirect('document/edit/' . $documento->id);
            }
        } else {
            $this->template->content = View::factory('');
        }
    }

    //asignar nur o nuri
    public function action_asignar($id = '') {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            //propiedades del documento
            $documento = ORM::factory('documentos')
                    ->where('id', '=', $id)
                    ->and_where('id_user', '=', $this->user->id)
                    ->find();
            if ($documento) {
                if ($_POST) {
                    //generamos el codigo del correlativo
                    $codigo = $this->nuevo(-1);
                    //asignamos
                    $nuri = ORM::factory('asignados');
                    $nuri->codigo = $codigo;
                    $nuri->id_user = $this->user->id;
                    $nuri->fecha_creacion = date('Y-m-d H:i:s');
                    $nuri->tipo_id = -1;
                    $nuri->save();
                    //actualizamos propiedades del docuemtno

                    $documento->id_nuri = $nuri->id;
                    $documento->nuri = $codigo;
                    $documento->id_proceso = $_POST['proceso'];
                    $documento->save();
                    //enviamos al formulario de derivacion
                    $this->request->redirect('route/deriv/?hr=' . $documento->id_nuri);
                }
                $procesos = ORM::factory('procesos')->find_all();
                $arrP = array('' => '');
                foreach ($procesos as $p) {
                    $arrP[$p->id] = $p->proceso;
                }
                $this->template->content = View::factory('nur/create')
                        ->bind('procesos', $arrP)
                        ->bind('documento', $documento);
            } else {
                $mensaje = 'Usted no puede modificar/asignar documentos de otras personas';
                $this->template->content = View::factory('errors/general')
                        ->bind('mensaje', $mensaje);
            }
        } else {
            $this->request->redirect(URL::base() . 'login');
        }
    }

    //correlativo para un NURI -1=nuri / -2 = nur
    public function nuevo($type = -1) {
        $oCorrelativo = ORM::factory('correlativo')
                ->where('id_tipo', '=', $type)
                ->find();
        $oCorrelativo->correlativo = $oCorrelativo->correlativo + 1;
        $oCorrelativo->save();
        $codigo = '000' . $oCorrelativo->correlativo;
        if ($type == -1)
            $tipo = 'I/';
        else
            $tipo = '';
        $codigo = $tipo . date('Y') . '-' . substr($codigo, -4);
        return $codigo;
    }

    //nuris creados por el usuario
    public function action_listar() {
        $oNuri = New Model_Asignados();
        $count = $oNuri->count($auth->get_user());
        //$count2= $oNuri->count2($auth->get_user());           
        if ($count) {
            // echo $oNuri->count2($auth->get_user());
            $pagination = Pagination::factory(array(
                        'total_items' => $count,
                        'current_page' => array('source' => 'query_string', 'key' => 'page'),
                        'items_per_page' => 40,
                        'view' => 'pagination/floating',
            ));
            $result = $oNuri->nuris($auth->get_user(), $pagination->offset, $pagination->items_per_page);
            $page_links = $pagination->render();
            $this->template->title = 'Hojas de Seguimiento';
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->content = View::factory('nur/listar')
                    ->bind('result', $result)
                    ->bind('page_links', $page_links);
        } else {
            $this->template->content = View::factory('errors/general');
        }
    }

    /**
     * Derivacion del usuario en esa hoja de ruta que todavia no fue recibida (estado 1 = "No recibido").
     * Devuelve la fila (con id_seguimiento = paso desde el que derivo) o NULL.
     * Si derivo desde el documento (paso 0), solo cuenta si es el autor.
     */
    protected function derivacion_editable($nur) {
        $fila = DB::query(Database::SELECT, 'SELECT s.id, s.id_seguimiento FROM seguimiento s
                LEFT JOIN seguimiento p ON p.id = s.id_seguimiento
                LEFT JOIN documentos d ON d.nur = s.nur AND d.original = 1
                WHERE s.nur = :nur AND s.derivado_por = :user AND s.estado = 1
                  AND ((s.id_seguimiento = 0 AND d.id_user = :user) OR (s.id_seguimiento > 0 AND p.derivado_a = :user))
                ORDER BY s.id DESC LIMIT 1')
                ->param(':nur', (string) $nur)->param(':user', (int) $this->user->id)
                ->execute()->current();
        return $fila ? $fila : NULL;
    }

    /**
     * "Enterado": cierra una copia recibida sin derivarla (la archiva en la carpeta de copias de la oficina).
     * POST id_seg, observaciones. Responde JSON.
     */
    public function action_enterado() {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $seg = ORM::factory('seguimiento', (int) Arr::get($_POST, 'id_seg', 0));
        if (!$seg->loaded() || (int) $seg->derivado_a !== (int) $this->user->id) {
            $this->response->body(json_encode(array('error' => 'Esta hoja de ruta no está en su bandeja.')));
            return;
        }
        if ((int) $seg->oficial !== 0) {
            $this->response->body(json_encode(array('error' => 'Solo las copias se pueden cerrar con "Enterado". La oficial debe derivarse o archivarse.')));
            return;
        }
        if ((int) $seg->estado !== 2) {
            $this->response->body(json_encode(array('error' => 'Esta copia ya no está pendiente.')));
            return;
        }
        // carpeta de copias de la oficina (se reutiliza si ya existe una "Copias...")
        $carpeta = ORM::factory('carpetas')
                ->where('id_oficina', '=', $this->user->id_oficina)
                ->and_where('carpeta', 'LIKE', 'copias%')
                ->order_by('id')
                ->find();
        if (!$carpeta->loaded()) {
            $carpeta = ORM::factory('carpetas');
            $carpeta->id_oficina = $this->user->id_oficina;
            $carpeta->carpeta = 'COPIAS PARA CONOCIMIENTO';
            $carpeta->fecha_creacion = date('Y-m-d H:i:s');
            $carpeta->save();
        }
        $obs = trim((string) Arr::get($_POST, 'observaciones', ''));
        $archivo = ORM::factory('archivados');
        $archivo->id_user = $this->user->id;
        $archivo->nur = $seg->nur;
        $archivo->id_carpeta = $carpeta->id;
        $archivo->observaciones = mb_substr('Enterado (copia)' . ($obs !== '' ? ': ' . $obs : ''), 0, 1000, 'UTF-8');
        $archivo->fecha = date('Y-m-d H:i:s');
        $archivo->save();
        $seg->estado = 10;
        $seg->id_archivo = $archivo->id;
        $seg->save();
        $this->response->body(json_encode(array('ok' => 1, 'carpeta' => $carpeta->carpeta)));
    }

    public function action_deriv() {
        $nur = Arr::get($_GET, 'hr', 0);
        $documento = ORM::factory('documentos')
                ->where('nur', '=', $nur)
                ->and_where('original', '=', 1)
                ->find();
        if ($documento->loaded()) {
            $session = Session::instance();
            $session->delete('destino');

            $proceso = ORM::factory('procesos', $documento->id_proceso);
            $errors = array();
            $user = $this->user;
            if ($documento->estado == 0 && (int) $documento->id_user !== (int) $this->user->id) {
                // la primera derivacion solo la puede hacer quien genero el documento
                $this->template->title .= ' / Derivar ' . $documento->nur;
                $this->template->content = View::factory('no_access')
                        ->set('codigo', $documento->cite_original)
                        ->set('motivo', 'no_autor');
            } elseif ($documento->estado == 0) {
                $acciones = $this->acciones();
                $destinatarios = $this->destinatarios($this->user->id, $this->user->superior);
                $id_seguimiento = 0;
                $oficial = 1;
                $hijo = 0;
                $this->template->title.=' / Derivar Correspondencia - ' . $documento->nur;
                $this->template->titulo.='Derivar ' . $documento->nur;
                $this->template->descripcion = ' formulario de derviacion';

                // $this->template->styles = array('media/css/tablas.css' => 'screen', 'media/css/fcbk.css' => 'screen', 'media/css/modal.css' => 'screen',);
                //$this->template->scripts = array('media/js/jquery.fcbkcomplete.min.js',);
                //file:///home/ivan/Descargas/materialadmin/themeforest-10646222-material-admin-bootstrap-admin-html5-app/materialadmin/
                $this->template->scripts = array('static/js/libs/select2/select2.min.js', 'static/js/libs/bootstrap-datepicker/bootstrap-datepicker.js');
                $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all', 'static/css/theme-1/libs/bootstrap-datepicker/datepicker3.css' => 'screen');

                $this->template->content = View::factory('hojaruta/frm_derivacion')
                        ->bind('documento', $documento)
                        ->bind('acciones', $acciones)
                        ->bind('destinatarios', $destinatarios)
                        ->bind('id_seguimiento', $id_seguimiento)
                        ->bind('oficial', $oficial)
                        ->bind('hijo', $hijo)
                        ->bind('proceso', $proceso)
                        ->bind('errors', $errors)
                        ->bind('user', $user);
            } else {
                //verificamos que la hora de ruta esta en sus pendientes
                $pendiente = ORM::factory('seguimiento')
                        ->where('nur', '=', $nur)
                        ->and_where('derivado_a', '=', $this->user->id)
                        ->and_where('estado', '=', 2)
                        ->find();
                if ($pendiente->loaded()) {

                    $acciones = $this->acciones();
                    $destinatarios = $this->destinatarios($this->user->id, $this->user->superior);
                    $id_seguimiento = $pendiente->id;
                    $oficial = $pendiente->oficial;
                    $hijo = $pendiente->hijo;

                    $this->template->title.=' / Formulario de Derivación';
                    $this->template->titulo.='Derivar ' . $documento->nur;
                    $this->template->descripcion = ' formulario de derviacion';
                    $this->template->scripts = array('static/js/libs/select2/select2.min.js', 'static/js/libs/bootstrap-datepicker/bootstrap-datepicker.js');
                    $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all', 'static/css/theme-1/libs/bootstrap-datepicker/datepicker3.css' => 'screen');
                    $this->template->content = View::factory('hojaruta/frm_derivacion')
                            ->bind('documento', $documento)
                            ->bind('acciones', $acciones)
                            ->bind('destinatarios', $destinatarios)
                            ->bind('id_seguimiento', $id_seguimiento)
                            ->bind('oficial', $oficial)
                            ->bind('hijo', $hijo)
                            ->bind('proceso', $proceso)
                            ->bind('user', $user)
                            ->bind('errors', $errors);
                } elseif ($editable = $this->derivacion_editable($nur)) {
                    // corregir una derivacion propia que todavia nadie recibio (agregar o quitar destinatarios)
                    $paso = (int) $editable['id_seguimiento'];
                    $oficial = 1;
                    $hijo = 0;
                    if ($paso > 0) {
                        $anterior = ORM::factory('seguimiento', $paso);
                        $oficial = $anterior->oficial;
                        $hijo = $anterior->hijo;
                    }
                    $acciones = $this->acciones();
                    $destinatarios = $this->destinatarios($this->user->id, $this->user->superior);
                    $id_seguimiento = $paso;
                    $editando = TRUE;
                    $this->template->title .= ' / Editar derivación ' . $documento->nur;
                    $this->template->titulo .= 'Editar derivación ' . $documento->nur;
                    $this->template->scripts = array('static/js/libs/select2/select2.min.js', 'static/js/libs/bootstrap-datepicker/bootstrap-datepicker.js');
                    $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all', 'static/css/theme-1/libs/bootstrap-datepicker/datepicker3.css' => 'screen');
                    $this->template->content = View::factory('hojaruta/frm_derivacion')
                            ->bind('documento', $documento)
                            ->bind('acciones', $acciones)
                            ->bind('destinatarios', $destinatarios)
                            ->bind('id_seguimiento', $id_seguimiento)
                            ->bind('oficial', $oficial)
                            ->bind('hijo', $hijo)
                            ->bind('proceso', $proceso)
                            ->bind('user', $user)
                            ->bind('errors', $errors)
                            ->bind('editando', $editando);
                } else {
                    $this->request->redirect('route/trace/?hr=' . urlencode($nur));
                }
            }
        } else {
            $this->template->content = 'Hoja de Ruta Inexistente';
        }
    }

    //imprimir
    public function action_print() {

        $this->template->scripts = array('static/js/select2.full.js','static/js/eModal.min.js');
        $this->template->styles = array('static/css/select2.min.css' => 'screen');

        $mHojaruta = new Model_Hojasruta();
        $recientes = $mHojaruta->recientes_usuario($this->user->id, 8);
        // ?hr=... permite llegar con una hoja de ruta ya elegida
        $hr_inicial = mb_substr(trim(Arr::get($_GET, 'hr', '')), 0, 30);

        $this->template->titulo .= 'Imprimir hoja de ruta';
        $this->template->content = View::factory('hojaruta/imprimir')
                ->set('recientes', $recientes)
                ->set('hr_inicial', $hr_inicial);
    }

    // hojas de ruta derivadas por el usuario, con busqueda, filtros por estado/fecha y paginacion
    public function action_view() {
        $es_fecha = function ($v) {
            return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) !== FALSE;
        };
        $estado = Arr::get($_GET, 'estado', '');
        $filtros = array(
            'q' => mb_substr(trim(Arr::get($_GET, 'q', '')), 0, 100),
            'estado' => array_key_exists((int) $estado, Model_Derivaciones::$estados) && $estado !== '' ? (string) (int) $estado : '',
            'desde' => $es_fecha(Arr::get($_GET, 'desde', '')) ? Arr::get($_GET, 'desde') : '',
            'hasta' => $es_fecha(Arr::get($_GET, 'hasta', '')) ? Arr::get($_GET, 'hasta') : '',
        );
        $por_pagina = 25;
        $pagina = max(1, (int) Arr::get($_GET, 'pagina', 1));

        $mDerivaciones = new Model_Derivaciones();
        $resultado = $mDerivaciones->buscar($this->user->id, $filtros, $pagina, $por_pagina);
        $por_estado = $mDerivaciones->por_estado($this->user->id, $filtros);
        $total_paginas = max(1, (int) ceil($resultado['total'] / $por_pagina));

        $this->template->styles = array('static/css/bandeja.css?v=' . @filemtime(DOCROOT . 'static/css/bandeja.css') => 'all');
        $this->template->titulo .= 'Seguimiento';
        $this->template->descripcion = 'Hojas de ruta que derivó';
        $this->template->content = View::factory('hojaruta/ver')
                ->set('filas', $resultado['filas'])
                ->set('total', $resultado['total'])
                ->set('por_estado', $por_estado)
                ->set('filtros', $filtros)
                ->set('pagina', min($pagina, $total_paginas))
                ->set('total_paginas', $total_paginas)
                ->set('por_pagina', $por_pagina)
                ->set('estados', Model_Derivaciones::$estados);
    }

    /*     * */

    public function acciones() {
        $acciones = array();
        $acc = ORM::factory('acciones')->find_all();
        foreach ($acc as $a) {
            $acciones [$a->id] = $a->accion;
        }
        return $acciones;
    }

    public function destinatarios($id_user, $id_superior) {

        $lista_derivacion = array();
        $oDestino = New Model_Destinatarios();
        //dependientes
        $lista_destinos = $oDestino->dependientes($id_user);
        foreach ($lista_destinos as $l) {
            $lista_derivacion [$l['id']] = $l['oficina'] . ' - ' . Text::limit_words($l['nombre'], 6, '');
        }
        //superior
        $lista_destinos = $oDestino->superior($id_superior);
        foreach ($lista_destinos as $l) {
            $lista_derivacion [$l['id']] = $l['oficina'] . ' - ' . Text::limit_words($l['nombre'], 6, '');
        }

        $lista_destinos = $oDestino->destinos($id_user);
        foreach ($lista_destinos as $l) {
            if (!array_key_exists($l->id, $lista_derivacion))
                // $lista_derivacion [$l->id] = $l->oficina . ' - ' . Text::limit_words($l->nombre, 6, '');
		$lista_derivacion [$l->id] = $l->cargo . ' - ' . Text::limit_words($l->nombre, 6, '');
        }

        //print_r($lista_destinos);
        //sort($lista_derivacion, true);
        //sort($lista_derivacion);
        asort($lista_derivacion);
        return $lista_derivacion;
    }

    public function action_oficina($id = 0) {

        $oficina = ORM::factory('oficinas', $id);
        if ($oficina->loaded()) {
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)->find_all();
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
            $this->template->title.=' / ' . $oficina->oficina;
            $this->template->titulo = '<v>' . HTML::chars($oficina->oficina) . '</v>';
            $this->template->descripcion = 'Lista de Personal';
            $this->template->content = View::factory('user/personal')
                    ->bind('usuarios', $usuarios);
        } else {
            
        }
    }

}

?>
