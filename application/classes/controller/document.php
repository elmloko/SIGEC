<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_document extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        parent::before();

//
        //  $this->template->styles = array('media/css/tablas.csss' => 'all', 'media/css/datatable.css' => 'all');
        //  $this->template->scripts = array('media/js/datatable.js',
        //     'media/js/resizable.min.js',
        //    'media/js/jquery.tablesorter.min.js');

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
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'document');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    public function action_index() {
        $oTipo = New Model_Tipos();
        $mistipos = $oTipo->lista($this->user->id);
        $oDoc = New Model_documentos();

        $tipo = $oTipo->tipoUsuario($this->user->id);
        $doc = $oDoc->tipoDocumentos($this->user->id);
        // var_dump($doc);

        $tipoDoc = array();
        foreach ($tipo as $t) {
            $tipoDoc[] = $t['tipo'];
        }


        $documentos = $oDoc->agrupados($this->user->id);
        $recientes = $oDoc->recientes($this->user->id);
        foreach ($documentos as $d) {
            $tipos[$d['id_tipo']]['cantidad'] = $d['n'];
        }


        $oficina = $_GET['oficina'];
        $tipodocumento = ORM::factory('tipos')->where('plural', '=', trim($_GET['tipo']))->find();
        if ($tipodocumento->loaded()) {
            $ofi = ORM::factory('oficinas', $oficina);
            $tipo = $tipodocumento->id;
            $this->template->titulo.=$ofi->oficina;
            $this->template->descripcion = 'Procesos generados';


            $this->save($this->user->id_entidad, $this->user->id, 'Genera reporte documentos generados: ' . $tipodocumento->plural);
            $this->template->content = View::factory('reportes/jqwdocs')
                    ->bind('oficina', $oficina)
                    ->bind('tipo_doc', $tipodocumento)
                    ->bind('tipo', $tipo);
        }

        $this->template->title .= ' / Generados';
        $this->template->titulo .= 'Generar y Lista de Generados';
        $this->template->descripcion = 'Generar, cantidad de documentos generados y  ver recientes';
        $this->template->content = View::factory('documentos/index')
                ->bind('documentos', $documentos)
                // ->bind('tipos',$tipos)
                ->bind('mistipos', $mistipos)
                ->bind('tipoDoc', $tipoDoc)
                ->bind('user', $this->user)
                ->bind('recientes', $recientes);
    }

    public function action_vista() {
        $codigo = $_GET['doc'];
        $mensajes = array();
        $errors = array();
        $documento = ORM::factory('documentos')
                ->where('codigo', '=', $codigo)
//->and_where('id_user','=',$this->user->id)
                ->find();
        if ($documento->loaded()) {
            $tipo = $documento->tipos->tipo;
//archivo
// $archivo=ORM::factory('archivos')
//         ->where('id_documento','=',$id)
//        ->find();
//tipo
//$tipo=  ORM::factory('tipos',$documento->id_tipo);                    
            $this->template->title .= ' | ' . $documento->codigo;
            $this->template->content = View::factory('documentos/vista_2')
                    ->bind('d', $documento)
                    ->bind('tipo', $tipo)
//->bind('archivo', $archivo)
                    ->bind('errors', $errors)
                    ->bind('mensajes', $mensajes);
        } else {
            $this->template->content = 'El documento no existe';
        }
    }

    public function action_detalle($id = 0) {
        $mensajes = array();
        $errors = array();
        $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
        if ($documento->loaded()) {
            $ok = true;
            if ($documento->estado == 1) { //si esta derivado entonces el documento solo pueden ver aquellos quienes intevienen en el seguimiento
                $ok = false;
                $seguimiento = ORM::factory('seguimiento')
                        ->where('nur', '=', $documento->nur)
                        ->find_all();
                foreach ($seguimiento as $s) {
                    if (($s->derivado_a == $this->user->id) || ($s->derivado_por == $this->user->id) || $this->user->prioridad == 1)
                        $ok = true;
                }
                if ($this->user->super != 0)
                    $ok = true;
            }
            $this->template->title .= ' / ' . $documento->codigo;
            $this->template->titulo .=$documento->codigo;
            $this->template->descripcion .= ' Descripcion del documento - ' . $documento->codigo;
            if ($ok) {
                $tipo = $documento->tipos->tipo;
//archivo
                $archivo = ORM::factory('archivos')->where('id_documento', '=', $id)->find_all();

                $this->template->content = View::factory('documentos/detalle')
                        ->bind('d', $documento)
                        ->bind('tipo', $tipo)
                        ->bind('archivo', $archivo)
                        ->bind('errors', $errors)
                        ->bind('mensajes', $mensajes);
            } else {

                $this->template->content = View::factory('no_access');
            }
        } else {
            $this->template->content = 'El documento no existe';
        }
    }

    public function generar_codigo($tip, $abre, $id) {
//obtenemos la sigla de la oficina
        $oficina = ORM::factory('oficinas', $id);
        if ($oficina) {
            $correlativo = ORM::factory('correlativo')->where('id_oficina', '=', $id)
                    ->and_where('id_tipo', '=', $tip)
                    ->find();
            if ($correlativo->loaded()) {
                $correlativo->correlativo = $correlativo->correlativo + 1; //incrementamos en 1 el correlativo
                $correlativo->save();
                $corr = substr('000' . $correlativo->correlativo, -4);
                if ($abre != '')
                    $abre.='/';
                return $abre . $oficina->sigla . '/' . date('Y') . '-' . $corr;
//return $codigo;
            }
        }
    }

