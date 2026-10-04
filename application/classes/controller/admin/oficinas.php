<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Oficinas extends Controller_AdminTemplate
{

    protected $user;
    protected $menus;

    public function before()
    {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            //menu top de acuerdo al nivel
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($this->user->nivel);
            parent::before();
            $this->template->titulo = '<v>Oficina / </v> ';
            $this->template->descripcion = '';
            $this->template->username = $this->user->nombre;
            // $this->template->title='<li>'.html::anchor('admin','Bandeja').'</li>';
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

    // lista de oficinas
    public function action_index()
    {
        $oOficina = New Model_Oficinas();
        $oficinas = $oOficina->lista_oficinas();
        $this->template->title .= ' Listar';
        $this->template->titulo .= ' Listar';
        $this->template->descripcion .= ' Listar general de oficinas';
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('admin/lista_oficinas')
            ->bind('oficinas', $oficinas);
    }

    public function action_lista($id = '')
    {
        $entidad = ORM::factory('entidades')->where('id', '=', $id)->and_where('estado', '=', 1)->find();
        // sin filtro el selector debe quedar en "Todas las entidades", no en una fija
        $id_entidad = '';
        $nombre_entidad = 'Lista de todas las oficinas';
        if ($entidad->loaded()) {
            $id_entidad = $entidad->id;
            $nombre_entidad = $entidad->entidad;
        }
        // se listan todas, activas e inactivas: antes las inactivas desaparecian y no habia
        // forma de volver a activarlas desde la pantalla
        $oficinas = DB::query(Database::SELECT, 'SELECT o.id, o.padre, o.oficina, o.sigla, o.estado,
                    e.entidad, e.id AS id_entidad,
                    (SELECT COUNT(*) FROM users WHERE id_oficina = o.id AND habilitado = 1) AS usuarios,
                    (SELECT oficina FROM oficinas p WHERE p.id = o.padre) AS nombre_padre
                FROM oficinas o
                INNER JOIN entidades e ON o.id_entidad = e.id'
                . ($entidad->loaded() ? ' WHERE e.id = :id' : '')
                . ' ORDER BY e.entidad, o.oficina')
                ->param(':id', (int) $id)
                ->execute()->as_array();
        //lista entidades
        $options = array();
        $entidades = ORM::factory('entidades')->where('estado', '=', 1)->order_by('entidad')->find_all();
        foreach ($entidades as $e) {
            $options[$e->id] = $e->entidad;
        }
        //<script src="../../assets/"></script>
        //<script src="../../assets/
        $this->template->scripts = array('media/js/select-chain.js',
            'static/js/libs/select2/select2.min.js',
            'static/js/libs/DataTables/extensions/ColVis/js/dataTables.colVis.min.js',
            'static/js/libs/DataTables/jquery.dataTables.min.js',
            //'media/js/jquery.tablesorter.min.js'
        );
        $this->template->styles = array(
            'static/css/theme-1/libs/select2/select2.css' => 'all',
            'static/css/theme-1/libs/DataTables/extensions/dataTables.colVis.css' => 'all',
            'static/css/theme-1/libs/DataTables/jquery.dataTables.css' => 'all'
        );

        /*
        // Obteniendo el id_entidad
        if (count($options) === 1) {
            reset($options);
            $id = key($options);
        }
        */

        $session = Session::instance();
        $this->template->titulo .= 'Oficinas';
        $this->template->content = View::factory('admin/oficinas/lista')
            ->bind('oficinas', $oficinas)
            ->bind('options', $options)
            ->bind('id_entidad', $id_entidad)
            ->set('filtrada', $entidad->loaded())
            ->set('mensaje', $session->get_once('oficina_mensaje', ''))
            ->set('error', $session->get_once('oficina_error', ''))
            ->bind('entidad', $nombre_entidad);
    }

    public function action_nuevo()
    {
        $entidades = ORM::factory('entidades')->find_all();
        $options = array();
        foreach ($entidades as $e) {
            $options[$e->id] = $e->entidad;
        }
        if ($_POST) {
            $this->request->redirect('/admin/user/create/' . $_POST['entidad']);
        }
        $this->template->content = View::factory('admin/nuevo1')
            ->bind('options', $options);
    }

    //nueva oficina
    public function action_create($id = '')
    {
        $id_entidad = $id;

        $error = array();
        $info = array();
        if (isset($_POST['create'])) {
            $id = $_POST['id_entidad'];
            $sigla2 = trim($_POST['sigla']);
            $sigla = ORM::factory('oficinas')->where('sigla', '=', $sigla2)->find();
            if ($sigla->loaded()) {
                $error['Error'] = 'Ya existe una oficina con la sigla <b>' . $_POST['sigla'] . '</b>, escriba otra por favor.';
            } else {
                $oficina = ORM::factory('oficinas');
                $oficina->id_entidad = $_POST['id_entidad'];
                $oficina->oficina = $_POST['oficina'];
                $oficina->sigla = $sigla2;
                $oficina->padre = $_POST['padre'];
                $oficina->estado = '1';
                $oficina->save();
                if ($oficina->id) {
                    //ahora guardamos por defecto el cite para los tipos de documentos
                    $tipos = ORM::factory('tipos')->where('doc', '=', 0)->find_all();
                    foreach ($tipos as $t) {
                        $correlativo = ORM::factory('correlativo');
                        $correlativo->id_oficina = $oficina->id;
                        $correlativo->id_tipo = $t->id;
                        $correlativo->save();
                    }
                    $info['Exito!'] = 'Se creo correctamente la oficina <b>' . HTML::chars($oficina->oficina) . '</b>';
                    $_POST = array();
                }
            }
        }
        $oEntidades = ORM::factory('entidades')->find_all();

        $entidades = array();
        foreach ($oEntidades as $e) {
            $entidades[$e->id] = $e->entidad;
        }
        $entidad = ORM::factory('entidades', array('id' => $id));
        if ($entidad->loaded()) {
            $options = array('0' => 'Oficina Inicial');
            $oficinas = ORM::factory('oficinas')->where('id_entidad', '=', $entidad->id)->find_all();
            foreach ($oficinas as $o) {
                $options[$o->id] = $o->oficina . ' | ' . $o->sigla;
            }
            if (sizeof($options) == 0) {
                $options[0] = 'Oficina Principal';
            }
            $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
            $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

            $this->template->content = View::factory('admin/oficinas/nuevo')
                ->bind('options', $options)
                ->bind('entidades', $entidades)
                ->bind('id_entidad', $id_entidad)
                ->bind('error', $error)
                ->bind('info', $info);
        }
    }

    //editar oficina
    public function action_edit($id = '')
    {
        $error = array();
        $info = array();
        $id_oficina = $id;

        $oficina = ORM::factory('oficinas')
            ->where('id', '=', $id_oficina)
            ->find();
        $nombre_oficina = $oficina->oficina;
        $id_entidad = $oficina->id_entidad;
        $id_padre_oficina = $oficina->padre;
        $sigla = $oficina->sigla;
        $estado = $oficina->estado;

        $oEntidades = ORM::factory('entidades')->find_all();
        $entidades = array();
        foreach ($oEntidades as $e) {
            $entidades[$e->id] = $e->entidad;
        }

        $options = array('0' => 'Oficina Inicial');
        $oficinas = ORM::factory('oficinas')->where('id_entidad', '=', $id_entidad)->find_all();
        foreach ($oficinas as $o) {
            $options[$o->id] = $o->oficina . ' | ' . $o->sigla;
        }
        if (sizeof($options) == 0) {
            $options[0] = 'Oficina Principal';
        }

        if (isset($_POST['edit'])) {
            $nueva_sigla = strtoupper(trim($_POST['sigla']));
            $nuevo_nombre = trim($_POST['oficina']);
            $nuevo_padre = (int) Arr::get($_POST, 'padre', 0);
            $nuevo_estado = isset($_POST['estado']) ? 1 : 0;

            if ($nuevo_nombre === '') {
                $error['Error'] = 'Escriba el nombre de la oficina.';
            } elseif ($nueva_sigla === '') {
                $error['Error'] = 'Escriba la sigla de la oficina.';
            } elseif (ORM::factory('oficinas')->where('sigla', '=', $nueva_sigla)
                    ->where('id', '<>', $oficina->id)->find()->loaded()) {
                // antes se comparaba contra TODAS las oficinas, incluida esta misma:
                // guardar sin tocar la sigla siempre daba "ya existe" y la sigla no se guardaba
                $error['Error'] = 'Ya existe otra oficina con la sigla <b>' . HTML::chars($nueva_sigla) . '</b>.';
            } elseif ($nuevo_padre === (int) $oficina->id) {
                $error['Error'] = 'Una oficina no puede depender de sí misma.';
            } elseif ($nuevo_estado === 0 AND (int) $oficina->estado === 1) {
                $activos = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM users WHERE id_oficina = :id AND habilitado = 1')
                        ->param(':id', (int) $oficina->id)->execute()->get('n');
                if ($activos > 0) {
                    $error['Error'] = 'No se desactivó: la oficina todavía tiene ' . $activos . ' usuario' . ($activos == 1 ? '' : 's') . ' activo' . ($activos == 1 ? '' : 's') . '.';
                }
            }

            if (sizeof($error) == 0) {
                $oficina->sigla = $nueva_sigla;
                $oficina->padre = $nuevo_padre;
                $oficina->oficina = $nuevo_nombre;
                $oficina->estado = $nuevo_estado;
                $oficina->save();
                $this->save($this->user->id_entidad, $this->user->id, 'Administrador edito la oficina <b>' . $oficina->oficina . '</b>');
                Session::instance()->set('oficina_mensaje', 'Se guardaron los cambios de ' . $oficina->oficina . '.');
                $this->request->redirect('/admin/oficinas/lista/' . (int) $oficina->id_entidad);
            }
            // se conserva lo que escribio para no perderlo al mostrar el error
            $nombre_oficina = $nuevo_nombre;
            $sigla = $nueva_sigla;
            $id_padre_oficina = $nuevo_padre;
            $estado = $nuevo_estado;
        }

        $usuarios = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM users WHERE id_oficina = :id AND habilitado = 1')
                ->param(':id', (int) $oficina->id)->execute()->get('n');
        $hijas = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM oficinas WHERE padre = :id')
                ->param(':id', (int) $oficina->id)->execute()->get('n');

        $this->template->titulo .= 'Editar oficina';
        $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');
        $this->template->content = View::factory('admin/oficinas/editar')
            ->bind('options', $options)
            ->set('oficina', $oficina)
            ->set('usuarios', $usuarios)
            ->set('hijas', $hijas)
            ->bind('entidades', $entidades)
            ->bind('nombre_oficina', $nombre_oficina)
            ->bind('id_entidad', $id_entidad)
            ->bind('id_padre_oficina', $id_padre_oficina)
            ->bind('estado', $estado)
            ->bind('sigla', $sigla)
            ->bind('error', $error)
            ->bind('info', $info);

    }

    // eliminacion logica de oficina
    // desactiva o vuelve a activar una oficina. Antes bastaba con abrir el enlace: cualquier
    // clic (o el prefetch del navegador) desactivaba la oficina, asi que ahora exige un POST.
    public function action_remove($id = '')
    {
        $session = Session::instance();
        $oficina = ORM::factory('oficinas', (int) $id);
        if ($this->request->method() !== Request::POST OR !$oficina->loaded()) {
            $this->request->redirect('/admin/oficinas/lista');
        }
        $volver = '/admin/oficinas/lista/' . (int) $oficina->id_entidad;

        if ((int) $oficina->estado === 1) {
            // con gente activa dentro, desactivarla la deja sin bandeja ni destinatarios
            $usuarios = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM users WHERE id_oficina = :id AND habilitado = 1')
                    ->param(':id', (int) $oficina->id)
                    ->execute()->get('n');
            if ($usuarios > 0) {
                $session->set('oficina_error', 'No se desactivó ' . $oficina->oficina . ': todavía tiene ' . $usuarios . ' usuario' . ($usuarios == 1 ? '' : 's') . ' activo' . ($usuarios == 1 ? '' : 's') . '. Trasládelos o deles de baja primero.');
                $this->request->redirect($volver);
            }
            $oficina->estado = 0;
            $oficina->save();
            $this->save($this->user->id_entidad, $this->user->id, 'Administrador desactivo la oficina <b>' . $oficina->oficina . '</b>');
            $session->set('oficina_mensaje', 'Se desactivó la oficina ' . $oficina->oficina . '.');
        } else {
            $oficina->estado = 1;
            $oficina->save();
            $this->save($this->user->id_entidad, $this->user->id, 'Administrador activo la oficina <b>' . $oficina->oficina . '</b>');
            $session->set('oficina_mensaje', 'Se activó la oficina ' . $oficina->oficina . '.');
        }
        $this->request->redirect($volver);
    }

    public function action_oficinas($id = '')
    {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            /* $oData=new Model_data();
              $usuarios=$oData->usuarios($id);
             * */
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)->find_all();
            $this->template->content = View::factory('user/list')->bind('usuarios', $usuarios);
        }
    }

    //lista de usuarios
    public function action_listar()
    {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $user = ORM::factory('users', $auth->get_user());
            $oficina = ORM::factory('oficinas', 27);
            $usuarios = ORM::factory('users')->where('id_oficina', '=', 27)->find_all();
            $this->template->menu = View::factory('admin/menu');
            $this->template->content = View::factory('user/list')->bind('usuarios', $usuarios)
                ->bind('oficina', $oficina);
        }
    }

    public function action_listado($ide = '')
    {
        $entidad = ORM::factory('entidades', $ide);
        if ($entidad->loaded()) {
            $oficina = ORM::factory('oficinas')
                ->where('id_entidad', '=', $entidad->id)
                ->and_where('padre', '=', 0)
                ->find();

            $this->lista = '<ul id="entidad">';
            // echo '<ul>';

            $this->listar($oficina, $entidad->entidad, $entidad->sigla);
            //   echo '</ul>';
            $this->lista .= '</ul>';
            $config = array();
            //$config=  ORM::factory('configuracion',1);
            $this->template->descripcion = $entidad->entidad;
            $this->template->title .= ' / Organigrama';
            $this->template->titulo = '<v>Organigrama</v>';
            //$this->template->menu = View::factory('admin/menu');
            $this->template->content = View::factory('oficina/lista')
                ->bind('lista', $this->lista)
                ->bind('entidad', $entidad)
                ->bind('config', $config);
        }
    }

    public function listar($id, $oficina, $sigla)
    {
        $h = ORM::factory('oficinas')->where('padre', '=', $id)->count_all();
        //echo '<li>'.$oficina;       
        //$this->lista.='<li class="oficina" style="display:none;">'.HTML::anchor('admin/user/lista/'.$id,$oficina.' <br/> '.$sigla);
        $this->lista .= '<li class="oficina" style="display:none;">' . HTML::anchor('admin/user/lista/' . $id, $oficina);
        if ($h > 0) {
            //echo '<ul>';
            $this->lista .= '<ul>';
            $hijos = ORM::factory('oficinas')->where('padre', '=', $id)->find_all();
            foreach ($hijos as $hijo) {
                $oficina = $hijo->oficina;
                $this->listar($hijo->id, $oficina, $hijo->sigla);
            }
            $this->lista .= '</ul>';
            // echo '</ul>';
        } else {
            $this->lista .= '</li>';
            //   echo '</li>';
        }
    }

}

?>
