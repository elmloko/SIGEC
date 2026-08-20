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
        $q = trim(Arr::get($_GET, 'q', ''));
        $page = (int) Arr::get($_GET, 'page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $items_per_page = 50;

        $oHR = New Model_Hojasruta();
        $count = $oHR->contarTodas($q);

        $pagination = Pagination::factory(array(
            'total_items' => $count,
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'items_per_page' => $items_per_page,
            'view' => 'pagination/floating',
        ));

        $hojasruta = $oHR->todasAdmin($pagination->offset, $items_per_page, $q);
        $page_links = $pagination->render();

        $this->template->title .= ' Hojas de Ruta';
        $this->template->titulo .= ' Hojas de Ruta';
        $this->template->descripcion .= ' Listado general de hojas de ruta generadas en el sistema';
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('admin/hojasruta/lista')
            ->bind('hojasruta', $hojasruta)
            ->bind('count', $count)
            ->bind('page_links', $page_links)
            ->bind('q', $q);
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

        if (isset($_POST['editar'])) {
            $nur_original = $documento->nur;
            $nuevo_nur = trim($_POST['nur']);
            $nuevo_codigo = trim($_POST['codigo']);

            // validamos que el codigo no se duplique con otro documento
            if ($nuevo_codigo !== $documento->codigo) {
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
                    $enur_old = Database::instance()->escape($nur_original);
                    $enur_new = Database::instance()->escape($nuevo_nur);

                    db::query(Database::UPDATE, 'UPDATE seguimiento SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                    db::query(Database::UPDATE, 'UPDATE agrupaciones SET padre = ' . $enur_new . ' WHERE padre = ' . $enur_old)->execute();
                    db::query(Database::UPDATE, 'UPDATE agrupaciones SET hijo = ' . $enur_new . ' WHERE hijo = ' . $enur_old)->execute();
                    db::query(Database::UPDATE, 'UPDATE hojasruta SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                    db::query(Database::UPDATE, 'UPDATE documentos SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();
                    db::query(Database::UPDATE, 'UPDATE nurs SET nur = ' . $enur_new . ' WHERE nur = ' . $enur_old)->execute();

                    $this->save($this->user->id_entidad, $this->user->id, 'Administrador cambio el NUR ' . $nur_original . ' a ' . $nuevo_nur . ' (actualizacion en cascada)');

                    // recargamos el documento porque su nur ya cambio directamente en la BD
                    $documento = ORM::factory('documentos')->where('id', '=', $id)->find();
                }

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

        $this->template->title .= ' / Editar ' . $documento->nur;
        $this->template->titulo .= ' Editar ' . $documento->nur;
        $this->template->descripcion .= ' Editar los datos del documento de la hoja de ruta';
        $this->template->content = View::factory('admin/hojasruta/editar')
            ->bind('documento', $documento)
            ->bind('error', $error)
            ->bind('info', $info);
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
                $enur = Database::instance()->escape($nur);

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
            }
        }

        $this->request->redirect('/admin/hojasruta/lista');
    }

}

?>
