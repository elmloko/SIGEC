<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_login extends Controller_Mintemplate {

    public function action_index() {
        // El formulario de acceso debe refrescarse para tomar cambios de estilo y sesión.
        $this->response->headers('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $this->response->headers('Pragma', 'no-cache');
        $this->response->headers('Expires', '0');

        $errors = array();
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            //$session = Session::instance();
            //$user = $session->get('session_native');
            if ($auth->get_user()->nivel == 5) {
                $this->request->redirect('admin');
            } else {
                $this->request->redirect('dashboard');

            }

        } else {
            if (isset($_POST['submit'])) {
                $validate = Validation::factory($this->request->post());
                $validate->rule('username', 'not_empty')
                        ->rule('password', 'not_empty');
                if ($validate->check()) {
                    //si activo el recordar
                    $remember = FALSE;
                    if (isset($_POST['remember'])) {
                        $remember = TRUE;
                    }

                    $user = $auth->login(html::chars($_POST['username']), html::chars($_POST['password']), $remember);
                    if ($user) {
                        $usuario = ORM::factory('users', $auth->get_user());

                        if ((int) $usuario->habilitado !== 1) {
                            // cuenta deshabilitada: se cierra la sesion recien creada y se rechaza el ingreso
                            $auth->logout(TRUE, TRUE);
                            $this->template->errors['login'] = 'Cuenta deshabilitada, contacte al administrador.';
                        } else {
                            $session = Session::instance();
                            $session->set('username', $usuario->nombre);
                            $session->set('username', $usuario->username);
                            $session->set('cargo', $usuario->cargo);
                            //vitacora
                            $this->save($usuario->id_entidad, $usuario->id, $usuario->nombre . ' / <b>' . $usuario->cargo . '</b> ingresó al sistema');

                            if ($usuario->nivel == 5) {
                                $this->request->redirect('admin');
                            } else {
                                $this->request->redirect($this->url_interna(Arr::get($_GET, 'url', '')));
                            }
                        }
                    }
                    else {
                        // mensaje generico: no revelar si el usuario existe o si fallo la contraseña
                        $this->template->errors['login'] = 'Usuario o contraseña incorrectos.';
                        //$_POST=array();
                    }
                }
            }
        }
        $this->template->title .= ' / Ingreso';
        $this->template->content = View::factory('login_form')->set('errors', $this->template->errors);
    }

    public function action_recovery() {
        $username = Arr::get($_GET, 'u', '');
        $token = Arr::get($_GET, 't', '');
        if ($username != '' && $token != '') {
            $pass = ORM::factory('resetpass')
                    ->where('username', '=', $username)
                    ->and_where('token', '=', $token)
                    ->find();
            if ($pass->loaded()) {
                $error = "";
                $info = "";
                $user = ORM::factory('users', $pass->user_id);

                if (isset($_POST['pass_old'])) {
                    $pass1 = $_POST['pass_new'];
                    $pass2 = $_POST['pass_new2'];
                    if ($pass1 == $pass2) {
                        $password = hash_hmac('sha256', $pass1, '2, 4, 6, 7, 9, 15, 20, 23, 25, 30');
                        $user->password = $password;
                        $user->save();
                        $pass->delete();
                        $info = "Se cambio exitosamente su contraseña, ir a <a href='/' class='btn btn-sm btn-primary'><i class='fa fa-user'></i> Formulario de autenticaci&oacute;n</a>";
                    } else {
                        $error = "Las contraseñas no coinciden, intentelo de nuevo";
                    }
                }
                $this->template->content = View::factory('user/recovery')
                        ->bind('user', $user)
                        ->bind('info', $info)
                        ->bind('error', $error);
            } else {
                // echo $user->token;
                $this->request->redirect('error404');
            }
        }
    }

    // solo permite volver a rutas internas del sistema (evita redirecciones a sitios externos)
    protected function url_interna($url) {
        $url = trim($url);
        if ($url === '' || strpos($url, '//') !== FALSE || strpos($url, '\\') !== FALSE
                || !preg_match('#^/?[A-Za-z0-9_\-./?=&%+]*$#', $url)) {
            return 'dashboard';
        }
        return ltrim($url, '/');
    }

}

?>
