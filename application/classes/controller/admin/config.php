<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Config extends Controller_AdminTemplate
{
    protected $user;
    protected $menus;

    public function before()
    {
       parent::before();
    }

    public function after()
    {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'user');
        $oSM = New Model_menus();

        parent::after();
    }

    public function action_index()
    {
        $this->template->content = View::factory('admin/config/config');
    }

    public function action_tipos()
    {
        $this->template->scripts = array(
            //'media/js/select-chain.js',
            //'static/js/libs/select2/select2.min.js',
            'static/js/libs/DataTables/extensions/ColVis/js/dataTables.colVis.min.js',
            'static/js/libs/DataTables/jquery.dataTables.min.js',
            //'media/js/jquery.tablesorter.min.js'
        );
        $this->template->styles = array(
            // 'static/css/theme-1/libs/select2/select2.css' => 'all',
            'static/css/theme-1/libs/DataTables/extensions/dataTables.colVis.css' => 'all',
            'static/css/theme-1/libs/DataTables/jquery.dataTables.css' => 'all'
        );
        $tipos = ORM::factory('tipos')->where('activo', '=', 1)->find_all();

        $this->template->content = View::factory('admin/config/tipo_documentos')
            ->bind('tipos', $tipos);
    }

    //nuevo tipo y edicion de tipos
    public function action_tipo($id = '')
    {
        $error = '';
        // el id sale de la URL, no del formulario
        $t = ($id !== '') ? ORM::factory('tipos', (int) $id) : ORM::factory('tipos');
        $nuevo = !$t->loaded();
        $campos = array('tipo', 'plural', 'abreviatura', 'action', 'descripcion', 'cite_tipo', 'cite', 'template', 'template_via');
        $datos = array();
        foreach ($campos as $c) {
            $datos[$c] = $nuevo ? '' : (string) $t->$c;
        }
        $datos['via'] = $nuevo ? 0 : (int) $t->via;
        $datos['cite_propio'] = $nuevo ? 0 : (int) $t->cite_propio;
        $datos['activo'] = $nuevo ? 1 : (int) $t->activo;

        if (isset($_POST['submit'])) {
            foreach ($campos as $c) {
                $datos[$c] = trim(Arr::get($_POST, $c, ''));
            }
            $datos['abreviatura'] = strtoupper($datos['abreviatura']);
            $datos['via'] = isset($_POST['via']) ? 1 : 0;
            $datos['cite_propio'] = isset($_POST['cite_propio']) ? 1 : 0;
            $datos['activo'] = isset($_POST['activo']) ? 1 : 0;

            if ($datos['tipo'] === '') {
                $error = 'Escriba el nombre del tipo de documento.';
            } elseif ($datos['plural'] === '') {
                $error = 'Escriba el nombre en plural.';
            } elseif (!preg_match('/^[a-z0-9_-]{2,30}$/', $datos['action'])) {
                $error = 'El identificador solo admite letras minúsculas, números, guion y guion bajo (de 2 a 30 caracteres).';
            } else {
                $repetido = ORM::factory('tipos')->where('action', '=', $datos['action']);
                if (!$nuevo) {
                    $repetido->where('id', '<>', $t->id);
                }
                if ($repetido->find()->loaded()) {
                    $error = 'Ya existe otro tipo con el identificador <b>' . HTML::chars($datos['action']) . '</b>.';
                } elseif (!$nuevo AND $datos['activo'] === 0 AND (int) $t->activo === 1) {
                    $usados = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM usertipo u
                                INNER JOIN users s ON s.id = u.id_user
                                WHERE u.id_tipo = :id AND s.habilitado = 1')
                            ->param(':id', (int) $t->id)->execute()->get('n');
                    if ($usados > 0) {
                        $error = 'No se desactivó: todavía hay ' . $usados . ' persona' . ($usados == 1 ? '' : 's') . ' con permiso para generar este tipo. Quíteselo primero en «Documentos permitidos».';
                    }
                }
            }

            if ($error === '') {
                foreach ($datos as $k => $v) {
                    $t->$k = $v;
                }
                $t->save();
                $this->save($this->user->id_entidad, $this->user->id,
                        'Administrador ' . ($nuevo ? 'creo' : 'edito') . ' el tipo de documento <b>' . $t->tipo . '</b>');
                Session::instance()->set('tipo_mensaje', $nuevo
                        ? 'Se creó el tipo de documento ' . $t->tipo . '.'
                        : 'Se guardaron los cambios de ' . $t->tipo . '.');
                $this->request->redirect('/admin/tipos');
            }
        }

        $uso = array('documentos' => 0, 'usuarios' => 0);
        if (!$nuevo) {
            $uso = DB::query(Database::SELECT, 'SELECT
                        (SELECT COUNT(*) FROM documentos WHERE id_tipo = :id) AS documentos,
                        (SELECT COUNT(*) FROM usertipo u INNER JOIN users s ON s.id = u.id_user
                            WHERE u.id_tipo = :id AND s.habilitado = 1) AS usuarios')
                    ->param(':id', (int) $t->id)->execute()->current();
        }

        $this->template->titulo .= $nuevo ? 'Nuevo tipo de documento' : 'Editar tipo de documento';
        $this->template->content = View::factory('admin/config/tipo_config')
            ->set('t', $t)
            ->set('nuevo', $nuevo)
            ->set('datos', $datos)
            ->set('uso', $uso)
            ->set('error', $error);
    }

}

?>
