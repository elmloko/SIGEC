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
            'media/jqwidgets/jqxadata.export.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            //'media/jqwidgets/jqxgrid.grouping.js',
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
            'static/js/eModal.min.js'
        );
        $this->template->content = View::factory('admin/j_usuarios');
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
                if (sizeof($error) == 0) {
                    $oPassword = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
                    $password = hash_hmac('sha256', $oPassword->valor, '2, 4, 6, 7, 9, 15, 20, 23, 25, 30'); //sigec users
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
    public function action_edit($id)
    {

        $u = ORM::factory('users')->where('id', '=', $id)->find();
        if ($u->loaded()) {
            if (isset($_POST['submit'])) {
                try {
                    $message = '';
                    $error = array();

                    $email = ORM::factory('users')
                        ->where('email', '=', $_POST['email'])
                        ->and_where('id', '<>', $_POST['id'])
                        ->find();
                    if ($email->loaded()) {
                        $error['correo'] = "El correo ya existe en la base de datos, elija otro por favor.";
                    }
                    $email = ORM::factory('users')
                        ->where('username', '=', $_POST['username'])
                        ->where('id', '<>', $_POST['id'])
                        ->find();
                    if ($email->loaded()) {
                        $error['username'] = "Ya existe un usuario con el nombre '" . $_POST['username'] . "', elija otro por favor";
                    }
                    if (sizeof($error) == 0) {
                        // Create the user using form values
                        $user = ORM::factory('users', $_POST['id']);
                        $user->username = $_POST['username'];
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
                            $u = $user;
                            //$user->add('roles', 1);
//                            $rol = ORM::factory('usersrol');
                            //                          $rol->user_id = $user->id;
                            //                        $rol->role_id = 1;
                            //                      $rol->save();
                            //                    $user = ORM::factory('users', $user->id);
                            //                  $user->add('tipo', 3);
                            //                $user->add('tipo', 4);
                            //              $user->add('tipo', 5);
                            // Reset values so form is not sticky
                            $_POST = array();
                            // Set success message
                            $message = "Se modifico el usuario '{$user->username}' correctamente";

                            // RELOAD PAGE
                            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/admin/user";

                            echo '<script>window.location = "' . $url . '";</script>';
                            die;
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

            $usuario = array();

            $usuario['id'] = $u->id;
            $usuario['username'] = $u->username;
            $usuario['nombre'] = $u->nombre;
            $usuario['cargo'] = $u->cargo;
            $usuario['cedula_identidad'] = $u->cedula_identidad;
            $usuario['genero'] = $u->genero;
            $usuario['id_oficina'] = $u->id_oficina;

            $usuario['id_entidad'] = $u->id_entidad;
            $usuario['superior'] = $u->superior;
            $usuario['dependencia'] = $u->dependencia;
            $usuario['mosca'] = $u->mosca;
            $usuario['nivel'] = $u->nivel;
            $usuario['email'] = $u->email;

            $this->template->scripts = array('media/js/select-chain.js', 'static/js/libs/select2/select2.min.js');
            $this->template->styles = array('static/css/theme-1/libs/select2/select2.css' => 'all');

            $entidades = ORM::factory('entidades')->find_all();

            $options = array();
            foreach ($entidades as $e) {
                $options[$e->id] = $e->entidad;
            }
            //oficinas
            $oOficinas = ORM::factory('oficinas')->where('id_entidad', '=', $u->id_entidad)->find_all();
            $oficinas = array();
            foreach ($oOficinas as $o) {
                $oficinas[$o->id] = $o->oficina;
            }
            $oNiveles = ORM::factory('niveles')->find_all();
            $niveles = array();
            foreach ($oNiveles as $n) {
                $niveles[$n->id] = $n->nivel;
            }
            $this->template->content = View::factory('admin/user/edit')
                ->bind('usuario', $usuario)
                ->bind('oficinas', $oficinas)
                ->bind('options', $options)
                ->bind('message', $message)
                ->bind('error', $error)
                ->bind('niveles', $niveles);
        } else {

        }
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
                    $password = hash_hmac('sha256', $oPassword->valor, '2, 4, 6, 7, 9, 15, 20, 23, 25, 30'); //sigec users
                    $_POST['password'] = $password;

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