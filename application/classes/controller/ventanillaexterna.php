<?php

defined('SYSPATH') or die('Acceso denegado');

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

class Controller_Ventanillaexterna extends Controller
{
    public $template = 'ventanilla/recepcion_externa';

    /*
    public function action_index()
    {
        $this->response->body(View::factory('busqueda/seguimiento_externo'));
    }
    */

    public function before()
    {
        parent::before();
        //$this->template->title = 'SIGEC';
    }

    //modulo para ventanilla
    public function action_index()
    {
        $user = new Model_Users();
        $user->id = 255;
        $user->id_entidad = 13;

        // MOTIVOS
        $result = ORM::factory('motivos')->where('activo', '=', 1)->find_all();
        foreach ($result as $r) {
            $motivos[$r->id] = $r->motivo;
        }

        // DESTINATARIOS
        $oDestinatario = New Model_Destinatarios();
        $destinos = $oDestinatario->destinos($user->id);

        if ($_POST) {
            $id_user = $user->id;

            $id_proceso = '4';
            $id_usuario = $id_user;
            $id_tipo = '70';
            $nombre_destinatario = $_POST['destinatario'];
            $cargo_destinatario = $_POST['cargodes'];
            $referencia = $_POST['referencia'];
            $adjuntos = $_POST['adjunto'];
            $hojas = $_POST['hojas'];
            // '0000' cuando es insertado por el CIUDADANO
            $entidad_sisin = '0000';
            $prioridad = '1';

            // PROCEDIMIENTO ALMACENADO
            $sql = "CALL set_genera_nota_externa_digital(
                '$id_proceso', 
                '$id_usuario', 
                '$id_tipo', 
                '$nombre_destinatario',
                '$cargo_destinatario', 
                '$referencia', 
                '$adjuntos', 
                '$hojas', 
                '$entidad_sisin', 
                '$prioridad', 
                @pv_resultado, 
                @pv_mensaje, 
                @pv_mensajebd);";

            $result = DB::query(Database::SELECT, $sql)->execute();
            $result1 = DB::query(Database::SELECT, "SELECT @pv_resultado AS pv_resultado, @pv_mensaje AS pv_mensaje, @pv_mensajebd AS pv_mensajebd;")->execute();

            $this->request->redirect('ventanillaExterna/edit/' . $result1[0]['pv_resultado']);

            /*
            if ($documento->id) {
                $oNur = New Model_nurs();
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
                $descripcion->id_motivo = $_POST['motivo'];
                $descripcion->id_user = $this->user->id;
                $descripcion->fecha = date('Y-m-d H:i:s');
                $descripcion->save();

                $_POST = array();
                $_FILES = array();
                $this->request->redirect('ventanilla/edit/' . $documento->id);
            }
            */
        }

        // --- [INICIO] PARAMETRIZAR LA VISTA ---
        $view = View::factory('ventanilla/recepcion_externa');
        $view->bind('motivos', $motivos);
        $view->bind('destinos', $destinos);