// lista de documentos segun el tipo    
    public function action_type($t = '') {
        $tipo = ORM::factory('tipos', array('id' => $t));
        $count = $tipo->documentos->where('id_user', '=', $this->user->id)->and_where('id_tipo', '=', $tipo->id)->count_all();
// Creamos una instancia de paginacion + configuracion
        $pagination = Pagination::factory(array(
                    'total_items' => $count,
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => 15,
                    'view' => 'pagination/floating',
        ));
        $results = $tipo->documentos
                ->where('id_user', '=', $this->user->id)
                ->and_where('id_tipo', '=', $tipo->id)
                ->order_by('fecha_creacion', 'DESC')
                ->limit($pagination->items_per_page)
                ->offset($pagination->offset)
                ->find_all();
// Render the pagination links
        $page_links = $pagination->render();
//tipos para los tabs       
        $this->template->title .= ' / ' . $tipo->plural;
        $this->template->titulo .= $tipo->plural;
        $this->template->descripcion = 'Lista de ' . $tipo->plural . ' generados';
        switch ($tipo->action) {
            case 'sol_pasajes':
                $this->template->content = View::factory('documentos/listar_solicitudes')
                        ->bind('results', $results)
                        ->bind('page_links', $page_links)
                        ->bind('tipo', $tipo);


                break;
            case 'inf_viaje':
                $this->template->content = View::factory('documentos/listar_viajes')
                        ->bind('results', $results)
                        ->bind('page_links', $page_links)
                        ->bind('tipo', $tipo);


                break;

            default:
                $this->template->content = View::factory('documentos/listar')
                        ->bind('results', $results)
                        ->bind('page_links', $page_links)
                        ->bind('tipo', $tipo);

                break;
        }
    }

    /*
     * function para editar un documento
     * 
     */

    public function action_add_file() {
        if ($_POST) {
            for ($i = 0; $i < count($_FILES ['archivo']) - 1; $i++) {
                echo $_FILES ['archivo']['name'][$i];
            }
            var_dump($_FILES);
            /* $filename = upload::save ( $_FILES ['archivo'],NULL,'archivo/'.date('Y_m') );												
              $archivo = ORM::factory ( 'archivos' ); //intanciamos el modelo proveedor
              $archivo->nombre_archivo = basename($filename);
              $archivo->extension = $_FILES ['archivo'] ['type'];
              $archivo->tamanio = $_FILES ['archivo'] ['size'];
              $archivo->id_user = $this->user->id;
              $archivo->id_documento = $documento->id;
              $archivo->sub_directorio = date('Y').'/';
              $archivo->fecha = date('Y-m-d H:i:s');
              $archivo->save ();
             * 
             */
        }
        $this->template->content = View::factory('documentos/add_file');
    }

    public function action_files() {
        $oArchivo = New Model_archivos();
        $archivo = $oArchivo->listar($this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->title .= ' / Archivos Digitales';
        $this->template->titulo .= 'Archivos Digitales';
        $this->template->descripcion = 'lista de archivos subidos al sistema';
        $this->template->content = View::factory('documentos/archivos')
                ->bind('results', $archivo);
    }

//asignar hoja de ruta pendiente
    public function action_asignar($id = '') {
        if ($id != '') {
//verificamos que el documento pertenece al usuario y ademas no tiene hoja de ruta
            $documento = ORM::factory('documentos')
                    ->where('id_user', '=', $this->user->id)
                    ->and_where('id', '=', $id)
                    ->find();
            if ($documento->loaded()) {
                $oSeg = New Model_Seguimiento();
                $pendientes = $oSeg->pendiente($this->user->id);
                $this->template->title .= ' / ' . $documento->codigo;
                $this->template->titulo .= ' Asignar hoja de ruta a ' . $documento->codigo;
                $this->template->styles = array('media/css/tablas.css' => 'all');
                $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
                $this->template->descripcion = 'Escoja una hora de ruta y presione en el boton asignar';
                $this->template->content = View::factory('documentos/asignar')
                        ->bind('pendientes', $pendientes)
                        ->bind('documento', $documento);
            }
        }
    }

    public function action_asignacion() {
        if (isset($_GET['id_doc']) && isset($_GET['hr'])) {
            $hr = $_GET['hr'];
            $id = $_GET['id_doc'];
//ademas el documento debe ser del propietario
            $documento = ORM::factory('documentos')
                    ->where('id_user', '=', $this->user->id)
                    ->and_where('id', '=', $id)
                    ->and_where('nur', '=', '')
                    ->find();
            if ($documento->loaded()) {
                /*  $entidad=ORM::factory('entidades',$this->user->id_entidad);                                                                        
                  $hojaruta=ORM::factory('nurs')->where('nur','=',$hr)->find();
                  if($hojaruta->loaded())
                  { */
                $oNur = New Model_nurs();
                $documento->nur = $hr;
                $documento->original = 0;
                $documento->save();
//cazamos al documento con el nur asignado
                $rs = $documento->has('nurs', $hr);
                $documento->add('nurs', $hr);
                /*   } */
                $this->request->redirect('/documento/edit/' . $documento->id);
            }
        }
    }

    public function action_newHR($id = '') {
        if ($id != '') {
            $documento = ORM::factory('documentos', $id);
            if ($documento->loaded()) {
                if ($documento->nur == '') {

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
                    $this->request->redirect('/documento/edit/' . $documento->id);
                } else {
                    $this->request->redirect('/documento/edit/' . $documento->id);
                }
            }
        }
    }

}

?>
