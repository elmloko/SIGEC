<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_Documento extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'document');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    /**
     * Cite nuevo para un documento del tipo $tipo en la oficina $oficina_id: toma el siguiente correlativo
     * (oficina, tipo, gestion) y arma el codigo con la plantilla del tipo (tipos.cite o tipos.cite_tipo).
     */
    private function nuevo_cite($tipo, $oficina_id, $anio) {
        $oOficina = New Model_Oficinas();
        //obtenemos informe juridico
        if ($this->es_juridico()) {
            $correlativo = $oOficina->correlativoJuridico(156, $tipo->id, $anio);
        } else {
            $correlativo = $oOficina->correlativo($oficina_id, $tipo->id, $anio);
        }
        return $this->armar_cite($tipo, $oficina_id, $anio, $correlativo);
    }

    // los documentos de juridica (oficina 156 y sus dependientes) llevan un solo correlativo, el de la 156
    private function es_juridico() {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);
        return $oficina->padre == 156 || $oficina->id == 156;
    }

    /** Arma el codigo del cite con la plantilla del tipo para el correlativo $cor (ej. '0073'). */
    private function armar_cite($tipo, $oficina_id, $anio, $cor) {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);
        $oOficina = New Model_Oficinas();
        $abre = $oOficina->tipo($tipo->id);
        $entidad = ORM::factory('entidades')->where('id', '=', $oficina->id_entidad)->find();
        //variables que usan las plantillas del cite
        $ofi = $oOficina->sigla($oficina_id);
        $ent = $entidad->sigla;
        $mosca = $this->user->mosca;
        $aniom = $anio;
        $tip = $tipo->abreviatura;
        $cite_propio = '';
        $plantilla = $tipo->cite_propio > 0 ? $tipo->cite : $tipo->cite_tipo;
        eval("\$str = \"$plantilla\";");
        return $str;
    }

    /**
     * Si el cite del documento es el ultimo emitido de su tipo (nadie genero otro despues),
     * devuelve ese numero al correlativo para que se vuelva a usar y no quede un salto.
     */
    private function liberar_cite($documento, $anio) {
        $tipo = ORM::factory('tipos', $documento->id_tipo);
        $correlativo = ORM::factory('correlativo')
                ->where('id_oficina', '=', $this->es_juridico() ? 156 : $documento->id_oficina)
                ->and_where('id_tipo', '=', $tipo->id)
                ->and_where('gestion', '=', $anio)
                ->find();
        if (!$correlativo->loaded() || $correlativo->correlativo < 1) {
            return;
        }
        $ultimo = $this->armar_cite($tipo, $documento->id_oficina, $anio, substr('000' . $correlativo->correlativo, -4));
        $anterior = $this->armar_cite($tipo, $documento->id_oficina, $anio, substr('000' . ($correlativo->correlativo - 1), -4));
        // (si la plantilla no usa el numero, ambos coinciden y no se toca nada)
        if ($ultimo === $documento->codigo && $anterior !== $ultimo) {
            $correlativo->correlativo = $correlativo->correlativo - 1;
            $correlativo->save();
        }
    }

    /** Tipos a los que se puede cambiar un documento: los que el usuario puede generar, sin los que llevan datos propios. */
    private function tipos_para_cambio($documento) {
        return DB::query(Database::SELECT, "SELECT t.id, t.tipo FROM usertipo u INNER JOIN tipos t ON t.id = u.id_tipo
                WHERE u.id_user = :u AND t.activo = 1 AND t.id <> 6 AND t.id <> :actual
                AND t.action NOT IN ('sol_pasajes', 'inf_viaje') ORDER BY t.tipo")
                ->param(':u', (int) $this->user->id)
                ->param(':actual', (int) $documento->id_tipo)
                ->execute()->as_array('id', 'tipo');
    }

    /**
     * Solo se cambia el tipo mientras el documento no tenga hoja de ruta, y si su tipo actual
     * no lleva datos propios (pasajes/viajes) que se perderian al cambiarlo.
     */
    private function tipo_cambiable($documento) {
        if ($documento->nur != '') {
            return FALSE;
        }
        $actual = ORM::factory('tipos', $documento->id_tipo);
        return !in_array($actual->action, array('sol_pasajes', 'inf_viaje'));
    }

    // cambia el tipo de documento (ej. nota interna -> informe) y le asigna el cite del nuevo tipo
    public function action_cambiartipo($id = '') {
        $documento = ORM::factory('documentos')->where('id', '=', $id)->and_where('id_user', '=', $this->user->id)->find();
        if (!$documento->loaded() || $this->request->method() !== Request::POST) {
            $this->request->redirect('/document');
        }
        $id_tipo = (int) Arr::get($_POST, 'id_tipo', 0);
        $permitidos = $this->tipos_para_cambio($documento);
        if ($documento->nur != '') {
            $error = 'hoja_ruta';
        } elseif (!$this->tipo_cambiable($documento) || !isset($permitidos[$id_tipo])) {
            $error = 'tipo';
        } else {
            $tipo = ORM::factory('tipos', $id_tipo);
            $anterior = $documento->codigo;
            $tipo_anterior = ORM::factory('tipos', $documento->id_tipo)->tipo;
            $anio = $documento->fecha_creacion ? date('Y', strtotime($documento->fecha_creacion)) : date('Y');
            $this->liberar_cite($documento, $anio);
            $codigo = $this->nuevo_cite($tipo, $documento->id_oficina, $anio);
            $documento->id_tipo = $tipo->id;
            $documento->codigo = $codigo;
            $documento->cite_original = $codigo;
            $documento->save();
            $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ', cambió el documento <b>' . $anterior . '</b> (' . $tipo_anterior . ') a ' . $tipo->tipo . ': <b>' . $codigo . '</b>');
            $error = '';
        }
        $this->request->redirect('/documento/edit/' . $documento->id . '?' . ($error ? 'error_tipo=' . $error : 'tipo_cambiado=1'));
    }

    //nuevo
    public function action_transferir($id) {

        $documento = ORM::factory('documentos')
                ->where('id_user', '=', $this->user->id)
                ->and_where('id', '=', $id)
                ->find();
        if ($documento->loaded()) {
            //datos enviados por formulario
            if (isset($_POST['submit'])) {
                $user = ORM::factory('users', $_POST['destino']);
                $d_trans = ORM::factory('documentos', $documento->id);
                $d_trans->id_user = $user->id;
                $d_trans->save();
                $this->template->titulo.=$documento->cite_original;
                $this->template->descripcion.='Transferencia de Documento';
                $this->template->content = 'Documento Transferito exitosamente.';
            } else {
                $destino = array();
                $oVias = new Model_data();
                $superior = $oVias->superior($this->user->id);
                foreach ($superior as $k) {
                    $id = $k['id'];
                    $destino[$id] = $k['nombre'] . ' / ' . $k['cargo'];
                }
                $dependientes = $oVias->dependientes($this->user->id);
                foreach ($dependientes as $k) {
                    $id = $k['id'];
                    $destino[$id] = $k['nombre'] . ' / ' . $k['cargo'];
                }
                $oDestinatario = New Model_Destinatarios();
                $destinos = $oDestinatario->destinos($this->user->id);
                foreach ($destinos as $k) {
                    $destino[$k->id] = $k->nombre . ' / ' . $k->cargo;
                }
                asort($destino);
                $this->template->titulo.=$documento->cite_original;
                $this->template->descripcion.='Transferencia de Documento';
                $this->template->content = View::factory('documentos/transferir')
                        ->bind('destino', $destino)
                        ->bind('documento', $documento);
            }
        }
    }

    public function action_generar($t = '') {
        //$this->template->menubar = '';

        $tipo = ORM::factory('tipos', array('action' => $t)); //obtenemos el id del tipo            
        if ($tipo->loaded()) {
            if (isset($_POST['submit'])) {
                $oficina = ORM::factory('oficinas', $this->user->id_oficina);
                if (isset($_POST['cite_despacho'])) {
                    if ($oficina->padre > 0)
                        $oficina_id = $oficina->padre;
                    else
                        $oficina_id = $oficina->id;
                }
                else {
                    $oficina_id = $oficina->id;
                }
                $codigo = $this->nuevo_cite($tipo, $oficina_id, date('Y'));
                if ($_POST['proceso'] > 0 && $_POST['proceso'] < 30) {
                    $proceso = $_POST['proceso'];
                } else
                    $proceso = 4;
                $documento = ORM::factory('documentos'); //intanciamos el modelo documentos                        
                $documento->id_user = $this->user->id;
                $documento->id_tipo = $tipo->id;
                $documento->id_proceso = $proceso;
                $documento->id_oficina = $oficina_id;

                $documento->codigo = $codigo;
                $documento->cite_original = $codigo;
                $documento->nombre_destinatario = $_POST['destinatario']; //
                $documento->cargo_destinatario = $_POST['cargo_des'];
                $documento->institucion_destinatario = $_POST['institucion_des'];
                $documento->nombre_remitente = $_POST['remitente'];
                $documento->cargo_remitente = $_POST['cargo_rem'];
                $documento->mosca_remitente = $_POST['mosca'];
                $documento->referencia = $_POST['referencia'];
                $documento->contenido = $_POST['descripcion'];
                $documento->fecha_creacion = date('Y-m-d H:i:s');
                $documento->adjuntos = $_POST['adjuntos'];
                $documento->hojas = (int) Arr::get($_POST, 'hojas', 0);
                $documento->copias = $_POST['copias'];
                $documento->nombre_via = $_POST['via'];
                $documento->cargo_via = $_POST['cargovia'];
                $documento->titulo = $_POST['titulo'];
                $documento->id_entidad = $this->user->id_entidad;
                $documento->nur = ''; // evita error "Field 'nur' doesn't have a default value"; se sobreescribe abajo si se asigna hoja de ruta
                $documento->save();
                //si se creo el documento entonces
                //guardamos en vitacora
                $accion = $this->user->nombre.' generó el documento : <b>' . $documento->cite_original . '</b>';

                if ($documento->id) {
                    if ($_POST['hojaruta']) { //asignamos hoja de ruta
                        //generamos la hoja de ruta a partir de la entidad
                        $entidad = ORM::factory('entidades', $this->user->id_entidad);
                        $oNur = New Model_nurs();
                        $nur = $oNur->correlativo(-1, $entidad->sigla2 . '/', $this->user->id_entidad, date('Y'));
                        $nur_asignado = $oNur->asignarNur($nur, $this->user->id, $this->user->nombre);
                        $documento->nur = $nur;
                        $documento->save();
                        //cazamos al documento con el nur asignado
                        $rs = $documento->has('nurs', $nur_asignado);
                        $documento->add('nurs', $nur_asignado);
                        $accion.= ' con Hoja de Ruta : <b>' . $documento->nur . '</b>';
                    }
                    $this->save($this->user->id_entidad, $this->user->id, $accion);



                    //si se creo una solicitud de pasajes y viaticos entonces almacenamos los datos referentes a elo mas
                    switch ($t) {
                        //solicitud de pasajes y viaticos
                        case 'sol_pasajes':
                            $pasajes = ORM::factory('pasajes');
                            $pasajes->id_documento = $documento->id;

                            if (isset($_POST['pasaje']))
                                $pasajes->pasaje = $_POST['pasaje'];

                            if (isset($_POST['viatico']))
                                $pasajes->viatico = $_POST['viatico'];

                            $pasajes->lugar = $_POST['lugar'];
                            $pasajes->tipo_viaje = $_POST['tipo_viaje'];
                            $pasajes->medio_transporte = $_POST['medio_transporte'];
                            $pasajes->fecha_salida = $_POST['fecha_salida'] . " " . $_POST['hora_salida'] . ":00";
                            $pasajes->fecha_retorno = $_POST['fecha_retorno'] . " " . $_POST['hora_retorno'] . ":00";
                            $pasajes->dias = $_POST['dias'];
                            $pasajes->save();
                            break;
                        //informe de descargo de viaje
                        case 'inf_viaje':
                            //var_dump($_POST);
                            $viajes = ORM::factory('viajes');
                            $viajes->id_documento = $documento->id;
                            $viajes->lugar = $_POST['lugar'];
                            $viajes->tipo_viaje = $_POST['tipo_viaje'];
                            $viajes->medio_transporte1 = $_POST['transporte_ida'];
                            $viajes->medio_transporte2 = $_POST['transporte_retorno'];
                            $viajes->fecha_salida = $_POST['fecha_salida'] . " " . $_POST['hora_salida'] . ":00";
                            $viajes->fecha_retorno = $_POST['fecha_retorno'] . " " . $_POST['hora_retorno'] . ":00";
                            //$viajes->dias=$_POST['dias'];
                            $viajes->no_descripcion = $_POST['no_descripcion'];
                            $viajes->viatico = $_POST['viatico'];
                            $viajes->fecha_presentacion = date('Y-m-d');
                            $viajes->resolucion = $_POST['resolucion'];
                            $viajes->pases = $_POST['pases'];
                            $viajes->form110 = $_POST['form110'];
                            $viajes->cuenta_corriente = $_POST['cuenta_corriente'];
                            $viajes->cuenta_utc = $_POST['cuenta_utc'];
                            $viajes->fondos_copia = $_POST['fondos_copia'];
                            $viajes->pasaje_aereo = $_POST['pasaje_aereo'];
                            $viajes->pasaje_terrestre = $_POST['pasaje_terrestre'];
                            $viajes->form604 = $_POST['form604'];
                            $viajes->planilla_invitados = $_POST['planilla_invitados'];
                            $viajes->cedula_identidad = $_POST['cedula_identidad'];
                            $viajes->save();
                            break;

                        default:
                            $_POST = array();
                            $this->request->redirect('documento/edit/' . $documento->id);
                            break;
                    }
                }
            }
            $oVias = new Model_data();
            $vias = $oVias->vias($this->user->id);
            $superior = $oVias->superior($this->user->id);
            $dependientes = $oVias->dependientes($this->user->id);
            $oDestinatario = New Model_Destinatarios();
            $destinos = $oDestinatario->destinos($this->user->id);

            $destinatarios = array();
            foreach ($vias as $v) {
                $destinatarios[$v['id_d']]['nombre'] = $v['nombre'];
                $destinatarios[$v['id_d']]['cargo'] = $v['cargo'];
                $destinatarios[$v['id_d']]['genero'] = $v['genero'];
            }
            foreach ($superior as $v) {
                $destinatarios[$v['id']]['nombre'] = $v['nombre'];
                $destinatarios[$v['id']]['cargo'] = $v['cargo'];
                $destinatarios[$v['id']]['genero'] = $v['genero'];
            }
            foreach ($dependientes as $v) {
                $destinatarios[$v['id']]['nombre'] = $v['nombre'];
                $destinatarios[$v['id']]['cargo'] = $v['cargo'];
                $destinatarios[$v['id']]['genero'] = $v['genero'];
            }
            foreach ($destinos as $v) {
                $destinatarios[$v->id]['nombre'] = $v->nombre;
                $destinatarios[$v->id]['cargo'] = $v->cargo;
                $destinatarios[$v->id]['genero'] = $v->genero;
            }
            sort($destinatarios);
            $procesos = ORM::factory('procesos')->find_all();
            $options = array('' => '[Elija proceso]');
            foreach ($procesos as $p) {
                $options[$p->id] = $p->proceso;
            }
            // $this->template->scripts    = array('ckeditor/adapters/jquery.js','ckeditor/ckeditor.js');                                  
            $this->template->scripts = array('static/js/libs/select2/select2.min.js','static/js/eModal.min.js');
            $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

            $this->template->title .= ' / Generar ' . $tipo->tipo;
            $this->template->titulo .= 'Generar ' . $tipo->tipo;
            $this->template->descripcion = 'LLENE CORRECTAMENTE LOS DATOS EN EL FORMULARIO';

            $this->template->content = View::factory('documentos/create')
                    ->bind('options', $options)
                    ->bind('user', $this->user)
                    ->bind('documento', $tipo)
                    ->bind('superior', $superior)
                    ->bind('dependientes', $dependientes)
                    ->bind('destinos', $destinos)
                    ->bind('destinatarios', $destinatarios)
                    ->bind('tipo', $tipo)
                    ->bind('vias', $vias);


            /*                 if($t=='circular')
              {
              $oficina=ORM::factory('oficinas')->where('id','=',$this->user->id_oficina)->find();
              $entidad=ORM::factory('entidades')->where('id','=',$oficina->id_entidad)->find();
              $oficinas=ORM::factory('oficinas')->where('id_entidad','=',$entidad->id)->find_all();
              $this->template->content    =View::factory('documentos/crear_circular')
              ->bind('options', $options)
              ->bind('user', $this->user)
              ->bind('documento', $tipo)
              ->bind('superior', $superior)
              ->bind('dependientes', $dependientes)
              ->bind('oficinas', $oficinas)
              ->bind('tipo', $tipo)
              ->bind('vias', $vias);
              }
              else
              {
             */
        }
        //            }
        else {
            $oDoc = New Model_Tipos();
            $documentos = $oDoc->misTipos($this->user->id);
            $this->template->title.= ' / Generar documentos';
            $this->template->content = View::factory('documentos/nuevo')
                    ->bind('documentos', $documentos);
        }
    }

    public function action_edit($id = '') {
        $mensajes = array();
        $documento = ORM::factory('documentos')->where('id', '=', $id)->and_where('id_user', '=', $this->user->id)->find();
        if ($documento->loaded()) {
            // una vez recibido por el destinatario, el documento y sus archivos ya no se modifican
            $envio = EstadoDocumento::de($documento);
            // resultado de /documento/cambiartipo
            if (Arr::get($_GET, 'tipo_cambiado')) {
                $mensajes['Tipo cambiado!'] = 'Se cambió el tipo de documento y se asignó el cite ' . HTML::chars($documento->codigo) . '.';
            } elseif (Arr::get($_GET, 'error_tipo') == 'hoja_ruta') {
                $error_tipo = 'Este documento ya tiene hoja de ruta asignada: no se puede cambiar su tipo.';
            } elseif (Arr::get($_GET, 'error_tipo')) {
                $error_tipo = 'No se puede cambiar este documento al tipo seleccionado.';
            }
            $tipos_cambio = $this->tipo_cambiable($documento) ? $this->tipos_para_cambio($documento) : array();
            if ($envio['recibido'] && (isset($_POST['referencia']) || isset($_POST['adjuntar']))) {
                $error_archivo = 'Este documento ya fue recibido por el destinatario: no se puede modificar.';
                $_POST = array();
            }
            //si se envia los datos modificados entonces guardamamos
            if (isset($_POST['referencia'])) {
                $documento->nombre_destinatario = $_POST['destinatario'];
                ; //
                $documento->cargo_destinatario = $_POST['cargo_des'];
                $documento->institucion_destinatario = $_POST['institucion_des'];
                $documento->nombre_remitente = $_POST['remitente'];
                $documento->cargo_remitente = $_POST['cargo_rem'];
                $documento->mosca_remitente = $_POST['mosca'];
                $documento->referencia = $_POST['referencia'];
                $documento->contenido = $_POST['descripcion'];
                $documento->titulo = $_POST['titulo'];
//                   $documento->fecha_creacion=  time(); //fecha y hora en formato int
                $documento->adjuntos = $_POST['adjuntos'];
                $documento->copias = $_POST['copias'];
                $documento->hojas = (int) Arr::get($_POST, 'hojas', 0);
                $documento->nombre_via = $_POST['via'];
                $documento->cargo_via = $_POST['cargovia'];
                $documento->id_proceso = $_POST['proceso'];
                $documento->save();
                $mensajes['Modificado!'] = 'El documento se modifico correctamente.';
                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre.', modificó el documento: <b>' . $documento->cite_original . '</b>');
            }
            if (isset($_POST['adjuntar'])) {

                $sub_directorio = date('Y_m');
                $subido = TRUE;
                if (RemoteArchivo::is_enabled()) {
                    $filename = ($_FILES['archivo']['name'] != '') ? uniqid() . $_FILES['archivo']['name'] : '';
                    if ($filename != '') {
                        try {
                            $subido = (bool) RemoteArchivo::upload($_FILES['archivo']['tmp_name'], $sub_directorio . '/' . $filename);
                        } catch (Exception $e) {
                            $subido = FALSE;
                        }
                    }
                } else {
                    $path = rtrim(Kohana::$config->load('archivo')->get('path'), '/\\') . '/' . $sub_directorio;
                    if (!is_dir($path)) {
                        // Creates the directory
                        if (!mkdir($path, 0777, TRUE)) {
                            // On failure, throws an error
                            throw new Exception("No se puedo crear el directorio!");
                            exit;
                        }
                    }
                    $filename = upload::save($_FILES ['archivo'], NULL, $path);
                    $subido = (bool) $filename;
                }
                if (!$subido) {
                    // no se registra en la base un archivo que no llego al servidor
                    $error_archivo = 'No se pudo subir el archivo: no se pudo guardar en el servidor de archivos. Intente nuevamente en unos minutos.';
                } elseif ($_FILES ['archivo']['name'] != '') {
                    $archivo = ORM::factory('archivos'); //intanciamos el modelo proveedor
                    $archivo->nombre_archivo = basename($filename);
                    $archivo->extension = $_FILES ['archivo'] ['type'];
                    $archivo->tamanio = $_FILES ['archivo'] ['size'];
                    $archivo->id_user = $this->user->id;
                    $archivo->id_documento = $_POST['id_doc'];
                    $archivo->sub_directorio = $sub_directorio;
                    $archivo->fecha = date('Y-m-d H:i:s');
                    $archivo->save();
                    if ($archivo->id > 0)
                    //if ($archivo->id >= 0)
                        $_POST = array();
                }

                // === [INICIO] Verificar si el archivo tiene o no firma digital
                //$contents = file_get_contents('https://sigec.oopp.gob.bo/media/logo.png');
                //$file_to_base64 = base64_encode($contents);

                /*
                $path_file_upload = $path . '/' . basename($filename);
                $handle = fopen($path_file_upload, "r");
                $contents = fread($handle, filesize($path_file_upload));
                fclose($handle);

                //var_dump($contents);

                // Web Service Digital Sign
                $soap_client = new SoapClient(
                    'http://siemi.oopp.gob.bo/capanegocio/services/digitalsign.asmx?WSDL',
                    array('cache_wsdl' => WSDL_CACHE_NONE)
                );

                $check_sign_byte_file_response = $soap_client->SignByteFile(
                    array(
                        'file' => $contents
                    )
                );

                $response = json_encode($check_sign_byte_file_response);
                //echo $response;
                */

                // === [FIN] Verificar si el archivo tiene o no firma digital                
            }
            $oficina = ORM::factory('oficinas', $this->user->id_oficina);

            $oVias = new Model_data();
            $vias = $oVias->vias($this->user->id);
            $superior = $oVias->superior($this->user->id);
            $dependientes = $oVias->dependientes($this->user->id);
            $oDestinatario = New Model_Destinatarios();
            $destinos = $oDestinatario->destinos($this->user->id);

            $destinatarios = array();
            foreach ($vias as $v) {
                $destinatarios[$v['id_d']]['nombre'] = $v['nombre'];
                $destinatarios[$v['id_d']]['cargo'] = $v['cargo'];
                $destinatarios[$v['id_d']]['genero'] = $v['genero'];
            }
            foreach ($superior as $v) {
                $destinatarios[$v['id']]['nombre'] = $v['nombre'];
                $destinatarios[$v['id']]['cargo'] = $v['cargo'];
                $destinatarios[$v['id']]['genero'] = $v['genero'];
            }
            foreach ($dependientes as $v) {
                $destinatarios[$v['id']]['nombre'] = $v['nombre'];
                $destinatarios[$v['id']]['cargo'] = $v['cargo'];
                $destinatarios[$v['id']]['genero'] = $v['genero'];
            }
            foreach ($destinos as $v) {
                $destinatarios[$v->id]['nombre'] = $v->nombre;
                $destinatarios[$v->id]['cargo'] = $v->cargo;
                $destinatarios[$v->id]['genero'] = $v->genero;
            }
            sort($destinatarios);
            $tipo = ORM::factory('tipos', $documento->id_tipo);
            $archivos = ORM::factory('archivos')->where('id_documento', '=', $id)->and_where('estado', '=', 1)->find_all();
            $procesos = ORM::factory('procesos')->find_all();
            $options = array();
            foreach ($procesos as $p) {
                $options[$p->id] = $p->proceso;
            }
            $this->template->title .= ' / ' . $documento->codigo;
            $this->template->titulo .= ' Editar ' . $documento->codigo . ' | <v>' . $documento->nur . '</v>';
            $this->template->descripcion = 'Editar documento, subir archivos digitales y derivacion directa';
            //$this->template->scripts = array('media/redactor/redactor.min.js', 'media/redactor/langs/es.js');
            //$this->template->styles = array('media/redactor/css/redactor.css' => 'all', 'media/css/tablas.css' => 'screen');
            $this->template->scripts = array('static/js/libs/select2/select2.min.js','static/js/libs/spin.js/spin.min.j','static/js/eModal.min.js');
            $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

            $this->template->content = View::factory('documentos/edit')
                    ->bind('documento', $documento)
                    ->bind('archivos', $archivos)
                    ->bind('tipo', $tipo)
                    ->bind('superior', $superior)
                    ->bind('destinatarios', $destinatarios)
                    ->bind('user', $this->user)
                    ->bind('options', $options)
                    ->bind('mensajes', $mensajes)
                    ->bind('error_archivo', $error_archivo)
                    ->bind('envio', $envio)
                    ->bind('tipos_cambio', $tipos_cambio)
                    ->bind('error_tipo', $error_tipo)
                    ->bind('archivos', $archivos);
        } else {
         //   $this->template->title .= ' / ' . $documento->codigo;
         //   $this->template->titulo .= ' Editar ' . $documento->codigo . ' | <v>' . $documento->nur . '</v>';
      //      $this->template->descripcion = 'Editar documento, subir archivos digitales y derivacion directa';
            $this->template->content = '<div class="error">Solo puede editar documentos creados por su usuario</div> ';
        }
    }

//function para obtoner las horas
    public function horas() {
        $m_horas = ORM::factory('horas')->find_all();
        $horas = array();
        foreach ($m_horas as $h) {
            $horas[$h->hora] = $h->hora;
        }
        return $horas;
    }

    //
    public function transportes() {
        $medios_transporte = ORM::factory('mediotransporte')->find_all();
        $m_transporte = array();

        foreach ($medios_transporte as $m) {
            $m_transporte[$m->id] = $m->medio_transporte;
        }
        return $m_transporte;
    }

}

?>