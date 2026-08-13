<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            //menu top de acuerdo al nivel
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            $oNivel = New Model_niveles();
            $this->menus = $oNivel->menus($this->user->nivel);
            parent::before();
            $this->template->title = 'Usuario /';
            $this->template->titulo = '<v>Usuario / </v>';
            if ($this->user->theme != null) {
                $this->template->theme = $this->user->theme;
            }
        } else {
            $url = substr($_SERVER['REQUEST_URI'], 1);
            $this->request->redirect('/login?url=' . $url);
        }
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'bandeja');
        $oSM = New Model_menus();
        $submenus = $oSM->submenus('user');
        $this->template->submenu = View::factory('templates/submenu')->bind('smenus', $submenus)->set('titulo', 'Menu Usuario');
        $this->template->username = $this->user->nombre;
        $this->template->cargo = $this->user->cargo;
        parent::after();
    }

    public function action_index() {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);
        $oficina = $oficina->oficina;
        $user = $this->user;
        $this->template->title = $this->user->nombre;
        $this->template->titulo.=$this->user->username;
        $entidad = ORM::factory('entidades')->where('id', '=', $this->user->id_entidad)->find();
        $this->template->descripcion = $entidad->entidad;
        $this->template->content = View::factory('user/info')
                ->bind('user', $user)
                ->bind('oficina', $oficina);
    }

    public function action_password() {
        $errors = array();
        $info = array();
        if ($_POST) {

            $auth = Auth::instance();
            $pass_old = $auth->hash_password($_POST['pass_old']);
            if ($pass_old == $this->user->password) { //verificamos que el password anterior coincida
                if ($_POST['pass_new'] == $_POST['pass_new2']) {
                    $user = ORM::factory('users', array('id' => $this->user->id));
                    if ($user->loaded()) {
                        $user->password = $auth->hash_password($_POST['pass_new']);
                        $user->save();
                        $info[] = 'Su contraseña fue  cambiado correctamente';
                        //vitacora
                        $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' <b>' . $this->user->cargo . '</b> Cambio su contrase&ntilde;');
                    }
                } else {
                    $errors[] = 'Las contraseñas no coinciden';
                }
            } else {
                $errors[] = 'La contraseña anterior es incorrecta';
            }
        }
        $user = $this->user;
        $this->template->title.=' / Cambiar contrase&ntilde;a';
        $this->template->titulo.=' Cambiar contrase&ntilde;a';
        $this->template->descripcion = 'Cambie su contrase&ntilde;a de ingreso al sistema';
        $this->template->content = View::factory('user/change_pass')
                ->bind('user', $user)
                ->bind('errors', $errors)
                ->bind('info', $info);
    }

    public function action_data() {
        $errors = array();
        $info = array();
        if ($_POST) {

            $user = ORM::factory('users', array('id' => $this->user->id));
            if ($user->loaded()) {
                $user->nombre = $_POST['nombre'];
                $user->cargo = $_POST['cargo'];
                $user->mosca = $_POST['mosca'];
                $user->email = $_POST['email'];
                $user->genero = $_POST['genero'];
                $user->save();
                $info[] = 'Sus datos fueron cambiados correctamente';
            } else {
                $errors[] = 'Ocurrio un error, vuelva a inentarlo';
            }
        }
        $user = $this->user;
        $this->template->title.=' / Cambiar datos personales';
        $this->template->titulo.=' Cambiar datos personales';
        $this->template->descripcion = 'Cambie sus datos de usuario';
        $this->template->content = View::factory('user/change_data')
                ->bind('user', $user)
                ->bind('errors', $errors)
                ->bind('info', $info);
    }

    public function action_nuevo() {
        $entidades = ORM::factory('entidades')->find_all();
        $options = array();
        foreach ($entidades as $e) {
            $options[$e->id] = $e->entidad;
        }
        $this->template->content = View::factory('admin/nuevo1')
                ->bind('options', $options);
    }

    public function action_create() {
        $oficinas = ORM::factory('oficinas')->find_all();
        $options = array('' => 'Seleccione oficina...');
        foreach ($oficinas as $o) {
            $options[$o->id] = $o->oficina;
        }
        //options cargos
        $opCargos = '';
        $cargos = ORM::factory('users')->find_all();
        foreach ($cargos as $c) {
            $opCargos.='<option value="' . $c->id . '" class="' . $c->id_oficina . '">' . $c->cargo . ' - ' . $c->nombre . '</options>';
        }
        $this->template->content = View::factory('user/create')
                ->bind('options', $options)
                ->bind('opCargos', $opCargos)
                ->bind('message', $message)
                ->bind('errors', $errors);

        /* $oficinas=ORM::factory('oficinas')->find_all();
          $this->template->content = View::factory('user/create')
          ->bind('errors', $errors)
          ->bind('message', $message)
          ->bind('oficinas',$oficinas); */

        if ($_POST) {
            try {

                // Create the user using form values
                $user = ORM::factory('user')->create_user($_POST, array(
                    'username',
                    'password',
                    'email',
                    'id_oficina',
                    'mosca',
                    'cargo',
                    'nombre',
                    'superior'
                ));

                // Grant user login role
                $user->add('roles', ORM::factory('role', array('name' => 'login')));

                // Reset values so form is not sticky
                $_POST = array();

                // Set success message
                $message = "You have added user '{$user->username}' to the database";
            } catch (ORM_Validation_Exception $e) {

                // Set failure message
                $message = 'There were errors, please see form below.';

                // Set errors using custom messages
                $errors = $e->errors('models');
            }
        }
    }

    public function action_logout() {
        $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' <b>' . $this->user->cargo . '</b> salio del sistema');
        Auth::instance()->logout();
        Request::current()->redirect('');
    }

    public function action_list($id = '') {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)->find_all();
            $this->template->content = View::factory('user/list')->bind('usuarios', $usuarios);
        }
    }

    public function action_listar() {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $user = ORM::factory('users', $auth->get_user());
            if ($user->nivel == 3) {
                $oficina = ORM::factory('oficinas', 27);
                $usuarios = ORM::factory('users')->where('id_oficina', '=', 27)->find_all();
                $this->template->menu = View::factory('admin/menu');
                $this->template->content = View::factory('user/list')->bind('usuarios', $usuarios)
                        ->bind('oficina', $oficina);
            } else {
                $this->template->content = View::factory('errors/user');
            }
        }
    }

    public function action_info() {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);
        $oficina = $oficina->oficina;
        $user = $this->user;
        $this->template->title = $this->user->nombre;
        $this->template->titulo.=$this->user->username;
        $entidad = ORM::factory('entidades')->where('id', '=', $this->user->id_entidad)->find();
        $this->template->descripcion = $entidad->entidad;
        $this->template->content = View::factory('user/info')
                ->bind('user', $user)
                ->bind('oficina', $oficina);
    }

    public function action_destinatarios() {
        $o_destinos = New Model_Destinatarios();
        $destinatarios = $o_destinos->destinos($this->user->id);
        $this->template->title.='Destinatarios';
        $this->template->titulo.='Destinatarios';
        $this->template->descripcion.='Lista de destinatarios permitidos';
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/style.css' => 'all');
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('user/destinatarios')
                ->bind('destinatarios', $destinatarios)
                ->bind('user', $this->user);
    }

    public function action_x_des() {
        $id_usuario = Arr::get($_GET, 'id_user', '');
        $id_destino = Arr::get($_GET, 'id_des', '');
        if (($id_destino != '') && ($id_usuario != '')) {
            $destino = ORM::factory('destinatarios')
                    ->where('id_usuario', '=', $id_usuario)
                    ->and_where('id_destino', '=', $id_destino)
                    ->find();

            $destino->delete();
        }
        $this->request->redirect('/user/destinatarios/' . $id_usuario);
    }

    public function action_color($color = '') {
        if ($color != '') {
            $user = ORM::factory('users', $this->user->id);
            if ($user->loaded()) {
                $user->theme = $color;
                $user->save();
                $this->request->redirect('user/color');
            }
        }
        $this->template->title.='Personalizar color';
        $this->template->titulo.='Personalizar color';
        $this->template->descripcion.='Personalizar el color del sistema';
        $colores = array(
            'azul' => '3487E3',
            'amarillo' => 'D3C702',
            'verde' => '619018',
            'naranja' => 'F8A006',
            'purpura' => '9102D3',
            'rojo' => 'CE1E16',
            'verdeazulado' => '069294',
            'violeta' => 'D302A9',
            'verdeclaro' => '8DC643',
            'cafe' => 'A04C1A',
            'negro' => '111',
            'plomo' => '8B8B8B',
            'amarilloclaro' => 'FFF600',
            'azulmarino' => '35338A',
        );
        $this->template->content = View::factory('user/color')
                ->bind('colores', $colores);
    }

    public function action_organigrama() {
        $ide = $this->user->id_entidad;
        $entidad = ORM::factory('entidades', $ide);
        if ($entidad->loaded()) {
            $oficina = ORM::factory('oficinas')
                    ->where('id_entidad', '=', $entidad->id)
                    ->and_where('principal', '=', 1)                    
                    ->find();

            $this->lista = '<ul id="entidad">';
            // echo '<ul>';

            $this->listar($oficina, $entidad->entidad, $entidad->sigla);
            //   echo '</ul>';
            $this->lista.='</ul>';
            $config = array();
            //$config=  ORM::factory('configuracion',1);

            $this->template->styles = array(
                'media/css/bootstrap.min.css' => 'all',
                'media/css/jquery.jOrgChart.css' => 'all',);
            $this->template->scripts = array(
                'media/js/jquery.jOrgChart.js',);

            $this->template->descripcion = $entidad->entidad;
            $this->template->title.= ' Organigrama';
            $this->template->titulo = '<v>Organigrama</v>';
            // $this->template->menu=  View::factory('admin/menu');
            $this->template->content = View::factory('oficina/lista')
                    ->bind('lista', $this->lista)
                    ->bind('entidad', $entidad)
                    ->bind('config', $config);
            echo $this->lista;
        }
    }

    public function listar($id, $oficina, $sigla) {
        $h = ORM::factory('oficinas')->where('padre', '=', $id)->count_all();
        //echo '<li>'.$oficina;       
        //$this->lista.='<li class="oficina" style="display:none;">'.HTML::anchor('admin/user/lista/'.$id,$oficina.' <br/> '.$sigla);
        $this->lista.='<li class="oficina" style="display:none;">' . HTML::anchor('user/oficina/' . $id, $oficina);
        if ($h > 0) {
            //echo '<ul>';
            $this->lista.='<ul>';
            $hijos = ORM::factory('oficinas')->where('padre', '=', $id)->find_all();
            foreach ($hijos as $hijo) {
                $oficina = $hijo->oficina;
                $this->listar($hijo->id, $oficina, $hijo->sigla);
            }
            $this->lista.='</ul>';
            // echo '</ul>';
        } else {
            $this->lista.='</li>';
            //   echo '</li>';
        }
    }

    public function action_oficina($id = 0) {

        $oficina = ORM::factory('oficinas', $id);
        if ($oficina->loaded()) {
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)->find_all();
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
            $this->template->title.=' ' . $oficina->oficina;
            $this->template->titulo = '<v>' . $oficina->oficina . '</v>';
            $this->template->descripcion = 'Lista de Personal';
            $this->template->content = View::factory('user/personal')
                    ->bind('usuarios', $usuarios);
        } else {
            $this->template->content = 'Oficina no encontrada';
        }
    }

}

?>
