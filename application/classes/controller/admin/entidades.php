<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Entidades extends Controller_AdminTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'admin');
        $oSM = New Model_menus();
        parent::after();
    }

    // lista de oficinas
    public function action_index() {
        $entidades = ORM::factory('entidades')->order_by('entidad')->find_all();
        // cuanto cuelga de cada entidad: es lo que decide si se puede borrar
        $uso = DB::query(Database::SELECT, 'SELECT e.id,
                    (SELECT COUNT(*) FROM oficinas WHERE id_entidad = e.id) AS oficinas,
                    (SELECT COUNT(*) FROM users WHERE id_entidad = e.id) AS usuarios,
                    (SELECT COUNT(*) FROM users WHERE id_entidad = e.id AND habilitado = 1) AS activos,
                    (SELECT COUNT(*) FROM documentos WHERE id_entidad = e.id) AS documentos
                FROM entidades e')
                ->execute()->as_array('id');
        $session = Session::instance();
        $mensaje = $session->get_once('entidad_mensaje', '');
        $error = $session->get_once('entidad_error', '');
        $this->template->titulo .= 'Entidades';
        $this->template->content = View::factory('admin/entidades/lista')
                ->bind('entidades', $entidades)
                ->set('uso', $uso)
                ->bind('mensaje', $mensaje)
                ->bind('error', $error);
    }

    // elimina una entidad solo si no tiene oficinas, usuarios ni documentos asociados
    public function action_eliminar($id = '') {
        $session = Session::instance();
        $entidad = ORM::factory('entidades', $id);
        if ($this->request->method() !== Request::POST || !$entidad->loaded()) {
            $this->request->redirect('/admin/entidades');
        }
        $dependencias = array(
            'oficinas' => 'oficinas',
            'users' => 'usuarios',
            'documentos' => 'documentos',
        );
        $en_uso = array();
        foreach ($dependencias as $tabla => $nombre) {
            $total = DB::select(array(DB::expr('COUNT(*)'), 'total'))
                    ->from($tabla)
                    ->where('id_entidad', '=', $entidad->id)
                    ->execute()
                    ->get('total');
            if ($total > 0) {
                $en_uso[] = "$total $nombre";
            }
        }
        if ($en_uso) {
            $session->set('entidad_error', 'No se puede eliminar la entidad ' . $entidad->sigla . ' porque tiene ' . implode(', ', $en_uso) . ' asociados. Puede desactivarla en su lugar.');
        } else {
            $sigla = $entidad->sigla;
            DB::delete('entidades_oficinas')->where('id_entidad', '=', $entidad->id)->execute();
            $entidad->delete();
            $session->set('entidad_mensaje', 'Se elimino la entidad ' . $sigla . '.');
        }
        $this->request->redirect('/admin/entidades');
    }

    public function action_logo($id) {
        $entidad = ORM::factory('entidades', $id);
        if ($entidad->loaded()) {


            if (isset($_POST['scrop'])) {
                //$this->view->disable();
                $targ_w = $targ_h = 180;
                $targ_w = 240;
                $targ_h = 90;
                $jpeg_quality = 100;

                $src = DOCROOT . 'static/logos/' . $entidad->logo;
                $logo='static/logos/' . $entidad->logo;
                $img_r = imagecreatefrompng($src);
                $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
                imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x1'], $_POST['y1'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);

                //header('Content-type: image/jpeg');                
                imagepng($dst_r, DOCROOT . 'static/logos/' . 1, $jpeg_quality);
                imagedestroy($dst_r);
                // unlink($src);
                // $_POST=array();
            }
            $this->template->styles = array(
                'static/plugins/dropzone/css/dropzone.css' => 'screen',
                'static/plugins/dropzone/css/basic.css' => 'screen',
                'static/css/logo.css' => 'screen'
            );
            $this->template->scripts = array(
                'static/js/logoEntidad.js',
                'static/plugins/dropzone/dropzone.min.js',
                'static/plugins/jscrop/js/jquery.Jcrop.js',
            );
            $this->template->content = View::factory('admin/entidades/logo')
                    ->bind('entidad', $entidad);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_subirlogo() {
        $foto = $_POST['username'] . '.jpg';
        $post = Validation::factory($_FILES)
                ->rule('file', 'Upload::not_empty')
                ->rule('file', 'Upload::type', array(':value', array('jpg','png')));
        // ->rule('archivo', 'Upload::size', array(':value', '20M'));
        //si pasa la validacion guardamamos 
        if (file_exists('static/logos/' . $foto)) {
            rename('static/logos/' . $foto, 'static/logos/' . time() . '_' . $foto);
        }
        if (file_exists('static/fotos/' . $foto)) {
            rename('static/fotos/' . $foto, 'static/logos/' . time() . '_' . $foto);
        }
        $path = 'static/logos/';
        $logo = uniqid() . '.png';
        $filename = upload::save($_FILES['file'], $logo, $path);
        $image = Image::factory($filename);
        $image->resize(240, 240);
        $image->save();
        $entidad = ORM::factory('entidades', $_POST['idp']);
        if ($entidad->loaded()) {
            $entidad->logo = $logo;
            $entidad->save();
        }
        /* if ($this->request->hasFiles() == true) {
          foreach ($this->request->getUploadedFiles() as $file) {
          //echo $file->getName(), " ", $file->getSize(), "\n";
          if (file_exists('tmp/' . $ci . '.jpg')) {
          rename('tmp/' . $ci . '.jpg', 'tmp/' . time() . '_' . $ci . '.jpg');
          }
          if (file_exists('personal/' . $ci . '.jpg')) {
          rename('personal/' . $ci . '.jpg', 'personal/' . time() . '_' . $ci . '.jpg');
          }
          $file->moveTo('tmp/' . $ci . '.jpg');
          $image = new Phalcon\Image\Adapter\GD('tmp/' . $ci . '.jpg');
          $image->resize(500, 500);
          if ($image->save()) {
          echo 'success';
          }
          }
          }
         */
        $_POST = null;
    }

    public function action_lista($id = '') {
        $entidad = ORM::factory('entidades', array('id' => $id));
        if ($entidad->loaded()) {
            $oficinas = $entidad->oficinas->find_all();
            $this->template->content = View::factory('/admin/oficinas')
                    ->bind('oficinas', $oficinas);
        } else {
            $this->template->content = 'Error: No se encontro la entidad';
        }
    }

    public function action_nuevo() {
        $errors = array();
        $mensaje = '';
        if (isset($_POST['entidad'])) {
            //verificamos que la sigla de la entidad no exista ya
            $entidad = ORM::factory('entidades', array('sigla' => $_POST['sigla']));
            if ($entidad->id) {
                $sigla = $_POST['sigla'];
                $errors[] = "Ya existe una entidad con la sigla: $sigla , escriba otra por favor";
            } else {
                $entidad->entidad = Arr::get($_POST, 'entidad');
                $entidad->sigla = Arr::get($_POST, 'sigla');
                $entidad->sigla2 = Arr::get($_POST, 'sigla2');
                $entidad->direccion = Arr::get($_POST, 'direccion');
                $entidad->telefono = Arr::get($_POST, 'telefono');
                $entidad->save();
                $_POST = array();
                $mensaje = $entidad->entidad;
            }
        }

        $this->template->content = View::factory('admin/entidades/nuevo')
                ->bind('errors', $errors)
                ->bind('mensaje', $mensaje);
    }

    public function action_edit($id) {
        $errors = "";
        $mensaje = '';
        //verificamos que la sigla de la entidad no exista ya
        // el id sale de la URL, no del formulario
        $e = ORM::factory('entidades', (int) $id);
        if (!$e->loaded()) {
            $this->request->redirect('/admin/entidades');
        }
        $datos = array(
            'entidad' => $e->entidad, 'sigla' => $e->sigla, 'sigla2' => $e->sigla2,
            'direccion' => $e->direccion, 'telefono' => $e->telefono,
            'pie_1' => $e->pie_1, 'pie_2' => $e->pie_2, 'estado' => (int) $e->estado,
        );
        // cambio de logo desde la misma pantalla de edicion
        if (isset($_POST['subir_logo'])) {
            $archivo = Arr::get($_FILES, 'logo', array());
            if (empty($archivo['name'])) {
                $errors = 'Elija primero un archivo de imagen.';
            } elseif ((int) $archivo['error'] !== UPLOAD_ERR_OK) {
                $errors = 'No se pudo recibir el archivo (puede que sea demasiado grande).';
            } elseif ((int) $archivo['size'] > 2097152) {
                $errors = 'La imagen no debe pasar de 2 MB.';
            } else {
                // se comprueba el contenido, no la extension ni el tipo que dice el navegador:
                // antes se aceptaba cualquier archivo, incluido uno ejecutable
                $info = @getimagesize($archivo['tmp_name']);
                $formatos = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif');
                if (!$info OR !isset($formatos[$info[2]])) {
                    $errors = 'El archivo no es una imagen válida. Use JPG, PNG o GIF.';
                } else {
                    try {
                        $nombre = uniqid() . '.' . $formatos[$info[2]];
                        $ruta = DOCROOT . 'static/logos/';
                        if (!upload::save($archivo, $nombre, $ruta)) {
                            throw new Exception('upload::save devolvio FALSE (permisos de la carpeta o subida no valida)');
                        }
                        $imagen = Image::factory($ruta . $nombre);
                        if ($imagen->width > 480 OR $imagen->height > 480) {
                            $imagen->resize(480, 480);
                            $imagen->save();
                        }
                        $anterior = $e->logo;
                        $e->logo = $nombre;
                        $e->save();
                        // el logo anterior se conserva en el servidor por si hay que volver atras
                        $this->save($this->user->id_entidad, $this->user->id, 'Administrador cambio el logo de <b>' . $e->sigla . '</b> (antes: ' . $anterior . ')');
                        Session::instance()->set('entidad_mensaje_edit', 'Se cambió el logo de ' . $e->sigla . '.');
                        $this->request->redirect('/admin/entidades/edit/' . (int) $e->id);
                    } catch (Exception $ex) {
                        Kohana::$log->add(Log::ERROR, 'logo entidad: ' . $ex->getMessage());
                        $errors = 'No se pudo guardar la imagen.';
                    }
                }
            }
        }

        if ($errors === '' AND isset($_POST['guardar'])) {
            foreach ($datos as $k => $v) {
                if (isset($_POST[$k])) {
                    $datos[$k] = trim($_POST[$k]);
                }
            }
            $datos['sigla'] = strtoupper($datos['sigla']);
            $datos['sigla2'] = strtoupper($datos['sigla2']);
            $datos['estado'] = isset($_POST['estado']) ? 1 : 0;

            if ($datos['entidad'] === '') {
                $errors = 'Escriba el nombre de la entidad.';
            } elseif ($datos['sigla'] === '') {
                $errors = 'Escriba la sigla de la entidad.';
            } elseif (strlen($datos['sigla2']) > 3 AND $datos['sigla2'] !== strtoupper((string) $e->sigla2)) {
                // el limite solo se exige a lo nuevo: hay entidades guardadas con mas caracteres
                // y, si no, no se podria guardar ningun otro cambio de esas entidades
                $errors = 'La sigla abreviada admite 3 caracteres como máximo.';
            } elseif (ORM::factory('entidades')->where('sigla', '=', $datos['sigla'])->where('id', '<>', $e->id)->find()->loaded()) {
                $errors = 'Ya existe otra entidad con la sigla <b>' . HTML::chars($datos['sigla']) . '</b>.';
            } elseif ($datos['estado'] === 0 AND (int) $e->estado === 1) {
                // desactivar una entidad con gente dentro la deja sin poder trabajar
                $activos = DB::query(Database::SELECT, 'SELECT COUNT(*) AS n FROM users WHERE id_entidad = :id AND habilitado = 1')
                        ->param(':id', (int) $e->id)->execute()->get('n');
                if ($activos > 0) {
                    $errors = 'No se desactivó: la entidad todavía tiene ' . $activos . ' usuario' . ($activos == 1 ? '' : 's') . ' activo' . ($activos == 1 ? '' : 's') . '.';
                }
            }

            if ($errors === '') {
                foreach ($datos as $k => $v) {
                    $e->$k = $v;
                }
                $e->save();
                $this->save($this->user->id_entidad, $this->user->id, 'Administrador edito la entidad <b>' . $e->sigla . '</b>');
                Session::instance()->set('entidad_mensaje', 'Se guardaron los cambios de ' . $e->sigla . '.');
                $this->request->redirect('/admin/entidades');
            }
        }

        $uso = DB::query(Database::SELECT, 'SELECT
                    (SELECT COUNT(*) FROM oficinas WHERE id_entidad = :id) AS oficinas,
                    (SELECT COUNT(*) FROM users WHERE id_entidad = :id AND habilitado = 1) AS usuarios,
                    (SELECT COUNT(*) FROM documentos WHERE id_entidad = :id) AS documentos')
                ->param(':id', (int) $e->id)->execute()->current();

        if ($mensaje === '') {
            $mensaje = Session::instance()->get_once('entidad_mensaje_edit', '');
        }

        $this->template->titulo .= 'Editar entidad';
        $this->template->content = View::factory('admin/entidades/edit')
                ->bind('errors', $errors)
                ->set('e', $e)
                ->set('datos', $datos)
                ->set('uso', $uso)
                ->bind('mensaje', $mensaje);
    }

}

?>
