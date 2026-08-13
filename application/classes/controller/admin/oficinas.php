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
        $id_entidad = 13;
        $nombre_entidad = "Lista de todas las oficinas";
        if ($entidad->loaded()) {
            $id_entidad = $entidad->id;
            $nombre_entidad = $entidad->entidad;
        }
        $mOficinas = new Model_Oficinas();
        $oficinas = $mOficinas->listaOficinas($id);
        //lista entidades
        $options = array();
        $entidades = ORM::factory('entidades')->where('estado', '=', 1)->find_all();
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

        $this->template->content = View::factory('admin/oficinas/lista')
            ->bind('oficinas', $oficinas)
            ->bind('options', $options)
            ->bind('id_entidad', $id_entidad)
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
                    $info['Exito!'] = 'Se creo correctamente la oficina <b>' . $oficina->oficina . '</b>';
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

            $sigla2 = trim($_POST['sigla']);
            $sigla = ORM::factory('oficinas')->where('sigla', '=', $sigla2)->find();
            if ($sigla->loaded()) {
                $error['Error'] = 'Ya existe una oficina con la sigla <b>' . $_POST['sigla'] . '</b>, escriba otra por favor.';
            } else {
                $oficina->sigla = $sigla2;
            }

            //$oficina->id_entidad = $_POST['id_entidad'];
            $oficina->padre = $_POST['padre'];
            $oficina->oficina = $_POST['oficina'];
            if ($_POST['estado'] != '1') {
                $_POST['estado'] = 0;
            }
            $estado = $_POST['estado'];
            $oficina->estado = $estado;
            $oficina->save();
            $this->request->redirect('/admin/oficinas/lista');
        }

        $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');
        $this->template->content = View::factory('admin/oficinas/editar')
            ->bind('options', $options)
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
    public function action_remove($id = '')
    {
        $id_oficina = $id;

        $oficina = ORM::factory('oficinas')
            ->where('id', '=', $id_oficina)
            ->find();
        $oficina->estado = '0';
        $oficina->save();

        $this->request->redirect('/admin/oficinas/lista');
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