        $this->response->body($view);
        // --- [FIN] PARAMETRIZAR LA VISTA ---
    }

    public function action_edit($id)
    {

        $user = new Model_Users();
        $user->id = 255;
        $user->id_entidad = 13;

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
            $documento->hojas = $_POST['hojas'];
            // $documento->id_proceso=Arr::get($_POST,'proceso',1);
            $documento->id_proceso = 4;
            $documento->save();
            /*
            if ($_FILES['archivo']['name'] != '') {
                $post = Validation::factory($_FILES)
                    ->rule('archivo', 'Upload::not_empty')
                    ->rule('archivo', 'Upload::type', array(':value', array('pdf', 'PDF')))
                    ->rule('archivo', 'Upload::size', array(':value', '20M'));
                //si pasa la validacion guardamamos
                if ($post->check()) {
                    $path = '/var/www/html/adjuntos/sigec/archivo/' . date('Y_m');
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
                    $error['Documento escaneado: '] = 'El documento debe ser pdf y de un tamaño no mayor a 20M';
                }
            }
            */

            $path = rtrim(Kohana::$config->load('archivo')->get('path'), '/\\') . '/' . date('Y_m');
            if (!is_dir($path)) {
                // Creates the directory
                if (!mkdir($path, 0777, TRUE)) {
                    // On failure, throws an error
                    throw new Exception("No se puedo crear el directorio!");
                    exit;
                }
            }
            $filename = upload::save($_FILES ['archivo'], NULL, $path);
            if ($_FILES['archivo']['name'] != '') {
                $archivo = ORM::factory('archivos'); //intanciamos el modelo proveedor
                $archivo->nombre_archivo = basename($filename);
                $archivo->extension = $_FILES ['archivo'] ['type'];
                $archivo->tamanio = $_FILES ['archivo'] ['size'];
                $archivo->id_user = $user->id;
                $archivo->id_documento = $documento->id;
                $archivo->sub_directorio = date('Y_m');
                $archivo->fecha = date('Y-m-d H:i:s');
                $archivo->save();
                if ($archivo->id > 0)
                    //if ($archivo->id >= 0)
                    $_POST = array();
            }
            $_POST = array();
            $_FILES = array();
        }
        $documento = ORM::factory('documentos')->where('id', '=', $id)->and_where('id_user', '=', $user->id)->find();
        if ($documento->loaded()) {
            // MOTIVOS
            $motivos = array();
            $result = ORM::factory('motivos')->where('activo', '=', 1)->find_all();
            foreach ($result as $r) {
                $motivos[$r->id] = $r->motivo;
            }
            // ARCHIVO
            $archivo = ORM::factory('archivos')->where('id_documento', '=', $id)->order_by('id', 'DESC')->limit(1)
                ->find();

            //destinatarios
            $oDestinatario = New Model_Destinatarios();
            $destinos = $oDestinatario->destinos($user->id);

            $this->template->title .= ' / Editar ' . substr($documento->codigo, 1);
            $this->template->titulo .= 'Editar ' . substr($documento->codigo, 1);
            $this->template->descripcion = 'Editar correspondencia entrante';
            $this->template->content = View::factory('ventanilla/editar_externo')
                ->bind('documento', $documento)
                ->bind('motivos', $motivos)
                ->bind('archivo', $archivo)
                ->bind('destinos', $destinos)
                ->bind('error', $error)
                ->bind('info', $info);

            // --- [INICIO] PARAMETRIZAR LA VISTA ---
            $view = View::factory('ventanilla/editar_externo');
            $view->bind('documento', $documento);
            $view->bind('motivos', $motivos);
            $view->bind('archivo', $archivo);
            $view->bind('destinos', $destinos);
            $view->bind('error', $error);
            $view->bind('info', $info);

            $this->response->body($view);
            // --- [FIN] PARAMETRIZAR LA VISTA ---
        } else {
            $this->template->title .= ' / Editar ' . substr($documento->codigo, 1);
            $this->template->titulo .= 'Editar ' . substr($documento->codigo, 1);
            $this->template->descripcion = 'Editar correspondencia entrante';
            $this->template->content = '<div role="alert" class="alert alert-danger">
                    No tiene permisos para editar este documento. cualquier duda comuniquese con el administrador.
                </div>';
        }
    }

    public function action_download()
    {
        $id = $_GET['file'];
        //$this->autoRender = false;
        $archivo = ORM::factory('archivos', $id);
        if ($archivo->loaded()) {
            //ahora vemos que solo el que estee autorizado pueda descargar
            //  $file='/archivos/'.$archivo->sub_directorio.'/'.$archivo->nombre_archivo;
            $base = rtrim(Kohana::$config->load('archivo')->get('path'), '/\\');
            $file = $base . '/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
            $filetemp = substr($archivo->nombre_archivo, 13);

            if (!is_file($file)) {
                $this->autoRender = false;
                http_response_code(404);
                echo 'Archivo no encontrado en el servidor.';
                return;
            }

            if (ob_get_level()) {
                ob_end_clean();
            }

            header("Content-Description: File Transfer");
            header("Content-Type: " . ($archivo->extension ?: 'application/octet-stream'));
            header("Content-Disposition: attachment; filename=\"" . $filetemp . "\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($file));
            readfile($file);
            exit;
        } else {
            echo 'Archivo Inexistente.!!';
        }
    }
}

?>
