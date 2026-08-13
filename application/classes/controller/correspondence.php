<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_Correspondence extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($this->user->nivel);
            parent::before();
            $this->template->titulo = '<v>Correspondencia / </v>';
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
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'correspondence');
        $oSM = New Model_menus();
        $submenus = $oSM->submenus('correspondence');
        $this->template->submenu = View::factory('templates/submenu')->bind('smenus', $submenus)->set('titulo', 'Correspondencia');
        parent::after();
    }

    public function action_index() {
        $oSeg = New Model_Seguimiento();
//echo $this->user->id;
        $entrada = $oSeg->estado(1, $this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->title .= ' / Recepción de correspondencia';
        $this->template->titulo .= 'Recepción';
        $this->template->descripcion = 'lista de Correspondencia disponible para recepcion';
        $this->template->content = View::factory('correspondencia/entrada')
                ->bind('norecibidos', $entrada);
    }

    public function action_inbox() {
        $oSeg = New Model_Seguimiento();
        $inbox = $oSeg->estado(1, $this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->title .= '/ Recepción de correspondencia';
        $this->template->titulo .= 'Enviada';
        $this->template->descripcion = ' Correspondencia derivada que aun no entrego';
        $this->template->content = View::factory('correspondencia/entrada')
                ->bind('norecibidos', $inbox);
    }

    public function action_doa() {
        if ($_POST) {
            if ($_POST['accion'] == 0) { // 0 = archivar correspondencia
                $carpetas = ORM::factory('carpetas')->where('id_oficina', '=', $this->user->id_oficina)->find_all();
                $arrCarpetas = array();
                foreach ($carpetas as $c) {
                    $arrCarpetas[$c->id] = $c->carpeta;
                }
                //nurs
                $nurs = array();
                $oSeg = New Model_Seguimiento();
                foreach ($_POST['id_seg'] as $k => $v) {
                    $nur = $oSeg->nur($v);
                    $id = $nur[0]['id'];
                    $nurs[$id] = $nur[0]['nur'];
                }
                $this->template->title.=' / Archivar correspondencia';
                $this->template->titulo.='Archivar';
                $this->template->descripcion = 'SELECCIONE LA CARPETA donde guardara el tramite o proceso.';
                $this->template->content = View::factory('correspondencia/archivar')
                        ->bind('options', $arrCarpetas)
                        ->bind('nurs', $nurs);
            } else {
                $oSeg = New Model_Seguimiento();
                foreach ($_POST['id_seg'] as $k => $v) {
                    $nur = $oSeg->nur($v);
                    $id = $nur[0]['id'];
                    $nurs[$id] = $nur[0]['nur'];
                }
                $this->template->title.=' / agrupar correspondencia';
                $this->template->titulo.='Agrupar correspondencia';
                $this->template->descripcion = 'Agrupar correspondencia';
                $this->template->content = View::factory('correspondencia/agrupar')
                        ->bind('nurs', $nurs);
            }
        }
    }

    //archivar correspondencia final
    public function action_agruparf() {
        if (isset($_POST['principal'])) {

            $principal = $_POST['principal'];
            $padre = ORM::factory('seguimiento', $principal);
            if ($padre->loaded()) {
                foreach ($_POST['seg'] as $k => $v) {
                    $hijo = ORM::factory('seguimiento', $v);
                    if ($padre->nur != $hijo->nur) {
                        $agrupar = ORM::factory('agrupaciones');
                        $agrupar->padre = $padre->nur;
                        $agrupar->hijo = $hijo->nur;
                        $agrupar->id_seguimiento = $hijo->id;
                        $agrupar->id_user = $this->user->id;
                        $agrupar->nombre = $this->user->nombre;
                        $agrupar->cargo = $this->user->cargo;
                        $agrupar->fecha = date('Y-m-d H:i:s');
                        $agrupar->save();
                        if ($agrupar->id > 0) { //si se agrupo! entonces cambiamos el estado del hijo
                            $hijo->estado = 6;
                            $hijo->save();
                        }
                    }
                }
                //por le decimos al seguimiento del padre que tiene hijos jiji                    
                $padre->hijo = 1;
                $padre->save();
                $_POST = array();
                $this->request->redirect('correspondence/agrupado/?hr=' . $padre->nur);
            }
        }
    }

    //archivar correspondencia final
    public function action_archivarf() {
        if ($_POST) {
            if ($_POST['tipo'] == 0) {  //nueva carpeta
                $nombre_carpeta = $_POST['carpeta_input'];

                if ($nombre_carpeta == '') {
                    $carpeta = time();
                }
                $carpeta = ORM::factory('carpetas');
                $carpeta->id_oficina = $this->user->id_oficina;
                $carpeta->carpeta = $nombre_carpeta;
                $carpeta->fecha_creacion = date('Y-m-d H:i:s');
                $carpeta->save();
                if ($carpeta->id > 0) { //si se creo la carpeta entonces:
                    $id_carpeta = $carpeta->id;
                    foreach ($_POST['seg'] as $k => $v) {
                        $seg = ORM::factory('seguimiento', $v);
                        if ($seg->loaded()) {
                            $archivo = ORM::factory('archivados');
                            $archivo->id_user = $this->user->id;
                            $archivo->nur = $seg->nur;
                            $archivo->id_carpeta = $id_carpeta;
                            $archivo->observaciones = $_POST['observaciones'];
                            $archivo->fecha = date('Y-m-d H:i:s');
                            $archivo->save();
                            $seg->estado = 10;
                            $seg->id_archivo = $archivo->id;
                            $seg->save();
                        }
                    }
                    $_POST = array();
                }
            } else {
                foreach ($_POST['seg'] as $k => $v) {
                    $seg = ORM::factory('seguimiento', $v);
                    if ($seg->loaded()) {
                        $carpeta = ORM::factory('archivados');
                        $carpeta->id_user = $this->user->id;
                        $carpeta->nur = $seg->nur;
                        $carpeta->id_carpeta = $_POST['carpeta_lista'];
                        $carpeta->observaciones = $_POST['observaciones'];
                        $carpeta->fecha = date('Y-m-d H:i:s');
                        $carpeta->save();
                        $seg->estado = 10;
                        $seg->id_archivo = $carpeta->id;
                        $seg->save();
                    }
                }
                $_POST = array();
            }
            $this->request->redirect('correspondence/archived');
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

    public function action_received() {
        if (isset($_GET['id'])) {
            $user = ORM::factory('users')
                    ->where('id', '=', $_GET['id'])
                    ->and_where('superior', '=', $this->user->id)
                    ->find();
            if ($user->id) {
                $oSeg = New Model_Seguimiento();
                $entrada = $oSeg->pendiente($user->id);
                $carpetas = ORM::factory('carpetas')->where('id_oficina', '=', $user->id_oficina)->find_all();
                $arrCarpetas = array();
                foreach ($carpetas as $c) {
                    $arrCarpetas[$c->id] = $c->carpeta;
                }
                $oDoc = New Model_Tipos();
                $documentos = $oDoc->misTipos($this->user->id);
                $options = array();
                foreach ($documentos as $d) {
                    $options[$d->id] = $d->tipo;
                }
                $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
                $this->template->title .= ' / Corresponsdencia Pendientes';
                $this->template->titulo .= 'Pendientess ';
                $this->template->descripcion = 'Correspondencia recibida para pronta respuesta';
                $this->template->content = View::factory('correspondencia/lista_pendientes')
                        ->bind('entrada', $entrada)
                        ->bind('carpetas', $arrCarpetas)
                        ->bind('user', $user)
                        ->bind('options', $options);
            } else {
                $this->template->content = 'No esta autorizado';
            }
        } else {
            $oSeg = New Model_Seguimiento();
            $entrada = $oSeg->pendiente($this->user->id);
            $carpetas = ORM::factory('carpetas')->where('id_oficina', '=', $this->user->id_oficina)->find_all();
            $arrCarpetas = array();
            foreach ($carpetas as $c) {
                $arrCarpetas[$c->id] = $c->carpeta;
            }
            $oDoc = New Model_Tipos();
            $documentos = $oDoc->misTipos($this->user->id);
            $options = array();
            foreach ($documentos as $d) {
                $options[$d->id] = $d->tipo;
            }
            $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
            $this->template->title .= ' / Correspondencia Pendiente';
            $this->template->titulo .= 'Pendientes ';
            $this->template->descripcion = 'Correspondencia recibida para pronta respuesta';
            $this->template->content = View::factory('correspondencia/pendientes')
                    ->bind('entrada', $entrada)
                    ->bind('carpetas', $arrCarpetas)
                    ->bind('user', $this->user)
                    ->bind('options', $options);
        }
    }

    /* mis archivos */

    public function action_archived() {
        $oCarpeta = New Model_Carpetas();
        $carpetas = $oCarpeta->archivadores($this->user->id);
        $this->template->title.=' / Correspondencia Archivada';
        $this->template->titulo .= 'Archivada ';
        $this->template->descripcion = 'Lista de Carpetas';
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->content = View::factory('correspondencia/archivadores')
                ->bind('carpetas', $carpetas);
    }

    public function action_folder($id = '') {
        $oArchivo = New Model_Archivados();
        $carpeta = $oArchivo->carpeta($id, $this->user->id);
        if (sizeof($carpeta) > 0) {
            $user = $this->user;
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->title.=' / Carpeta - ' . $carpeta[0]['carpeta'];
            $this->template->titulo.=' Carpeta - ' . $carpeta[0]['carpeta'];
            $this->template->descripcion = 'Correspondencia archivada en esta carpeta';
            $this->template->content = View::factory('correspondencia/carpeta')
                    ->bind('carpeta', $carpeta)
                    ->bind('user', $user);
        }
    }

    public function action_recepcion() {
        $oSeg = New Model_Seguimiento();
        $entrada = $oSeg->estado(1, $this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->title .= '<li><span>Entrada</span></li>';
        $this->template->content = View::factory('user/recepcionar')
                ->bind('norecibidos', $entrada);
    }

    public function action_receive($id = '') {
        if ($id != '') {
            $seguimiento = ORM::factory('seguimiento')->where('id', '=', $id)->and_where('derivado_a', '=', $this->user->id)->and_where('estado', '=', 1)->find();
            if ($seguimiento->loaded()) {
                $seguimiento->fecha_recepcion = date('Y-m-d H:i:s');
                $seguimiento->estado = 2; //2=pendiente oficial
                $seguimiento->save();
                //guardamos en vitacora
                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' | <b>' . $this->user->cargo . '</b> Recepciono la hoja de ruta ' . $seguimiento->nur);

                $this->request->redirect('./correspondence');
            } else {
                $this->template->content = 'No se pudo recepcionar correspondencia.';
            }
        } else {
            $this->template->content = 'No se pudo recepcionar correspondencia o ya fue recepcionada.';
        }
    }

    //detalle agrupado
    public function action_agrupado() {
        $nur = Arr::get($_GET, 'hr', '');
        $hijos = array();
        $padre = ORM::factory('agrupaciones')->where('padre', '=', $nur)->find_all();
        foreach ($padre as $p) {
            //obtenemos los hijos
            $hijo = ORM::factory('documentos')->where('nur', '=', $p->hijo)->and_where('original', '=', 1)->find();
            if ($hijo->loaded()) {
                $hijos[$hijo->nur] = array(
                    'id_nur' => $hijo->nur,
                    'nur' => $hijo->nur,
                    'documento' => $hijo->codigo,
                    'referencia' => $hijo->referencia,
                    'destinatario' => $hijo->nombre_destinatario,
                    'cargo' => $hijo->cargo_destinatario,
                );
            }
        }
        if (sizeof($hijos) > 0) {
            $padre = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->title.=' / Correspondencia Agrupada';
            $this->template->titulo.=' Agrupada';
            $this->template->descripcion = 'Hoja de ruta Agrupado';
            $this->template->content = View::factory('correspondencia/agrupado')
                    ->bind('hijos', $hijos)
                    ->bind('padre', $padre);
        } else {
            $this->template->title.=' / Agrupada';
            $this->template->titulo.=' Agrupada';
            $this->template->descripcion = 'Hoja de ruta Agrupado';
            $this->template->content = '<div class="error"><b>Error:</b> NO agrupado !!! </div>';
        }
    }

    public function action_unarchive($id = '') {
        $seguimiento = ORM::factory('seguimiento')->where('id', '=', $id)->and_where('derivado_a', '=', $this->user->id)->find();
        if ($seguimiento->id) {
            //debemos eliminar de archivos
            $archivo = ORM::factory('archivados', array('id' => $seguimiento->id_archivo));
            $archivo->delete();
            //cambiamos el estado a pendiente
            $seguimiento->estado = 2;
            $seguimiento->id_archivo = 0;
            $seguimiento->save();
            $this->request->redirect('correspondence/received');
        } else {
            $this->template->content = View::factory('acceso_denegado');
        }
    }

    //correspondencia de salida
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

    //imprimir enviado
    public function action_printDeriv($id = '') {
        $seg = ORM::factory('seguimiento', $id);
        if ($seg->loaded()) {
            if (($seg->derivado_por == $this->user->id) && ($seg->estado == '1')) {
                $oSeg = New Model_Seguimiento();
                $derivado = $oSeg->derivado($id);
                $this->template->content = View::factory('correspondencia/print_deriv')
                        ->bind('derivado', $derivado);
            } else {
                echo 'no se puede';
            }
        }
    }

    //imprimir enviado
    public function action_cancel($id = '') {
        $info = array();
        $seg = ORM::factory('seguimiento', array('id' => $id));
        $nur = $seg->nur;
        if ($seg->loaded()) {
            if (($seg->derivado_por == $this->user->id) && ($seg->estado == '1')) {
                $padre = $seg->id_seguimiento; //si tiene seguimiento anterior?
                $oficial = $seg->oficial;
                $seg->delete();
                //si tiene seguimiento
                if ($padre > 0) {
                    $oSeg = New Model_Seguimiento();
                    $oSeg->delete_deriv($padre);
                    $seguimiento = ORM::factory('seguimiento', array('id' => $padre));
                    if ($seguimiento->oficial == 2)
                        $seguimiento->oficial = 1;
                    $seguimiento->estado = 2; //pendiente                   
                    $seguimiento->save();
                    $info['info'] = '<b>Restaurado! : </b>La hoja de ruta <b>' . $seguimiento->nur . ' </b> fue restauradoa a <a href="/correspondence/received"> Correspondencia recibida</a>, si quiere volver a derivar click <a href="/route/deriv/?hr=' . $seguimiento->nur . '" > aqui </a>.';
                }
                //primera derivacion
                else {
                    if ($oficial == 1) {
                        $documento = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();
                        $documento->estado = 0;
                        $documento->save();
                        $info['info'] = '<b>Restaurado! : </b>La hoja de ruta <b>' . $documento->nur . ' </b> fue restaurada, para volver derivar busque el documento <a href="/document/edit/' . $documento->id . '">' . $documento->codigo . '</a> , o haga click <a href="/route/deriv/?hr=' . $documento->nur . '" > aqui </a>.';
                    }
                }
            } else {
                $error['error'] = '<b>Error!: </b>La hoja de ruta ya fue recibida por el destinatario o usted no lo tenia en su bandeja de salida';
            }
        }
        $oSeg = New Model_Seguimiento();
        $entrada = $oSeg->enviados($this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->title .= ' / enviada';
        $this->template->content = View::factory('correspondencia/enviados')
                ->bind('entrada', $entrada)
                ->bind('info', $info)
                ->bind('error', $error);
    }

    public function action_grouped() {
        $count = ORM::factory('agrupaciones')->where('id_user', '=', $this->user->id)->count_all();
        $pagination = Pagination::factory(array(
                    'total_items' => $count,
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => 50,
                    'view' => 'pagination/floating',
        ));
        $page_links = $pagination->render();
        $oDocumentos = New Model_Documentos();
        $agrupados = $oDocumentos->agrupaciones($this->user->id, $pagination->offset, $pagination->items_per_page);
        $this->template->title.='| Agrupados';
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('correspondencia/agrupados')
                ->bind('result', $agrupados)
                ->bind('count', $count)
                ->bind('page_links', $page_links);
    }

}

?>
