<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_User extends Controller_AdminTemplate
{
    protected $user;
    protected $menus;

    public function before()
    {
        parent::before();
    }

    public function after()
    {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'admin');
        $oSM = New Model_menus();

        parent::after();
    }

    //
    //lista de usuarios
    public function action_index()
    {
        // son pocos cientos de usuarios: se cargan todos y la busqueda/orden se hace en el navegador
        // (antes era una grilla jqx que armaba los filtros en SQL con los valores del navegador)
        $usuarios = DB::query(Database::SELECT, 'SELECT u.id, u.username, u.nombre, u.cargo, u.email, u.mosca, u.genero,
                    u.logins, u.last_login, u.habilitado, u.fecha_creacion, n.nivel, o.oficina, e.sigla AS entidad
                FROM users u
                INNER JOIN niveles n ON u.nivel = n.id
                INNER JOIN oficinas o ON u.id_oficina = o.id
                INNER JOIN entidades e ON o.id_entidad = e.id
                ORDER BY u.nombre')
                ->execute()->as_array();
        $this->template->titulo .= 'Usuarios';
        $this->template->scripts = array('static/js/eModal.min.js');
        $this->template->content = View::factory('admin/usuarios_panel')
                ->bind('usuarios', $usuarios)
                ->set('aviso', Session::instance()->get_once('us_aviso', ''))
                ->set('yo', (int) $this->user->id);
    }
    public function action_index_old()
    {
        $oUser = New Model_Users();
        $users = $oUser->listaGeneral();
        if (sizeof($users) > 0) {
            $this->template->content = View::factory('admin/users')
                ->bind('users', $users);
        }
    }

    public function action_add()
    {
        if (isset($_POST['submit'])) {
            try {
                $message = '';
                $error = array();
                $email = ORM::factory('users')
                    ->where('email', '=', $_POST['email'])
                    ->find();
                if ($email->loaded()) {
                    $error['correo'] = "El correo ya existe en la base de datos";
                }
                $email = ORM::factory('users')
                    ->where('username', '=', $_POST['username'])
                    ->find();
                if ($email->loaded()) {
                    $error['username'] = "Ya existe un usuario con el nombre '" . $_POST['username'] . "'";
                }
                $passIngresado = trim(Arr::get($_POST, 'password', ''));
                if ($passIngresado !== '' && strlen($passIngresado) < 6) {
                    $error['password'] = 'La contraseña debe tener al menos 6 caracteres';
                }
                if (sizeof($error) == 0) {
                    if ($passIngresado !== '') {
                        $password = Auth::instance()->crear_hash($passIngresado);
                    } else {
                        // la contraseña por defecto queda en el formato viejo (ver Auth_ORM)
                        $oPassword = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
                        $password = Auth::instance()->hash_legado($oPassword->valor);
                    }
                    // Create the user using form values
                    $user = ORM::factory('users');
                    $user->username = $_POST['username'];
                    $user->password = $password;
                    $user->nombre = $_POST['nombre'];
                    $user->cargo = $_POST['cargo'];
                    $user->cedula_identidad = $_POST['cedula_identidad'];
                    $user->id_oficina = $_POST['id_oficina'];
                    $user->id_entidad = $_POST['id_entidad'];
                    $user->email = strtolower($_POST['email']);
                    $user->mosca = strtoupper($_POST['mosca']);
                    $user->nivel = $_POST['nivel'];
                    $user->genero = $_POST['genero'];
                    $user->dependencia = $_POST['dependencia'];
                    $user->superior = $_POST['superior'];
                    $user->fecha_creacion = time();
                    if ($user->save()) {
                        //$user->add('roles', 1);
                        $rol = ORM::factory('usersrol');
                        $rol->user_id = $user->id;
                        $rol->role_id = 1;
                        $rol->save();

                        $user = ORM::factory('users', $user->id);
                        $user->add('tipo', 3);
                        $user->add('tipo', 4);
                        $user->add('tipo', 5);
                        // Reset values so form is not sticky
                        $_POST = array();
                        // Set success message
                        $message = "Se creo el usuario '{$user->username}' correctamente";
                    }
                    // Grant user login role
//                $user->add('roles', 1);
                    //tipos
                }
            } catch (ORM_Validation_Exception $e) {

                // Set failure message
                $message = 'Usted tiene errores en el formulario revise por favor.';
                // Set errors using custom messages
                $error = $e->errors('models');
            }
        }
        $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

        $entidades = ORM::factory('entidades')->find_all();

        $options = array();
        foreach ($entidades as $e) {
            $options[$e->id] = $e->entidad;
        }
        $oNiveles = ORM::factory('niveles')->find_all();
        $niveles = array();
        foreach ($oNiveles as $n) {
            $niveles[$n->id] = $n->nivel;
        }
        $this->template->content = View::factory('admin/user/nuevo')
            ->bind('options', $options)
            ->bind('message', $message)
            ->bind('error', $error)
            ->bind('niveles', $niveles);
    }

    //editar usuario
    public function action_edit($id = 0)
    {
        $u = ORM::factory('users', (int) $id);
        if (!$u->loaded()) {
            $this->request->redirect('/admin/user');
        }
        $error = array();
        $datos = array(
            'username' => $u->username, 'nombre' => $u->nombre, 'cargo' => $u->cargo,
            'cedula_identidad' => $u->cedula_identidad, 'genero' => $u->genero, 'id_entidad' => $u->id_entidad,
            'id_oficina' => $u->id_oficina, 'superior' => (int) $u->superior, 'dependencia' => (int) $u->dependencia,
            'mosca' => $u->mosca, 'nivel' => (int) $u->nivel, 'email' => $u->email,
        );
        if ($this->request->method() === 'POST') {
            // el id sale de la URL, no del formulario
            foreach ($datos as $k => $v) {
                if (isset($_POST[$k])) {
                    $datos[$k] = trim($_POST[$k]);
                }
            }
            $datos['username'] = strtolower($datos['username']);
            $datos['email'] = strtolower($datos['email']);
            $datos['mosca'] = strtoupper($datos['mosca']);
            $datos['superior'] = (int) $datos['superior'];
            $datos['dependencia'] = (int) $datos['dependencia'] === 0 ? 0 : 1;
            $datos['genero'] = $datos['genero'] === 'mujer' ? 'mujer' : 'hombre';

            if ($datos['nombre'] === '') {
                $error['nombre'] = 'Escriba el nombre completo.';
            }
            if ($datos['cargo'] === '') {
                $error['cargo'] = 'Escriba el cargo.';
            }
            if (!preg_match('/^[a-z0-9._-]{3,50}$/', $datos['username'])) {
                $error['username'] = 'El usuario solo puede tener letras minúsculas, números, punto, guion o guion bajo (mínimo 3).';
            } elseif (ORM::factory('users')->where('username', '=', $datos['username'])->where('id', '<>', $u->id)->find()->loaded()) {
                $error['username'] = 'Ya existe otra persona con el usuario "' . $datos['username'] . '".';
            }
            if (!Valid::email($datos['email'])) {
                $error['email'] = 'El correo electrónico no es válido.';
            } elseif (ORM::factory('users')->where('email', '=', $datos['email'])->where('id', '<>', $u->id)->find()->loaded()) {
                $error['email'] = 'Ese correo ya lo usa otra persona.';
            }
            if ($datos['mosca'] === '') {
                $error['mosca'] = 'Escriba la rúbrica (mosca).';
            }
            $oficina = ORM::factory('oficinas', (int) $datos['id_oficina']);
            if (!$oficina->loaded() || (int) $oficina->id_entidad !== (int) $datos['id_entidad']) {
                $error['id_oficina'] = 'Elija una oficina de la entidad seleccionada.';
            }
            if (!ORM::factory('niveles', (int) $datos['nivel'])->loaded()) {
                $error['nivel'] = 'Elija un rol válido.';
            } elseif ((int) $datos['nivel'] === 5 && (int) $u->nivel !== 5) {
                // el rol de administrador del panel no se asigna desde esta pantalla
                $error['nivel'] = 'Elija un rol válido.';
            } elseif ((int) $u->nivel === 5 && (int) $datos['nivel'] !== 5) {
                // se puede quitar el rol de administrador, pero no al ultimo que queda
                $otros = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM users WHERE nivel = 5 AND habilitado = 1 AND id <> :id')
                        ->param(':id', (int) $u->id)
                        ->execute()->get('n');
                if ((int) $otros === 0) {
                    $error['nivel'] = 'Es el único administrador activo: primero dé el rol de administrador a otra persona.';
                }
            }
            if ($datos['superior'] === (int) $u->id) {
                $error['superior'] = 'Una persona no puede ser su propio superior.';
            } elseif ($datos['superior'] > 0 && !ORM::factory('users', $datos['superior'])->loaded()) {
                $error['superior'] = 'El superior elegido no existe.';
            }
            if (count($error) == 0) {
                foreach ($datos as $k => $v) {
                    $u->$k = $v;
                }
                // fecha_creacion ya no se pisa al editar
                $u->save();
                Session::instance()->set('us_aviso', 'Se guardaron los cambios de ' . $u->nombre . '.');
                $this->request->redirect('/admin/user' . ((int) $u->habilitado === 1 ? '' : '#baja'));
            }
        }

        $entidades = array();
        foreach (ORM::factory('entidades')->find_all() as $e) {
            $entidades[$e->id] = $e->entidad;
        }
        $oficinas = DB::query(Database::SELECT, 'SELECT id, id_entidad, oficina, sigla FROM oficinas ORDER BY oficina')
                ->execute()->as_array();
        // candidatos a superior: se filtran por oficina en el navegador
        $personas = DB::query(Database::SELECT, 'SELECT id, nombre, cargo, id_oficina, habilitado, dependencia FROM users
                WHERE id <> :id ORDER BY nombre')
                ->param(':id', (int) $u->id)
                ->execute()->as_array();
        $niveles = ORM::factory('niveles')->find_all()->as_array();
        $dependientes = DB::query(Database::SELECT, 'SELECT id, nombre, cargo FROM users
                WHERE superior = :id AND id <> :id AND habilitado = 1 ORDER BY nombre')
                ->param(':id', (int) $u->id)
                ->execute()->as_array();
        $oficina_actual = ORM::factory('oficinas', $u->id_oficina);

        $this->template->titulo .= 'Usuarios / Editar';
        $this->template->scripts = array('static/js/libs/select2/select2.min.js', 'static/js/eModal.min.js');
        $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');
        $this->template->content = View::factory('admin/user/edit')
                ->set('u', $u)
                ->set('datos', $datos)
                ->set('error', $error)
                ->set('entidades', $entidades)
                ->set('oficinas', $oficinas)
                ->set('personas', $personas)
                ->set('niveles', $niveles)
                ->set('dependientes', $dependientes)
                ->set('oficina_actual', $oficina_actual->loaded() ? $oficina_actual->oficina : '')
                ->set('yo', (int) $this->user->id);
    }

    //crear un nuevo usuario mediante 'id_oficina'
    public function action_create($id = 0)
    {

        $oficina = ORM::factory('oficinas', array('id' => $id));
        if ($oficina->loaded()) {
            $entidad = $oficina->entidad->find();
            $roles = ORM::factory('niveles')->find_all();
            $options = array();
            foreach ($roles as $o) {
                $options[$o->id] = $o->nivel;
            }
            $superiores = ORM::factory('users')
                ->where('id_oficina', '=', $id)
                ->and_where('dependencia', '=', 0)
                ->find_all();
            $jefes = array(0 => '');
            foreach ($superiores as $s):
                $jefes[$s->id] = $s->nombre;
            endforeach;
            $this->template->title = '' . $entidad->entidad;
            $this->template->content = View::factory('user/create')
                ->bind('options', $options)
                ->bind('message', $message)
                ->bind('errors', $errors)
                ->bind('oficina', $oficina)
                ->bind('jefes', $jefes)
                ->bind('entidad', $entidad);
            if ($_POST) {
                try {

                    //obtenemos el password por defecto 

                    $oPassword = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
                    // va en texto plano: create_user() le aplica el hash (filtro de Model_Auth_User);
                    // antes se mandaba ya hasheada y quedaba hasheada dos veces
                    $_POST['password'] = $oPassword->valor;

                    // Create the user using form values
                    $user = ORM::factory('user')->create_user($_POST, array(
                        'username',
                        'password',
                        'email',
                        'id_oficina',
                        'mosca',
                        'cargo',
                        'nombre',
                        'nivel',
                        'dependencia',
                        'genero',
                        'superior',
                        'id_entidad',
                    ));


                    // Grant user login role
                    $user->add('roles', 1);
                    //tipos
                    $user = ORM::factory('users', $user->id);
                    $user->add('tipo', 3);
                    $user->add('tipo', 4);
                    $user->add('tipo', 5);
                    // Reset values so form is not sticky
                    $_POST = array();

                    // Set success message
                    $message = "Se creo el usuario '{$user->username}' correctamente";
                } catch (ORM_Validation_Exception $e) {

                    // Set failure message
                    $message = 'Usted tiene errores en el formulario revise por favor.';
                    // Set errors using custom messages
                    $errors = $e->errors('models');
                }
            }
        }
    }

    //lista de usuarios por oficina
    public function action_lista($id = '')
    {
        $oficina = ORM::factory('oficinas', array('id' => $id));
        if ($oficina->loaded()) {
            $nombre_oficina = $oficina->oficina;
            $users = $oficina->users->find_all();
            $entidad = $oficina->entidad->find();  //vemos a entidad pertence la oficina (id_entidad)            
            $o_entidad = ORM::factory('entidades', $entidad);
            $oficinas = $o_entidad->oficinas->find_all();
            $options = array();
            foreach ($oficinas as $o) {
                $options[$o->id] = $o->oficina;
            }
            $this->template->titulo .= $oficina->oficina;
            $this->template->descripcion = 'Lista de usuarios de la oficina seleccionada';
            $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
            //$this->template->content = View::factory('admin/lista_usuarios')
            $this->template->content = View::factory('admin/user/lista')
                ->bind('users', $users)
                ->bind('oficina', $nombre_oficina)
                ->bind('id_oficina', $id)
                ->bind('users', $users)
                ->bind('options', $options)
                ->bind('entidad', $o_entidad)
                ->bind('id_entidad', $entidad);
        } else {
            $this->template->content = 'Oficina no encontrada';
        }
    }

    //detalle del usuario
    public function action_detalle($id = '')
    {
        $user = ORM::factory('users', array('id' => $id));
        if ($user->loaded()) {
            $documentos = $user->tipo->find_all();
            $oficina = ORM::factory('oficinas', $user->id_oficina);
            $o_destinos = New Model_Destinatarios();
            $destinatarios = $o_destinos->destinos($user->id);
            $this->template->titulo .= $user->nombre;
            $this->template->descripcion .= 'Configuracion del usuario';
            $this->template->content = View::factory('admin/user_detalle')
                ->bind('documentos', $documentos)
                ->bind('destinatarios', $destinatarios)
                ->bind('user', $user)
                ->bind('oficina', $oficina);
        } else {
            $this->template->content = 'Usuario inexistente';
        }
    }

    public function action_resetPass()
    {

    }

    //eliminar un destinanario

    public function action_x_des()
    {
        $id_usuario = Arr::get($_GET, 'id_user', '');
        $id_destino = Arr::get($_GET, 'id_des', '');
        if (($id_destino != '') && ($id_usuario != '')) {
            $destino = ORM::factory('destinatarios')
                ->where('id_usuario', '=', $id_usuario)
                ->and_where('id_destino', '=', $id_destino)
                ->find();

            $destino->delete();
        }
        $this->request->redirect('/admin/user/detalle/' . $id_usuario);
    }

    public function action_x_doc()
    {
        $id_usuario = Arr::get($_GET, 'id_user', '');
        $id_tipo = Arr::get($_GET, 'id_tipo', '');
        if (($id_tipo != '') && ($id_usuario != '')) {
            $user = ORM::factory('users', array('id' => $id_usuario));
            if ($user->has('tipo', $id_tipo))
                $user->remove('tipo', $id_tipo);
        }
        $this->request->redirect('/admin/user/detalle/' . $id_usuario);
    }

    //actualizar datos del usuario
    public function action_update()
    {
        if ($_POST) {
            $usuario = ORM::factory('users', array('id' => $_POST['user_id']));
            if ($usuario->loaded()) {
                $usuario->nombre = html::chars($_POST['nombre']);
                $usuario->cargo = html::chars($_POST['cargo']);
                $usuario->email = html::chars($_POST['email']);
                $usuario->dependencia = html::chars($_POST['dependencia']);
                $usuario->save();
                $this->request->redirect('/admin/user/detalle/' . $usuario->id);
            }
        }
    }

    public function action_list()
    {
        $usuarios = array();
        $oficinas = ORM::factory('oficinas')->find_all();
        foreach ($oficinas as $o) {
            $users = $o->users->find_all();
            foreach ($users as $u) {
                $usuarios[$u->id] = array(
                    'id_user' => $u->id,
                    'nombre' => $u->nombre,
                    'cargo' => $u->cargo,
                    'oficina' => $o->oficina,
                    'username' => $u->username,
                );
            }
        }
        //$users=ORM::factory('users')->where('id','<>',$this->user->id)->find_all();
        $this->template->titulo .= 'Listado general';
        $this->template->descripcion .= 'Lsiat general de usuarios ';
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('admin/users')
            ->bind('users', $usuarios);
    }

    public function action_login()
    {
        $this->template->content = View::factory('user/login')
            ->bind('errors', $errors)
            ->bind('message', $message);

        if ($_POST) {
            // Attempt to login user
            $remember = array_key_exists('remember', $_POST);
            $user = Auth::instance()->login($_POST['username'], $_POST['password'], $remember);

            // If successful, redirect user
            if ($user) {
                Request::current()->redirect('user/index');
            } else {
                $message = 'Error de acceso';
            }
        }
    }

    public function action_logout()
    {
        // Log user out
        Auth::instance()->logout();
        //header('Location: ../');
        // Redirect to login page
        Request::current()->redirect('');
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

}

?>