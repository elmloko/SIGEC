<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_DefaultTemplate
{

    protected $user;
    protected $menus;

    public function before()
    {
        parent::before();
        //si el usuario esta logeado entocnes mostramos el menu
    }

    public function after()
    {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'user');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    public function action_index()
    {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);
        $oficina = $oficina->oficina;
        $user = $this->user;
        $this->template->title = $this->user->nombre;
        $this->template->titulo .= $this->user->username;
        $entidad = ORM::factory('entidades')->where('id', '=', $this->user->id_entidad)->find();
        $this->template->descripcion = $entidad->entidad;
        $this->template->content = View::factory('user/info')
            ->bind('user', $user)
            ->bind('oficina', $oficina);
    }

    public function action_pass()
    {
        $errors = array();
        $info = array();
        if ($_POST['pass_old']) {
            $auth = Auth::instance();
            $pass_old = $auth->hash_password($_POST['pass_old']);
            if ($pass_old == $this->user->password) { //verificamos que el password anterior coincida
                if ($_POST['pass1'] == $_POST['pass2']) {
                    $user = ORM::factory('users', array('id' => $this->user->id));
                    if ($user->loaded()) {
                        $user->password = $auth->hash_password($_POST['pass1']);
                        $user->save();
                        $info[] = 'Su contraseña fue cambiado correctamente';
                        //vitacora
                        $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' ' . $this->user->cargo . ' cambio su contrase&ntilde;');
                    }
                } else {
                    $errors[] = 'Las contraseñas nuevas no coinciden';
                }
            } else {
                $errors[] = 'La contraseña actual es incorrecta';
            }
        }
        $user = $this->user;
        $this->template->content = View::factory('user/change_pass')
            ->bind('user', $user)
            ->bind('errors', $errors)
            ->bind('info', $info);
    }

    public function action_profile($id = "")
    {
        $errors = array();
        $info = array();
        // el administrador (nivel 5) puede ver y editar el perfil de otros; el resto, solo el suyo
        if ($id != "" && (int) $this->user->nivel === 5) {
            $user = ORM::factory('users', array('id' => (int) $id));
        } else {
            $user = ORM::factory('users', array('id' => $this->user->id));
        }
        if (!$user->loaded()) {
            $this->request->redirect('user/profile');
        }
        $es_propio = ((int) $user->id === (int) $this->user->id);
        $dir_fotos = DOCROOT . 'static/fotos/';

        //cambiar datos personales
        if (isset($_POST['submit-usuario'])) {
            $nombre = trim((string) Arr::get($_POST, 'nombre', ''));
            if ($nombre === '') {
                $errors[] = 'El nombre no puede quedar vacío.';
            } else {
                $user->nombre = $nombre;
                $user->cargo = trim((string) Arr::get($_POST, 'cargo', ''));
                $user->mosca = trim((string) Arr::get($_POST, 'mosca', ''));
                if ($user->save()) {
                    $info[] = 'Los datos se guardaron correctamente.';
                } else {
                    $errors[] = 'Ocurrió un error, vuelva a intentarlo.';
                }
            }
        }
        //cambiar contraseña
        if (isset($_POST['submit-pass'])) {
            $auth = Auth::instance();
            $pass_old = $auth->hash_password($_POST['pass_old']);
            if ($pass_old == $this->user->password) { //verificamos que el password anterior coincida
                if ($_POST['pass_new'] == $_POST['pass_new2']) {
                    $user->password = $auth->hash_password($_POST['pass_new']);
                    $user->save();
                    $info[] = 'Su contraseña fue cambiada correctamente.';
                    //vitacora
                    $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' ' . $this->user->cargo . ' cambio su contrase&ntilde;');
                } else {
                    $errors[] = 'Las contraseñas no coinciden.';
                }
            } else {
                $errors[] = 'La contraseña anterior es incorrecta.';
            }
        }
        // recortar la foto subida (queda en static/fotos/tmp/ hasta recortarla)
        if (isset($_POST['scrop'])) {
            $src = $dir_fotos . 'tmp/' . $user->username . '.jpg';
            $img_r = file_exists($src) ? @imagecreatefromjpeg($src) : FALSE;
            if (!$img_r) {
                $errors[] = 'No se encontró la foto para recortar. Vuelva a subirla.';
            } else {
                $ancho = imagesx($img_r);
                $alto = imagesy($img_r);
                // recorte cuadrado dentro de la imagen; si no se marco nada, el cuadrado central
                $w = (int) round((float) Arr::get($_POST, 'w', 0));
                $x = (int) round((float) Arr::get($_POST, 'x1', 0));
                $y = (int) round((float) Arr::get($_POST, 'y1', 0));
                if ($w < 20) {
                    $w = min($ancho, $alto);
                    $x = (int) (($ancho - $w) / 2);
                    $y = (int) (($alto - $w) / 2);
                }
                $w = min($w, $ancho, $alto);
                $x = max(0, min($x, $ancho - $w));
                $y = max(0, min($y, $alto - $w));
                $lado = 300;
                $dst_r = imagecreatetruecolor($lado, $lado);
                imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $lado, $lado, $w, $w);
                if (file_exists($dir_fotos . $user->username . '.jpg')) {
                    // se conserva la anterior, como hacia subirfoto
                    @rename($dir_fotos . $user->username . '.jpg', $dir_fotos . time() . '_' . $user->username . '.jpg');
                }
                imagejpeg($dst_r, $dir_fotos . $user->username . '.jpg', 90);
                imagedestroy($dst_r);
                imagedestroy($img_r);
                @unlink($src);
                $info[] = 'La foto de perfil se actualizó.';
                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' ' . $this->user->cargo . ' modificó su foto de perfil');
            }
        }

        // DESTINATARIOS
        $mUsers = New Model_Users();
        $destinatarios = $mUsers->destinatarios($user->id);

        // actividad del usuario
        $actividad = DB::query(Database::SELECT, 'SELECT
                (SELECT COUNT(*) FROM documentos WHERE id_user = :u) AS documentos,
                (SELECT COUNT(*) FROM seguimiento WHERE derivado_por = :u) AS derivaciones,
                (SELECT COUNT(*) FROM seguimiento WHERE derivado_a = :u AND estado = 2) AS pendientes,
                (SELECT COUNT(*) FROM seguimiento WHERE derivado_a = :u AND estado = 1) AS por_recibir')
                ->param(':u', (int) $user->id)->execute()->current();

        $oficina = ORM::factory('oficinas', $user->id_oficina);
        $foto_tmp = file_exists($dir_fotos . 'tmp/' . $user->username . '.jpg');

        $this->template->styles = array(
            'static/plugins/dropzone/css/dropzone.css' => 'screen',
            'static/plugins/dropzone/css/basic.css' => 'screen',
            'static/plugins/jscrop/css/jquery.Jcrop.css' => 'screen',
        );
        $this->template->scripts = array(
            'static/plugins/dropzone/dropzone.min.js',
            'static/plugins/jscrop/js/jquery.Jcrop.js',
            'static/js/eModal.min.js',
        );
        $this->template->titulo .= $es_propio ? 'Mi perfil' : 'Perfil de ' . $user->nombre;
        $this->template->content = View::factory('user/profile')
            ->bind('user', $user)
            ->bind('destinatarios', $destinatarios)
            ->bind('errors', $errors)
            ->bind('info', $info)
            ->bind('actividad', $actividad)
            ->bind('oficina', $oficina)
            ->bind('foto_tmp', $foto_tmp)
            ->bind('es_propio', $es_propio);
    }

    /**
     * Recibe la foto (Dropzone) y la deja en static/fotos/tmp/ para recortarla.
     * Solo para el propio usuario, o para otro si quien sube es administrador.
     */
    public function action_subirfoto()
    {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $user = $this->user;
        $idp = (int) Arr::get($_POST, 'idp', 0);
        if ($idp > 0 && $idp !== (int) $this->user->id && (int) $this->user->nivel === 5) {
            $user = ORM::factory('users', $idp);
        }
        $archivo = isset($_FILES['file']) ? $_FILES['file'] : NULL;
        $valido = Validation::factory($_FILES)
            ->rule('file', 'Upload::not_empty')
            ->rule('file', 'Upload::type', array(':value', array('jpg', 'jpeg')))
            ->rule('file', 'Upload::size', array(':value', '5M'));
        // ademas de la extension, el contenido debe ser realmente una imagen JPG
        $es_jpg = $archivo && is_uploaded_file($archivo['tmp_name']) && ($info = @getimagesize($archivo['tmp_name'])) && $info[2] === IMAGETYPE_JPEG;
        if (!$user->loaded() || !$valido->check() || !$es_jpg) {
            $this->response->status(400)->body(json_encode(array('error' => 'Suba una imagen JPG de hasta 5 MB.')));
            return;
        }
        $dir = DOCROOT . 'static/fotos/tmp/';
        $foto = $user->username . '.jpg';
        if (file_exists($dir . $foto)) {
            @rename($dir . $foto, $dir . time() . '_' . $foto);
        }
        if (!Upload::save($archivo, $foto, $dir)) {
            $this->response->status(500)->body(json_encode(array('error' => 'No se pudo guardar la imagen.')));
            return;
        }
        $this->response->body(json_encode(array('ok' => 1)));
    }

    public function action_nuevo()
    {
        $entidades = ORM::factory('entidades')->find_all();
        $options = array();
        foreach ($entidades as $e) {
            $options[$e->id] = $e->entidad;
        }
        $this->template->content = View::factory('admin/nuevo1')
            ->bind('options', $options);
    }

    public function action_create()
    {
        $oficinas = ORM::factory('oficinas')->find_all();
        $options = array('' => 'Seleccione oficina...');
        foreach ($oficinas as $o) {
            $options[$o->id] = $o->oficina;
        }
        //options cargos
        $opCargos = '';
        $cargos = ORM::factory('users')->find_all();
        foreach ($cargos as $c) {
            $opCargos .= '<option value="' . $c->id . '" class="' . $c->id_oficina . '">' . $c->cargo . ' - ' . $c->nombre . '</options>';
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

    public function action_logout()
    {
        $session = Session::instance();
        $usuario_sesion = ORM::factory('sesiones')
            ->where('user_id', '=', $this->user->id)
            ->and_where('session', '=', $session->id())
            ->find();
        if ($usuario_sesion->loaded()) {
            $usuario_sesion->delete();
        }
        $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' <b>' . $this->user->cargo . '</b> salio del sistema');
        Auth::instance()->logout();
        Request::current()->redirect('/login');
    }

    public function action_list($id = '')
    {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)->find_all();
            $this->template->content = View::factory('user/list')->bind('usuarios', $usuarios);
        }
    }

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

    public function action_info()
    {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);
        $oficina = $oficina->oficina;
        $user = $this->user;
        $this->template->title = $this->user->nombre;
        $this->template->titulo .= $this->user->username;
        $entidad = ORM::factory('entidades')->where('id', '=', $this->user->id_entidad)->find();
        $this->template->descripcion = $entidad->entidad;
        $this->template->content = View::factory('user/info')
            ->bind('user', $user)
            ->bind('oficina', $oficina);
    }

    public function action_destinatarios()
    {
        $o_destinos = New Model_Destinatarios();
        $destinatarios = $o_destinos->destinos($this->user->id);
        $this->template->title .= 'Destinatarios';
        $this->template->titulo .= 'Destinatarios';
        $this->template->descripcion .= 'Lista de destinatarios permitidos';
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/style.css' => 'all');
        $this->template->scripts = array(
            'media/js/jquery.tablesorter.min.js',
            'static/js/perfil.js',
            'static/plugins/dropzone/dropzone.min.js',
            'static/plugins/jscrop/js/jquery.Jcrop.js',
            'static/js/eModal.min.js',
        );

        $this->template->content = View::factory('user/destinatarios')
            ->bind('destinatarios', $destinatarios)
            ->bind('user', $this->user);
    }

    public function action_xdes()
    {
        $id_usuario = (int) Arr::get($_GET, 'id_user', 0);
        $id_destino = (int) Arr::get($_GET, 'id_des', 0);
        // solo se quitan destinatarios de la propia lista (o de cualquiera, si es administrador)
        if ($id_usuario !== (int) $this->user->id && (int) $this->user->nivel !== 5) {
            $id_usuario = (int) $this->user->id;
        }
        if ($id_destino > 0 && $id_usuario > 0) {
            $destino = ORM::factory('destinatarios')
                ->where('id_usuario', '=', $id_usuario)
                ->and_where('id_destino', '=', $id_destino)
                ->find();
            if ($destino->loaded()) {
                $destino->delete();
            }
        }
        $this->request->redirect('/user/profile/' . ($id_usuario !== (int) $this->user->id ? $id_usuario : ''));
    }

    public function action_color($color = '')
    {
        if ($color != '') {
            $user = ORM::factory('users', $this->user->id);
            if ($user->loaded()) {
                $user->theme = $color;
                $user->save();
                $this->request->redirect('user/color');
            }
        }
        $this->template->title .= 'Personalizar color';
        $this->template->titulo .= 'Personalizar color';
        $this->template->descripcion .= 'Personalizar el color del sistema';
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

    public function action_organigrama()
    {
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
            $this->lista .= '</ul>';
            $config = array();
            //$config=  ORM::factory('configuracion',1);

            $this->template->styles = array(
                // 'media/css/bootstrap.min.css' => 'all',
                'media/css/jquery.jOrgChart.css' => 'all',);
            $this->template->scripts = array(
                'media/js/jquery.jOrgChart.js',);

            $this->template->descripcion = $entidad->entidad;
            $this->template->title .= ' Organigrama';
            $this->template->titulo = '<v>Organigrama</v>';
            // $this->template->menu=  View::factory('admin/menu');
            $this->template->content = View::factory('oficina/lista')
                ->bind('lista', $this->lista)
                ->bind('entidad', $entidad)
                ->bind('config', $config);
            echo $this->lista;
        }
    }

    public function action_search()
    {

        $this->request->redirect('search/advanced');
    }

    public function listar($id, $oficina, $sigla)
    {
        $h = ORM::factory('oficinas')->where('padre', '=', $id)->count_all();
        //echo '<li>'.$oficina;       
        //$this->lista.='<li class="oficina" style="display:none;">'.HTML::anchor('admin/user/lista/'.$id,$oficina.' <br/> '.$sigla);
        $this->lista .= '<li class="oficina" style="display:none;">' . HTML::anchor('user/oficina/' . $id, $oficina);
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

    public function action_oficina($id = 0)
    {

        $oficina = ORM::factory('oficinas', $id);
        if ($oficina->loaded()) {
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)->find_all();
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
            $this->template->title .= ' ' . $oficina->oficina;
            $this->template->titulo = '<v>' . $oficina->oficina . '</v>';
            $this->template->descripcion = 'Lista de Personal';
            $this->template->content = View::factory('user/personal')
                ->bind('usuarios', $usuarios);
        } else {
            $this->template->content = 'Oficina no encontrada';
        }
    }

    public function action_busquedaAevivienda()
    {
        $this->request->redirect('http://192.168.10.50/consulta/advanced');
    }   

}

?>
